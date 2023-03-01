<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Excel;
use App\Models\Mapping;
use App\Models\MappingJob;
use App\Models\MappingResult;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Exports\MappingExport;

class MappingController extends ToolsController
{
    public function index()
    {
        $user_id = Auth::id();
        $user_obj = User::find($user_id);
        $allow_mapping = TRUE;
        if($this->UserHasRole($user_obj,"master-admin")){
            if(!empty(Session::get('browsing_from'))){
                $company_id = Session::get('browsing_from');
                //$company = Company::find(Session::get('browsing_from'));
                //$users = $company->users()->get();
                //$company_users_array = [];
                //foreach($users as $user){
                //    $company_users_array[] = $user->id;
                //}
                //$created_mapings = Mapping::whereIn('user_id', $company_users_array)->orderBy('id')->get();
                $created_mappings = Mapping::where('creator_company_id', $company_id)->orderBy('id')->get();
            } else {
                $allow_mapping = FALSE;
                $created_mappings = Mapping::all();
            }
        } else {
            $company_id = $user_obj->company()->first()->id;
            //$company = Company::find($company_id);
            //$users = $company->users()->get();
            //$company_users_array = [];
            //foreach($users as $user){
             //   $company_users_array[] = $user->id;
            //}
            //$created_mapings = Mapping::whereIn('user_id', $company_users_array)->orderBy('id')->get();
            $created_mappings = Mapping::where('creator_company_id', $company_id)->orderBy('id')->get();
        }

        return view('admin.mapping.index')->with(['mappings' => $created_mappings, 'allow_mapping' => $allow_mapping]);
    }

    public function import()
    {
        $user_id = Auth::id();
        $user_obj = User::find($user_id);
        if($this->UserHasRole($user_obj,"master-admin")) {
            if (!empty(Session::get('browsing_from'))) {
                return view('admin.mapping.import');
            } else {
                return redirect()->route('mapping');
            }
        } else {
            return view('admin.mapping.import');
        }
    }

    public function create(Request $request)
    {
        $request->validate([
            'payment-file' => 'required|max:2048',
            'transfer-file' => 'required|max:2048',
            'payment-file-headings' => 'required',
            'transfer-file-headings' => 'required'
        ]);
        if ($request->file('payment-file')) {
            $payment_path = $this->saveFiles($request->file('payment-file'));
        }
        if ($request->file('transfer-file')) {
            $transfer_path = $this->saveFiles($request->file('transfer-file'));
        }
        $payment_headers_row = $request->input('payment-file-headings')-1;
        $transfer_headers_row = $request->input('transfer-file-headings')-1;

        $payment_headers = $this->getDataFromFile($payment_path, "HEADERS", 1, $payment_headers_row);
        $transfer_headers = $this->getDataFromFile($transfer_path, "HEADERS", 1, $transfer_headers_row);
        $data = [
            'payment_file' => $payment_path,
            'payment_headers' => $payment_headers,
            'payment_file_header_row' => $payment_headers_row,
            'transfer_file' => $transfer_path,
            'transfer_headers' => $transfer_headers,
            'transfer_file_header_row' => $transfer_headers_row
        ];
        return response()->json($data);
    }

    public function preview(Request $request)
    {

        if ($request->input('type') == 'new_preview') {
            $is_new = true;
        } else if ($request->input('type') == 'mapping_preview') {
            $is_new = false;
        } else {
            die("Unpredictable value");
        }

        if ($is_new) {
            $request->validate([
                'mapping-name' => 'required|unique:mappings,name',
                'payment-transaction-code' => 'required',
                'transfer-transaction-code' => 'required',
                'payment-amount' => 'required',
                'transfer-amount' => 'required',
                'payment-file-headings' => 'required',
                'transfer-file-headings' => 'required',
            ]);

            $payment_headers_row = $request->input('payment-file-headings');
            $transfer_headers_row = $request->input('transfer-file-headings');

            $payment_array = $this->getDataFromFile(
                $request->input('payment-file-link'),
                "ALL",
                -1,
                $payment_headers_row+1,
                $payment_headers_row
            );
            $payment_headers = $this->getDataFromFile($request->input('payment-file-link'), "HEADERS", 1, $payment_headers_row);

            //dump($payment_headers);

            $transfer_array = $this->getDataFromFile(
                $request->input('transfer-file-link'),
                "ALL",
                -1,
                $transfer_headers_row+1,
                $transfer_headers_row
            );
            $transfer_headers = $this->getDataFromFile($request->input('transfer-file-link'), "HEADERS", 1, $transfer_headers_row);

            $replaced_transfer_array = $this->replaceTransferId($transfer_array, $request->input('transfer-transaction-code'));

            $left_headers = $request->input('payment-headers-selector_headers');
            $right_headers = $request->input('transfer-headers-selector_headers');

            foreach($left_headers as $key => $value){
                $left_headers[$key] = "left_".$this->formatKeys($value);
            }
            foreach($right_headers as $key => $value){
                $right_headers[$key] = "right_".$this->formatKeys($value);
            }
            $keep_headers_array = array_merge($left_headers, $right_headers);

            $result_array = $this->fullMergeArrays(
                $payment_array,
                $payment_headers,
                $replaced_transfer_array,
                $transfer_headers,
                $request->input('payment-transaction-code'),
                $request->input('transfer-transaction-code'),
                $request->input('payment-amount'),
                $request->input('transfer-amount')
            );

            $tmp[] = $result_array['headers'];
            $headers_array = $this->removeUnneededData2D($tmp, $keep_headers_array);
            $headers_array = $headers_array[0];

            if(!empty($request->input('separate-column')) && !empty($request->input('separate-values'))){

                $separated_data_array_result = $this->separateResult(
                    $result_array['data'],
                    "left_".$this->formatKeys($payment_headers[$request->input('separate-column')]),
                    explode(",", $request->input('separate-values'))
                );

                $separated_data_array_result['base'] = $this->removeUnneededData2D($separated_data_array_result['base'], $keep_headers_array);
                $separated_data_array_result['separated'] = $this->removeUnneededData2D($separated_data_array_result['separated'], $keep_headers_array);
            } else {

                $separated_data_array_result['base'] = $this->removeUnneededData2D($result_array['data'], $keep_headers_array);
                $separated_data_array_result['separated'] = [[]];
            }

            $cols_count = count($headers_array);

            $data = [
                'request_data' => (array)$request->all(),
                'total_headers' => $headers_array,
                'result' => $separated_data_array_result['base'],//$data_array,
                'addition_result' => $separated_data_array_result['separated'],
                'cols_count' => $cols_count,
            ];

            //die();

        }
        else {

            $request->validate([
                'payment-file' => 'required|max:2048',
                'transfer-file' => 'required|max:2048',
            ]);

            $mapping = Mapping::find($request->input('mapping_id'));

            $mapped_fileds = (array)json_decode($mapping->mapped_fields);

            if ($request->file('payment-file')) {
                $payment_path = $this->saveFiles($request->file('payment-file'));
            }
            if ($request->file('transfer-file')) {
                $transfer_path = $this->saveFiles($request->file('transfer-file'));
            }

            $payment_headers = $this->getDataFromFile($payment_path, "HEADERS", 1, $mapped_fileds['payment']->header_row);
            $transfer_headers = $this->getDataFromFile($transfer_path, "HEADERS", 1, $mapped_fileds['transfer']->header_row);

            $documents_fields = (array)json_decode($mapping->files_fields);
            $documents_check = FALSE;
            if ($payment_headers === (array)$documents_fields['payment']) {
                $documents_check = TRUE;
            } else {
                $documents_check = FALSE;
            }
            if ($documents_check) {
                if ($transfer_headers === (array)$documents_fields['transfer']) {
                    $documents_check = TRUE;
                } else {
                    $documents_check = FALSE;
                }
            }
            if ($documents_check) {

                $payment_array = $this->getDataFromFile(
                    $payment_path,
                    "ALL",
                    -1,
                    $mapped_fileds['payment']->header_row+1,
                    $mapped_fileds['payment']->header_row
                );

                $transfer_array = $this->getDataFromFile(
                    $transfer_path,
                    "ALL",
                    -1,
                    $mapped_fileds['transfer']->header_row+1,
                    $mapped_fileds['transfer']->header_row
                );

                $left_headers = explode(",", $mapped_fileds['payment']->keep_headers);
                $right_headers = explode(",", $mapped_fileds['transfer']->keep_headers);

                foreach($left_headers as $key => $value){
                    $left_headers[$key] = "left_".$this->formatKeys($value);
                }
                foreach($right_headers as $key => $value){
                    $right_headers[$key] = "right_".$this->formatKeys($value);
                }
                $keep_headers_array = array_merge($left_headers, $right_headers);

                $replaced_transfer_array = $this->replaceTransferId($transfer_array, $mapped_fileds['transfer']->transaction_code);

                $result_array = $this->fullMergeArrays(
                    $payment_array,
                    $payment_headers,
                    $replaced_transfer_array,
                    $transfer_headers,
                    $mapped_fileds['payment']->transaction_code,
                    $mapped_fileds['transfer']->transaction_code,
                    $mapped_fileds['payment']->amount,
                    $mapped_fileds['transfer']->amount
                );

                $tmp[] = $result_array['headers'];
                $headers_array = $this->removeUnneededData2D($tmp, $keep_headers_array);
                $headers_array = $headers_array[0];

                if(!empty($mapped_fileds['tools']->separate_column) && !empty($mapped_fileds['tools']->separate_value)){

                    $separated_data_array_result = $this->separateResult(
                        $result_array['data'],
                        "left_".$this->formatKeys($payment_headers[$mapped_fileds['tools']->separate_column]),
                        explode(",", $mapped_fileds['tools']->separate_value)
                    );

                    $separated_data_array_result['base'] = $this->removeUnneededData2D($separated_data_array_result['base'], $keep_headers_array);
                    $separated_data_array_result['separated'] = $this->removeUnneededData2D($separated_data_array_result['separated'], $keep_headers_array);
                } else {

                    $separated_data_array_result['base'] = $this->removeUnneededData2D($result_array['data'], $keep_headers_array);
                    $separated_data_array_result['separated'] = [[]];
                }

                $cols_count = count($headers_array);

                $request_data = [
                    "mapping-name"              => $mapping->name,
                    "mapping-id"                => $request->input('mapping_id'),
                    "separate-column"           => $mapped_fileds['tools']->separate_column,
                    "separate-values"           => $mapped_fileds['tools']->separate_value,
                    "payment-amount"            => $mapped_fileds['payment']->amount,
                    "payment-file-link"         => $payment_path,
                    "payment-file-headings"     => $mapped_fileds['payment']->header_row,
                    "payment-transaction-code"  => $mapped_fileds['payment']->transaction_code,
                    "payment-keep-headers"      => $mapped_fileds['payment']->keep_headers,
                    "transfer-amount"           => $mapped_fileds['transfer']->amount,
                    "transfer-file-link"        => $transfer_path,
                    "transfer-file-headings"    => $mapped_fileds['transfer']->header_row,
                    "transfer-transaction-code" => $mapped_fileds['transfer']->transaction_code,
                    "transfer-keep-headers"     => $mapped_fileds['transfer']->keep_headers,
                    "type"                      => "mapping_preview"
                ];

                $data = [
                    'request_data' => $request_data,
                    'total_headers' => $headers_array,
                    'result' => $separated_data_array_result['base'],//$data_array,
                    'addition_result' => $separated_data_array_result['separated'],
                    'cols_count' => $cols_count,
                ];
            } else {
                $data = [
                    'error' => 'Uploaded files not match original fies'
                ];
            }
        }

        return response()->json($data);
    }

    public function generate_report(Request $request)
    {

        //dump($_POST);
        if ($request->input('type') == 'new_preview') {
            $is_new = true;
        } else if ($request->input('type') == 'mapping_preview') {
            $is_new = false;
        } else {
            die("Unpredictable value");
        }
        $validate_array = [
            'payment-file-link' => 'required',
            'transfer-file-link' => 'required',
            'payment-transaction-code' => 'required',
            'transfer-transaction-code' => 'required',
            'payment-amount' => 'required',
            'transfer-amount' => 'required',
            'payment-file-headings' => 'required',
            'transfer-file-headings' => 'required',
        ];
        if ($is_new) {
            $validate_array['mapping-name'] = 'required|unique:mappings,name';
        }
        $payment_headers_row = $request->input('payment-file-headings');
        $transfer_headers_row = $request->input('transfer-file-headings');
        $payment_array = $this->getDataFromFile(
            $request->input('payment-file-link'),
            "ALL",
            -1,
            $payment_headers_row + 1,
            $payment_headers_row
        );
        $payment_headers = $this->getDataFromFile($request->input('payment-file-link'), "HEADERS", 1, $payment_headers_row);
        $transfer_array = $this->getDataFromFile(
            $request->input('transfer-file-link'),
            "ALL",
            -1,
            $transfer_headers_row + 1,
            $transfer_headers_row
        );
        $transfer_headers = $this->getDataFromFile($request->input('transfer-file-link'), "HEADERS", 1, $transfer_headers_row);
        $replaced_transfer_array = $this->replaceTransferId($transfer_array, $request->input('transfer-transaction-code'));
        //dump($request->input('payment-keep-columns'));
        //dump(explode(",", $request->input('payment-keep-columns')));
        $left_headers = explode(",", $request->input('payment-keep-columns'));
        $right_headers = explode(",", $request->input('transfer-keep-columns'));
        foreach ($left_headers as $key => $value) {
            $left_headers[$key] = "left_" . $this->formatKeys($value);
        }
        foreach ($right_headers as $key => $value) {
            $right_headers[$key] = "right_" . $this->formatKeys($value);
        }
        $keep_headers_array = array_merge($left_headers, $right_headers);

        $result_array = $this->fullMergeArrays(
            $payment_array,
            $payment_headers,
            $replaced_transfer_array,
            $transfer_headers,
            $request->input('payment-transaction-code'),
            $request->input('transfer-transaction-code'),
            $request->input('payment-amount'),
            $request->input('transfer-amount')
        );

        $tmp[] = $result_array['headers'];
        $headers_array = $this->removeUnneededData2D($tmp, $keep_headers_array);
        $headers_array = $headers_array[0];

        if(!empty($request->input('separate-column')) && !empty($request->input('separate-values'))){

            $separated_data_array_result = $this->separateResult(
                $result_array['data'],
                "left_".$this->formatKeys($payment_headers[$request->input('separate-column')]),
                explode(",", $request->input('separate-values'))
            );

            //dump($separated_data_array_result);

            $separated_data_array_result['base'] = $this->removeUnneededData2D($separated_data_array_result['base'], $keep_headers_array);
            $separated_data_array_result['separated'] = $this->removeUnneededData2D($separated_data_array_result['separated'], $keep_headers_array);
        } else {

            $separated_data_array_result['base'] = $this->removeUnneededData2D($result_array['data'], $keep_headers_array);
            $separated_data_array_result['separated'] = [[]];
        }

        //$data_array = $this->removeUnneededData2D($result_array['data'], $keep_headers_array);
        $cols_count = count($headers_array);

        $data = [
            'total_headers' => $headers_array,
            //'result' => $data_array,
            'result' => $separated_data_array_result['base'],
            'addition_result' => $separated_data_array_result['separated'],
            'cols_count' => $cols_count,
            'mapping_name' => $request->input('mapping-name')
        ];

        $mapping_id = $request->input('mapping-id');

        //dump($headers_array);

        /* adding base data */
        $export_array[] = $headers_array;
        foreach ($separated_data_array_result['base'] as $row){
            array_push($export_array, $row);
        }
        /* adding empty rows */
        $tmp_array = [];
        for($i = 0; $i < 5; $i++){
            if($i == 0) {
                for ($j = 0; $j < $cols_count; $j++) {
                    $tmp_array[] = "";
                }
            }
            array_push($export_array, $tmp_array);
        }
        /* adding addition data */
        $export_array[] = $headers_array;
        foreach ($separated_data_array_result['separated'] as $row){
            array_push($export_array, $row);
        }


        $reformatted_fields_keys = [];
        foreach($result_array['headers'] as $key => $value){
            if($value == $payment_headers[$request->input('payment-amount')]){
                $reformatted_fields_keys['payment'] = $key;
            }
            if($value == $transfer_headers[$request->input('transfer-amount')]){
                $reformatted_fields_keys['transfer'] = $key;
            }
        }

        $billed_total = [];
        $billed_total['base'] = $this->calculateColumnTotal($separated_data_array_result['base'], $reformatted_fields_keys['payment']);
        $billed_total['separated'] = $this->calculateColumnTotal($separated_data_array_result['separated'], $reformatted_fields_keys['payment']);

        $received_total = [];
        $received_total['base'] = $this->calculateColumnTotal($separated_data_array_result['base'], $reformatted_fields_keys['transfer']);
        $received_total['separated'] = $this->calculateColumnTotal($separated_data_array_result['separated'], $reformatted_fields_keys['transfer']);

        $difference_total = [];
        $difference_total['base'] = $this->reformatAmount($billed_total['base'] + $received_total['base']);
        $difference_total['separated'] = $this->reformatAmount($billed_total['separated'] + $received_total['separated']);

        $store_user_id = Auth::id();
        $browsing_from = Session::get("browsing_from");
        if(isset($browsing_from)){
            $store_company_id = $browsing_from;
        } else {
            $store_user_obj = User::find($store_user_id);
            $store_company_id = $store_user_obj->company()->first()->id;
        }


        $mapping_result_data = [
            'mapping_id'        => $mapping_id,
            'billed_document'   => $request->input('payment-file-link'),
            'received_document' => $request->input('transfer-file-link'),
            'result_file'       => "",
            'total_billed'      => json_encode($billed_total),
            'total_received'    => json_encode($received_total),
            'total_difference'  => json_encode($difference_total),
            'mapping_result'    => json_encode($export_array),
            'creator_company_id'=> $store_company_id
        ];

        $row_obj = MappingResult::create($mapping_result_data);
        $mapping_result_id = $row_obj->id;

        $user_id = Auth::id();
        $mapping_job_data = [
            'user_id'           => $user_id,
            'mapping_result_id' => $mapping_result_id,
            'creator_company_id'=> $store_company_id
        ];

        MappingJob::create($mapping_job_data);

        $date = new \DateTime();
        $user_id = Auth::id();
        $formatted_date = $date->format("_d_m_Y_H_i");
        $result_file_name = 'export_'.$user_id.$formatted_date.'.csv';

        $export = new MappingExport($export_array);
        Excel::store($export, 'public/'.$result_file_name);

        MappingResult::where('id', $mapping_result_id)->update(['result_file' => $result_file_name]);

        $data['file_path'] = $result_file_name;

        return view('admin.mapping.report')->with($data);
    }

    public function report(Request $request)
    {
        $validate_array = [
            'mapping-name' => 'required|unique:mappings,name',
            'payment-file-link' => 'required',
            'transfer-file-link' => 'required',
            'payment-transaction-code' => 'required',
            'transfer-transaction-code' => 'required',
            'payment-amount' => 'required',
            'transfer-amount' => 'required',
            'payment-file-headings' => 'required',
            'transfer-file-headings' => 'required',
        ];
        $request->validate($validate_array);

        $db_headers = [];
        $db_mapping = [
            'payment' => [
                'transaction_code'  => $request->input('payment-transaction-code'),
                'amount'            => $request->input('payment-amount'),
                'header_row'        => $request->input('payment-file-headings'),
                'keep_headers'      => $request->input('payment-keep-columns'),
            ],
            'transfer' => [
                'transaction_code'  => $request->input('transfer-transaction-code'),
                'amount'            => $request->input('transfer-amount'),
                'header_row'        => $request->input('transfer-file-headings'),
                'keep_headers'      => $request->input('transfer-keep-columns')
            ],
            'tools' => [
                'separate_column'   => $request->input('separate-column'),
                'separate_value'    => $request->input('separate-values'),
                'payment_file_link' => $request->input('payment-file-link'),
                'transfer_file_link'=> $request->input('transfer-file-link'),
            ]
        ];

        $payment_headers_row = $request->input('payment-file-headings');
        $payment_headers = $this->getDataFromFile($request->input('payment-file-link'), "HEADERS", 1, $payment_headers_row);
        $db_headers['payment'] = $payment_headers;

        $transfer_headers_row = $request->input('transfer-file-headings');
        $transfer_headers = $this->getDataFromFile($request->input('transfer-file-link'), "HEADERS", 1, $transfer_headers_row);
        $db_headers['transfer'] = $transfer_headers;

        $user_id = Auth::id();

        $browsing_from = Session::get("browsing_from");
        if(isset($browsing_from)){
            $store_company_id = $browsing_from;
        } else {
            $store_user_obj = User::find($user_id);
            $store_company_id = $store_user_obj->company()->first()->id;
        }
        //$store_user_obj = User::find($user_id);
        //$store_company_id = $store_user_obj->company()->first()->id;

        $data = [
            'name'              => $request->input('mapping-name'),
            'slug'              => preg_replace("/\s/", "-", $request->input('mapping-name')),
            'files_fields'      => json_encode($db_headers),
            'mapped_fields'     => json_encode($db_mapping),
            'user_id'           => $user_id,
            'creator_company_id'=> $store_company_id
        ];

        $db_row = Mapping::create($data);
        $mapping_id = $db_row->id;

        if($mapping_id){
            return redirect()->route('mapping')
            ->with('success', 'Mapping saved successfully');
        } else {
            return redirect()->route('mapping')
            ->with('error', 'Mapping not saved because of error in application');
        }
    }

    public function download($path)
    {
        $file = storage_path(). "/app/public/".$path;
        return response()->download($file);
    }

    public function destroy($id)
    {
        if (isset($id)) {
            Mapping::find($id)->delete();
            return redirect()->route('mapping')
                ->with('success', 'Mapping deleted successfully');
        } else {

            return redirect()->route('mapping')
                ->with('error', 'Mapping not deleted');
        }
    }

    public function reimport($id)
    {

        $mapping = Mapping::find($id);
        $mapped_fields = json_decode($mapping->mapped_fields);
        $documents_fields = (array)json_decode($mapping->files_fields);
        $mapped_fields_names = [];
        foreach ($mapped_fields as $key => $value) {
            foreach ($value as $f_key => $f_value) {
                $mapped_fields_names[$key][$f_key] = $documents_fields[$key][$f_value];
            }
            $mapped_fields_names[$key] = (object)$mapped_fields_names[$key];
        }
        $mapped_fields_names = (object)$mapped_fields_names;
        return view('admin.mapping.reimport')->with([
            'mapping' => $mapped_fields_names,
            'mapping_id' => $mapping->id
        ]);
    }

    public function ready_exports(){

        $user_id = Auth::id();
        $result = MappingJob::select(
                'mapping_jobs.id',
                'mappings.name',
                'mapping_results.total_billed',
                'mapping_results.total_received',
                'mapping_results.total_difference',
                'mapping_results.result_file',
                'mapping_results.id as result_id',
                'mapping_jobs.created_at'
            )
            ->join('mapping_results', 'mapping_results.id', '=', 'mapping_jobs.mapping_result_id')
            ->join('mappings', 'mapping_results.mapping_id', '=', 'mappings.id')
            ->where('mapping_jobs.user_id', $user_id)
            ->get();

        return view('admin.mapping.ready_exports')->with(['mappings' => $result]);
    }

    public function export_preview($mapping_result_id){

        $user_id = Auth::id();
        $result = MappingResult::select('mapping_results.*')
            ->join('mapping_jobs', 'mapping_results.id', '=', 'mapping_jobs.mapping_result_id')
            ->where('mapping_results.id', $mapping_result_id)
            ->where('mapping_jobs.user_id', $user_id)
            ->get();

        $result = $result[0];

        $result_data = json_decode($result->mapping_result);
        $result_headers = $result_data[0];
        unset($result_data[0]);

        $data = [
            'result_headers'    => $result_headers,
            'result_data'       => $result_data,
            'cols_count'        => count($result_headers)-1,
            'billed_document'   => $result->billed_document,
            'received_document' => $result->received_document,
            'result_file'       => $result->result_file
        ];

        return view('admin.mapping.export_preview')->with($data);

    }
}
