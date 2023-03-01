<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(){

        $roles = Role::orderBy('id','ASC')->paginate(15);

        return view('admin.roles.index', compact('roles'))
            ->with('i', (request()->input('page', 1) - 1) * 15);
    }

    public function create(){

        $permissions = Permission::get();
        return view('admin.roles.create',compact('permissions'));

    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permissions' => 'required',
        ]);

        $role = Role::create([
            'name' => $request->input('name'),
            'slug' => preg_replace('/\s/', '-', strtolower($request->input('name')))
        ]);

        foreach($request->input('permissions') as $permission){
            $db_permission = Permission::find($permission);
            $db_permission->roles()->attach($role);
        }

        return redirect()->route('roles')
            ->with('success','Role created successfully');
    }

    public function show($id)
    {
        $role = Role::find($id);
        $permissions = Permission::get();
        foreach($role->permissions as $role_permission){
            $role_permissions[] = $role_permission->id;
        }

        return view('admin.roles.show',compact('role','permissions', 'role_permissions'));
    }

    public function edit($id){

        $role = Role::find($id);
        $permissions = Permission::get();
        foreach($role->permissions as $role_permission){
            $role_permissions[] = $role_permission->id;
        }

        return view('admin.roles.edit',compact('role','permissions', 'role_permissions'));
    }

    public function update(Request $request, $id){

        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required',
            'permissions' => 'required',
        ]);

        $role = Role::find($id);

        Role::whereId($id)->update([
            'name' => $request->input('name'),
            'slug' => preg_replace('/\s/', '-', strtolower($request->input('slug')))
        ]);

        $role->permissions()->sync($request->input('permissions'));

        return redirect()->route('roles')
            ->with('success','Role created successfully');
    }

    public function destroy($id)
    {
        Role::where('id',$id)->delete();
        return redirect()->route('roles')
            ->with('success','Role deleted successfully');
    }
}
