<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Excel;
use App\Models\Mapping;
use App\Models\NgDinamic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImportController extends ToolsController
{
    public function index()
    {

        $user_id = Auth::id();
        $user_obj = User::find($user_id);

        $custom_tables = new NgDinamic;

        $custom_tables->setTable('asfrdtgs_mappings');

        //dd($custom_tables->get());

        if($this->UserHasRole($user_obj,"master-admin")){
            if(!empty(Session::get('browsing_from'))){
                $company = Company::find(Session::get('browsing_from'));
                $users = $company->users()->get();
                $company_users_array = [];
                foreach($users as $user){
                    $company_users_array[] = $user->id;
                }
                $created_mapings = $custom_tables->whereIn('user_id', $company_users_array)->orderBy('id')->get();
                //$created_mapings = Mapping::whereIn('user_id', $company_users_array)->orderBy('id')->get();
            } else {
                $created_mapings = $custom_tables->get();
                //$created_mapings = Mapping::all();
            }
        } else {
            $company_id = $user_obj->company()->first()->id;
            $company = Company::find($company_id);
            $users = $company->users()->get();
            $company_users_array = [];
            foreach($users as $user){
                $company_users_array[] = $user->id;
            }
            $created_mapings = $custom_tables->whereIn('user_id', $company_users_array)->orderBy('id')->get();
            //$created_mapings = Mapping::whereIn('user_id', $company_users_array)->orderBy('id')->get();
        }

        //$created_mapings = Mapping::all();
        return view('admin.import.index')->with(['mappings' => $created_mapings]);
    }

    public function destroy($id)
    {
        if (isset($id)) {
            Mapping::find($id)->delete();
            return redirect()->route('import')
                ->with('success', 'Mapping deleted successfully');
        } else {

            return redirect()->route('import')
                ->with('error', 'Mapping not deleted');
        }
    }

    public function do_export($id)
    {
        $mapping = Mapping::find($id);
        $mapped_fields = json_decode($mapping->mapped_fields);
        $documents_fields = (array)json_decode($mapping->files_fields);

        //dump($documents_fields);
        //dump($mapped_fields);

        $mapped_fields_names = [];
        $header_row = [];
        foreach ($mapped_fields as $key => $value) {
            //dump($key);
            if($key != 'tools') {
                foreach ($value as $f_key => $f_value) {

                    if ($f_key != 'keep_headers') {
                        if ($f_key != "header_row") {
                            $mapped_fields_names[$key][$f_key] = $documents_fields[$key][$f_value];
                        } else if ($f_key == "header_row") {
                            $header_row[$key] = $f_value + 1;
                        }
                    } else {
                        $selected_headers[$key] = $f_value;
                    }
                }

                $mapped_fields_names[$key] = (object)$mapped_fields_names[$key];
            } else {
                $mapped_fields_names[$key] = (object)$value;
            }
        }
        $mapped_fields_names = (object)$mapped_fields_names;


        //dd($mapped_fields_names);
        //dd($selected_headers);

        $selected_headers_text['payment'] = explode(',', $selected_headers['payment']);
        $selected_headers_text['transfer'] = explode(',', $selected_headers['transfer']);

        return view('admin.import.reimport')->with([
            'mapping'               => $mapped_fields_names,
            'mapping_id'            => $mapping->id,
            'header_row'            => (object)$header_row,
            'selected_headers'      => (object)$selected_headers,
            'selected_headers_text' => $selected_headers_text
        ]);

    }
}
