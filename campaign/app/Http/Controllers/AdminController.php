<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request){

        $data = [
            'user' => $request->user()->name,
        ];

        return view('admin.index')->with($data);
    }
}
