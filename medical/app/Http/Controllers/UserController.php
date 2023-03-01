<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Mail;


class UserController extends ToolsController
{

    public function index(){

        $user_id = Auth::id();

        $user_obj = User::find($user_id);

        if($this->UserHasRole($user_obj,"master-admin")){
            if(!empty(Session::get('browsing_from'))){
                $company = Company::find(Session::get('browsing_from'));
                $users = $company->users()->orderBy('name')->paginate(15);
            } else {
                $users = User::orderBy('name', 'asc')->paginate(15);
            }
        } else {
            $company_id = $user_obj->company()->first()->id;
            $company = Company::find($company_id);
            $users = $company->users()->orderBy('name')->paginate(15);
        }

        foreach($users as $item){
            $role = $item->role()->first();
            if($role) {
                $item->role = $role->name;
            } else {
                $item->role = "";
            }
        }

        $data = [
            "users" => $users,
            'i'     => (request()->input('page', 1) - 1) * 15
        ];

        //dump(Session::get('browsing_from'));

        return view('admin.users.index')->with($data);
    }

    public function create($id){

        $user_id = Auth::id();

        if($this->CheckUserRole($user_id, 'master-admin')) {
            $companies = Company::all();
            $roles = Role::all();

            //dump($companies);
            //dump($roles);

        } else {

            $user_obj = User::find($user_id);
            //dd($user_obj);
            //dd($user_obj->company()->first());
            if(isset($user_obj->company()->first()->id)) {
                $company_id = $user_obj->company()->first()->id;

                $companies = Company::all();
                $companies = $this->FilterResults($companies, 'id', $company_id);
                $roles = Role::all();
                $roles = $this->FilterResults($roles, 'slug', 'master-admin', 'remove');

            } else {
                $companies = Company::all();
                $roles = Role::all();
            }


        }

        $this->FilterResults($companies, '', '');

        //dump($companies);
        //dump($roles);
        //dd();

        $data = [
            'companies' => $companies,
            'company_id'=> $id,
            'roles'     => $roles
        ];

        return view('admin.users.create')->with($data);
    }

    public function store(Request $request){

        $request->validate([
            'name' => 'required',
            'email' => ['required', 'email', 'unique:users'],
            'company_id' => 'required'
        ]);

        $rand_pass = $this->GenerateRandomRow();
        $password = Hash::make($rand_pass);
        //dump($rand_pass);

        $user_data = [
            'name'      => $request->input('name'),
            'email'     => $request->input('email'),
            'password'  => $password
        ];

        //dump($selected_role);
        //dump($permissions);

        $user_id = User::create($user_data)->id;

        $selected_company = Company::where('id', $request->input('company_id'))->first();
        $created_user = User::where('id', $user_id)->first();

        if($selected_company) {
            $selected_company->users()->attach($created_user);
        }

        $selected_role = Role::find($request->input('role_id'));
        $permissions = $selected_role->permissions()->get();

        $created_user->roles()->attach($selected_role);
        foreach($permissions as $permission){
            $created_user->permissions()->attach($permission);
        }

        $mail_data = [
            'email'     => $request->input('email'),
            'login'     => $request->input('email'),
            'password'  => $rand_pass
        ];
/*
        $to = $mail_data['email'];
        $subject = "Jed-medical user registration";

        $message  = "<div>";
        $message .= "<p>Login: ".$mail_data['login']."<p>";
        $message .= "<p>Password: ".$mail_data['password']."<p>";
        $message .= "</div>";

        //$message = "Test text";

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        //$headers .= 'From: Jed-medial admin <info@jedmedical.com>'. "\r\n";

        $success = mail($to, $subject, $message, $headers);
        if($success){
            dd("mail was sent");
        } else {
            //$errorMessage = error_get_last()['message'];
            dd(error_get_last());
        }
*/

        Mail::send('emails.user_register', $mail_data, function ($m) use ($request) {
            $m->from('serafim32167@gmail.com', 'Application admin');
            $m->to($request->input('email'), $request->input('name'))->subject('User created');
        });

        if($request->input('company_id') != 0) {
            return redirect()->route('company.users', $request->input('company_id'))
                ->with('success', 'User created successfully.');
        } else {
            return redirect()->route('users')
                ->with('success', 'User created successfully.');
        }
    }

    public function show($id){

        $row = Company::find($id);

        $data = [
            'company' => $row
        ];

        return view('admin.companies.show')->with($data);
    }

    public function edit($id){

        $user = User::find($id);
        $company = $user->company()->first();
        if(!$company){
            $tmp_array = ['id' => 0];
            $company = (object)$tmp_array;
        }
        $role = $user->role()->first();
        if(!$role){
            $tmp_array = ['id' => 0];
            $role = (object)$tmp_array;
        }

        $error = "";
        if(Session::get('custom_error')){
            $error = Session::get('custom_error');
            Session::remove('custom_error');
        }

        if($this->CheckUserRole($id, 'master-admin1')) {
            $companies = Company::all();
            $roles = Role::all();
        } else {
            if(isset($user->company()->first()->id)) {
                $company_id = $user->company()->first()->id;

                $companies = Company::all();
                $companies = $this->FilterResults($companies, 'id', $company_id);
                $roles = Role::all();
                $roles = $this->FilterResults($roles, 'slug', 'master-admin', 'remove');
            } else {
                $companies = Company::all();
                $roles = Role::all();
            }
        }
/*
        $companies = Company::all();
        $roles = Role::all();
*/
        $data = [
            'user'      => $user,
            'company'   => $company,
            'companies' => $companies,
            'role'      => $role,
            'roles'     => $roles,
            'error'     => $error
        ];

        return view('admin.users.edit')->with($data);
    }

    public function update(Request $request, $id){

        $request->validate([
            'user_id'       => 'required',
            'name'          => 'required',
            'email'         => ['required', 'email'],
            'old_email'     => ['required', 'email'],
            'company_id'    => 'required',
            'role_id'       => 'required'
        ]);

        $form_user_id = $request->input('user_id');
        $form_pass = $request->input('pass');
        $form_pass_conf = $request->input('pass_conf');

        //dump($_POST);

        $old_email = $request->input('old_email');
        $form_email = $request->input('email');

        if($old_email != $form_email) {
            $email_check = $this->CheckEmail($form_email, $form_user_id);
            if ($email_check) {
                return $email_check;
            }
        }
        $pass_check = $this->CheckPasswords($form_pass, $form_pass_conf, $form_user_id);
        if($pass_check){
            return $pass_check;
        }


        $data = [
            'name' => $request->input('name'),
            'email' => $form_email
        ];

        if(!empty($form_pass)){
            $data['password'] = Hash::make($form_pass);
        }

        User::whereId($form_user_id)->update($data);

        $user = User::find($form_user_id);

        $user->company()->detach();
        $new_company = Company::where('id', $request->input('company_id'))->first();
        if($new_company) {
            $new_company->users()->attach($user);
        }

        $user->role()->detach();
        $new_role = Role::find($request->input('role_id'));
        $user->roles()->attach($new_role);


        $user->permissions()->detach();
        $permissions = $new_role->permissions()->get();
        foreach($permissions as $permission){
            $user->permissions()->attach($permission);
        }

        return redirect()->route('users')
            ->with('success', 'User updated successfully');

    }

    public function destroy($id, $company_id){

        if(isset($id) && isset($company_id)) {
            $user = User::find($id);
            $user->role()->detach();
            $user->permissions()->detach();
            $user->company()->detach();
            $user->delete();

            if($company_id == 0){
                return redirect()
                    ->route('users',  $company_id)
                    ->with('success', 'User deleted successfully');
            } else {
                return redirect()
                    ->route('company.users',  $company_id)
                    ->with('success', 'User deleted successfully');
            }
        } else {
            if($company_id == 0){
                return redirect()
                    ->route('users', $company_id)
                    ->with('error', 'User not deleted');
            } else {
                return redirect()
                    ->route('company.users', $company_id)
                    ->with('error', 'User not deleted');
            }
        }
    }

    protected function GenerateRandomRow($length = 10) {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    protected function UserHasRole($user, $role){

        //$user = User::find($user_id);
        $roles = $user->role()->first();
        if($roles->slug === $role){
            return TRUE;
        } else {
            return FALSE;
        }
    }

    protected function CheckEmail($email, $user_id){

        $user = User::where('email', $email)->first();
        if($user){
            Session::put("custom_error", 'Given email already taken by other user');
            return redirect()
                ->route('users.edit', $user_id);
        } else {
            return FALSE;
        }
    }

    protected function CheckPasswords($pass, $pass_conf, $user_id){
        if(!empty($pass)) {
            if ($pass != $pass_conf) {
                Session::put("custom_error", 'Password and confirmation not match');
                return redirect()
                    ->route('users.edit', $user_id);
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    protected function FilterResults($query_result, $match_key, $match_values, $type="hold"){

        if($type === 'hold') {
            $new_result = [];
            foreach ($query_result as $key => $value) {
                if(!is_array($match_values)){
                    if($value->getAttribute($match_key) == $match_values){
                        $new_result[$key] = $value;
                    }
                } else {
                    if(in_array($value->getAttribute($match_key), $match_values)){
                        $new_result[$key] = $value;
                    }
                }
            }
        } else if($type === 'remove'){
            $new_result = [];
            foreach ($query_result as $key => $value) {
                $new_result[$key] = $value;
                if(!is_array($match_values)){
                    if($value->getAttribute($match_key) == $match_values){
                        unset($new_result[$key]);
                    }
                } else {
                    if(in_array($value->getAttribute($match_key), $match_values)){
                        unset($new_result[$key]);
                    }
                }
            }
        } else {
            $new_result = $query_result;
        }

        return $new_result;
    }
}
