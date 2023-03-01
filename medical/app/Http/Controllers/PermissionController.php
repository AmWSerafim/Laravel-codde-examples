<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{

    public function index(){

        $permissions = Permission::orderBy('id')->paginate(15);

        $data = [
            "permissions" => $permissions
        ];

        return view('admin.permissions.index')->with($data);
    }

    public function create(){

        return view('admin.permissions.create');
    }

    public function store(Request $request){

        $request->validate([
            'name' => 'required',
        ]);

        $data = $request->all();

        $data['slug'] = preg_replace("/\s/", "-", strtolower($data['name']));

        Permission::create($data);

        return redirect()->route('permissions')
            ->with('success', 'Permission created successfully.');
    }

    public function show($id){

        $row = Permission::find($id);

        $data = [
            'permission' => $row
        ];

        return view('admin.permissions.show')->with($data);
    }

    public function edit($id){

        $row = Permission::find($id);
        $data = [
            'permission' => $row
        ];

        return view('admin.permissions.edit')->with($data);
    }

    public function update(Request $request, $id){

        $request->validate([
            'name' => 'required',
            'slug' => 'required'
        ]);

        $data = [
            'name' => $request->name,
            'slug' => preg_replace("/\s/", "-", strtolower($request->slug))
        ];

        Permission::whereId($id)->update($data);

        return redirect()->route('permissions')
            ->with('success', 'Permission updated successfully');
    }

    public function destroy($id)
    {
        if(isset($id)) {
            Permission::find($id)->delete();

            return redirect()->route('permissions')
                ->with('success', 'Permission deleted successfully');
        } else {

            return redirect()->route('permissions')
                ->with('error', 'Permission not deleted');
        }
    }
}
