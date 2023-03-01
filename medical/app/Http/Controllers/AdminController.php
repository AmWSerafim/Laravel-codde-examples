<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index(Request $request){

        if(Auth::check()) {
            $user = Auth::user();
            $role = $user->role()->first();

            if($role->slug == "company-user"){
                return redirect()->route('import');
            } else if($role->slug == "master-admin"){
                return redirect()->route('companies');
            } else {
                return redirect()->route('mapping');
            }

//            dump($role->slug);
        }


        $data = [
            'user' => $request->user()->name,
        ];

        return view('admin.index')->with($data);
    }
}
