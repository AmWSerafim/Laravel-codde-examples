<?php

namespace App\Http\Controllers;

use Excel;
use App\Models\User;
use App\Models\Company;
use App\Models\MappingJob;
use App\Models\MappingResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ExportController extends ToolsController
{
    public function index(){

        $user_id = Auth::id();
        $prepared_company_id = [];

        if($this->CheckUserRole($user_id, 'master-admin')){
            if(!empty(Session::get('browsing_from'))){
                $prepared_company_id[] = Session::get('browsing_from');
                /*
                $company = Company::find(Session::get('browsing_from'));
                $users = $company->users()->get();
                $prepared_users_array = [];
                foreach($users as $user){
                    $prepared_users_array[] = $user->id;
                }*/
            } else {
                $conpmanies = Company::all();
                foreach($conpmanies as $item){
                    $prepared_company_id[] = $item->id;
                }
                /*
                $user_ids = User::all();

                $prepared_users_array = [];
                foreach($user_ids as $key => $value){
                    $prepared_users_array[$key] = $value->id;
                }*/
            }
        } else {
            $user_obj = User::find($user_id);
            $company_id = $user_obj->company()->first()->id;
            $prepared_company_id[] = $company_id;
            /*
            $company_obj = Company::find($company_id);
            $user_ids = $company_obj->users()->get();

            $prepared_users_array = [];
            foreach($user_ids as $key => $value){
                $prepared_users_array[$key] = $value->id;
            }
            */
        }

        //dump($prepared_users_array);

        $result = MappingJob::select(
            'mapping_jobs.id',
            'mappings.name',
            'mapping_results.total_billed',
            'mapping_results.total_received',
            'mapping_results.total_difference',
            'mapping_results.result_file',
            'mapping_results.id as result_id',
            'mapping_jobs.created_at',
            'users.name as user_name',
            'users.email as user_email'
        )
            ->join('mapping_results', 'mapping_results.id', '=', 'mapping_jobs.mapping_result_id')
            ->leftJoin('mappings', 'mapping_results.mapping_id', '=', 'mappings.id')
            ->leftJoin('users', 'users.id', '=', 'mapping_jobs.user_id')
            ->whereIntegerInRaw('mapping_jobs.creator_company_id', $prepared_company_id)
            //->toSql();
            ->get();

        //dump($result);
        //dd();
        $rebuilded_result = [];
        $counter = 0;
        foreach($result as $item){
            //dump($item);
            $billed = (array)json_decode($item->total_billed);
            $received = (array)json_decode($item->total_received);
            $difference = (array)json_decode($item->total_difference);

            $rebuilded_result[$counter]['id'] = $item->id;
            $rebuilded_result[$counter]['name'] = $item->name;
            $rebuilded_result[$counter]['user_name'] = $item->user_name;
            $rebuilded_result[$counter]['user_email'] = $item->user_email;
            $rebuilded_result[$counter]['billed'] = $billed['base'];
            $rebuilded_result[$counter]['received'] = $received['base'];
            $rebuilded_result[$counter]['difference'] = $difference['base'];
            $rebuilded_result[$counter]['addition_billed'] = $billed['separated'];
            $rebuilded_result[$counter]['addition_received'] = $received['separated'];
            $rebuilded_result[$counter]['addition_difference'] = $difference['separated'];
            $rebuilded_result[$counter]['created_at'] = date_format($item->created_at, 'M jS Y');
            $rebuilded_result[$counter]['result_id'] = $item->result_id;
            $rebuilded_result[$counter]['result_file'] = $item->result_file;

            $counter++;
        }
        //die();
        return view('admin.export.index')->with(['mappings' => $rebuilded_result]);
    }

    public function preview($mapping_result_id){

        $company_ids_array = [];

        $user_id = Auth::id();
        $user_obj = User::find($user_id);

        if($this->UserHasRole($user_obj,"master-admin")){
            if(!empty(Session::get('browsing_from'))){
                $company_ids_array[] = Session::get('browsing_from');
            } else {
                $companies = Company::all();
                foreach($companies as $item){
                    $company_ids_array[] = $item->id;
                }
            }
        } else {
            $company_ids_array[] = $user_obj->company()->first()->id;
        }

        $result = MappingResult::select('mapping_results.*')
            ->join('mapping_jobs', 'mapping_results.id', '=', 'mapping_jobs.mapping_result_id')
            ->where('mapping_results.id', $mapping_result_id)
            ->whereIn('mapping_jobs.creator_company_id', $company_ids_array)
            ->get();

        if(!isset($result[0])){
            return redirect()->route('import-history');
        }

        $result = $result[0];

        $result_data = json_decode($result->mapping_result);
        $result_data_array = (array)$result_data[0];
        $result_headers = $result_data_array;
        unset($result_data[0]);

        $cols_count = count($result_headers);

        $splited_array = [];
        $trigger = FALSE;
        foreach($result_data as $row){
            $counter = 0;
            foreach($row as $key => $value){
                if(empty($value)){
                    $counter++;
                }
            }
            if($counter == $cols_count){
                $trigger = TRUE;
            }

            if(!$trigger && $counter != $cols_count){
                $splited_array['base'][] = $row;
            } else if($trigger && $counter != $cols_count){
                if(!empty($row)) {
                    $splited_array['separated'][] = $row;
                }
            }
        }

        unset($splited_array['separated'][0]);

        $data = [
            'result_headers'    => $result_headers,
            'result_data'       => $splited_array['base'],//$result_data,
            'addition_data'     => $splited_array['separated'],
            'cols_count'        => $cols_count,//count($result_headers)-1,
            'billed_document'   => $result->billed_document,
            'received_document' => $result->received_document,
            'result_file'       => $result->result_file
        ];

        return view('admin.export.preview')->with($data);

    }
}
