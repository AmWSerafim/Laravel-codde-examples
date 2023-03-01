<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CompanyController extends Controller
{

    public function index(){

        $companies = Company::orderBy('id')->paginate(15);

        $data = [
            "companies" => $companies
        ];

        return view('admin.companies.index')->with($data);
    }

    public function create(){

        return view('admin.companies.create');
    }

    public function store(Request $request){

        //dd($requescccsdfdheghxbxbt);

        $request->validate([
            'title' => 'required',
        ]);

        $data = $request->all();

        $data['slug'] = preg_replace("/\s/", "-", strtolower($data['title']));
        /* here can be placed addition data what should be added to company */
        $data['addition_data'] = json_encode([]);

        Company::create($data);

        return redirect()->route('companies')
            ->with('success', 'Company created successfully.');
    }

    public function show($id){

        $row = Company::find($id);

        $data = [
            'company' => $row
        ];

        return view('admin.companies.show')->with($data);
    }

    public function edit($id){

        $row = Company::find($id);
        $data = [
            'company' => $row
        ];

        return view('admin.companies.edit')->with($data);
    }

    public function switch_to($id){

        Session::put("browsing_from", $id);

        return redirect()->route('companies');

    }

    public function switch_reset(){

        Session::remove('browsing_from');

        return redirect()->route('companies');
    }

    public function update(Request $request, $id){

        $request->validate([
            'title' => 'required',
            'slug' => 'required'
        ]);

        $data = $request->all();

        unset($data['_method']);
        unset($data['_token']);

        Company::whereId($id)->update($data);

       // dd($request);

        return redirect()->route('companies')
            ->with('success', 'Company updated successfully');
    }

    public function destroy($id)
    {
        if(isset($id)) {
            Company::find($id)->delete();

            return redirect()->route('companies')
                ->with('success', 'Company deleted successfully');
        } else {

            return redirect()->route('companies')
                ->with('error', 'Company not deleted');
        }
    }

    public function users($id){

        $company = Company::find($id);
        $users = $company->users()->orderBy('name')->paginate(15);

        foreach($users as $item){
            $role = $item->role()->first();
            $item->role = $role->name;
        }

        $data = [
            'company_id'=> $id,
            'users'     => $users
        ];

        return view('admin.companies.show_users')->with($data);
    }
}
