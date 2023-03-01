<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class DummyPermissionController extends Controller
{

    public function Permission()
    {
        $dev_permission = Permission::where('slug', 'create-permissions')->first();

        $dev_role = new Role();
        $dev_role->slug = 'master-admin';
        $dev_role->name = 'Master Admin';
        $dev_role->save();
        $dev_role->permissions()->attach($dev_permission);

        $dev_role = Role::where('slug', 'master-admin')->first();

        $createTasks = new Permission();
        $createTasks->slug = 'create-permissions';
        $createTasks->name = 'Create permissions';
        $createTasks->save();
        $createTasks->roles()->attach($dev_role);

        $dev_role = Role::where('slug', 'master-admin')->first();

        $dev_perm = Permission::where('slug', 'create-permissions')->first();

        $manager_permission = Permission::where('slug', 'mapping-all')->first();

        $manager_role = new Role();
        $manager_role->slug = 'company-admin';
        $manager_role->name = 'Company admin';
        $manager_role->save();
        $manager_role->permissions()->attach($manager_permission);

        $manager_role = Role::where('slug', 'company-admin')->first();

        $editUsers = new Permission();
        $editUsers->slug = 'mapping-all';
        $editUsers->name = 'Mapping all';
        $editUsers->save();
        $editUsers->roles()->attach($manager_role);

        $manager_role = Role::where('slug', 'company-admin')->first();

        $manager_perm = Permission::where('slug', 'mapping-all')->first();


        $developer = new User();
        $developer->name = 'Alex Kislyov';
        $developer->email = 'serafim32167@mail.com';
        $developer->password = bcrypt('QWEasd123!');
        $developer->save();
        $developer->roles()->attach($dev_role);
        $developer->permissions()->attach($dev_perm);

        $manager = new User();
        $manager->name = 'Jon Dou';
        $manager->email = 'jdmail@mail.com';
        $manager->password = bcrypt('QWEasd123!');
        $manager->save();
        $manager->roles()->attach($manager_role);
        $manager->permissions()->attach($manager_perm);

        return redirect()->back();
    }
}
