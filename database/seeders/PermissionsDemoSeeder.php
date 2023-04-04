<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class PermissionsDemoSeeder extends Seeder
{
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();


        Permission::create(['name' => 'create']);
        Permission::create(['name' => 'read']);
        Permission::create(['name' => 'edit']);
        Permission::create(['name' => 'delete']);
        Permission::create(['name' => 'manage']);


        //Trainee

        $role1 = Role::create(['name' => 'staff']);
        $role1->givePermissionTo('read');

        //Manager
        $role2 = Role::create(['name' => 'manager']);
        $role2->givePermissionTo('create');
        $role2->givePermissionTo('edit');
        $role2->givePermissionTo('delete');
        $role2->givePermissionTo('read');

        //Partner
        $role3 = Role::create(['name' => 'partner']);
        $role3->givePermissionTo('create');
        $role3->givePermissionTo('edit');
        $role3->givePermissionTo('delete');
        $role3->givePermissionTo('read');


        $role4 = Role::create(['name' => 'super-admin']);
        $role4->givePermissionTo('create');
        $role4->givePermissionTo('edit');
        $role4->givePermissionTo('delete');
        $role4->givePermissionTo('read');
        $role4->givePermissionTo('manage');





        $user = new User();
        $user->name = 'haris';
        $user->email = 'haris@gmail.com';
        $user->password = Hash::make('mzk123456');
        $user->save();
        $user->assignRole($role4);

        $user = new User();
        $user->name = 'Partner';
        $user->email = 'partner@gmail.com';
        $user->password = Hash::make('mzk123456');
        $user->save();
        $user->assignRole($role3);

        $user = new User();
        $user->name = 'Manager';
        $user->email = 'manager@gmail.com';
        $user->password = Hash::make('mzk123456');
        $user->save();
        $user->assignRole($role2);

        $user = new User();
        $user->name = 'Staff1';
        $user->email = 'staff1@gmail.com';
        $user->password = Hash::make('mzk123456');
        $user->save();
        $user->assignRole($role1);

        $user = new User();
        $user->name = 'Staff2';
        $user->email = 'staff2@gmail.com';
        $user->password = Hash::make('mzk123456');
        $user->save();
        $user->assignRole($role1);


        //islamabad Uesr
        $user = new User();
        $user->name = 'Islamabad Partner';
        $user->email = 'isl_partner@gmail.com';
        $user->location = 'islamabad';
        $user->password = Hash::make('mzk123456');
        $user->save();
        $user->assignRole($role3);

        $user = new User();
        $user->name = 'Islamabad Manager';
        $user->email = 'isl_manager@gmail.com';
        $user->location = 'islamabad';
        $user->password = Hash::make('mzk123456');
        $user->save();
        $user->assignRole($role2);

        $user = new User();
        $user->name = 'Islamabad Staff1';
        $user->email = 'isl_staff1@gmail.com';
        $user->location = 'islamabad';
        $user->password = Hash::make('mzk123456');
        $user->save();
        $user->assignRole($role1);

        $user = new User();
        $user->name = 'Islamabad Staff2';
        $user->email = 'isl_staff2@gmail.com';
        $user->location = 'islamabad';
        $user->password = Hash::make('mzk123456');
        $user->save();
        $user->assignRole($role1);


        //Lahore


        //islamabad Uesr
        $user = new User();
        $user->name = 'lahore Partner';
        $user->email = 'lah_partner@gmail.com';
        $user->location = 'lahore';
        $user->password = Hash::make('mzk123456');
        $user->save();
        $user->assignRole($role3);

        $user = new User();
        $user->name = 'lahore Manager';
        $user->email = 'lah_manager@gmail.com';
        $user->location = 'lahore';
        $user->password = Hash::make('mzk123456');
        $user->save();
        $user->assignRole($role2);

        $user = new User();
        $user->name = 'lahore Staff1';
        $user->email = 'lah_staff1@gmail.com';
        $user->location = 'lahore';
        $user->password = Hash::make('mzk123456');
        $user->save();
        $user->assignRole($role1);

        $user = new User();
        $user->name = 'lahore Staff2';
        $user->email = 'lah_staff2@gmail.com';
        $user->location = 'lahore';
        $user->password = Hash::make('mzk123456');
        $user->save();
        $user->assignRole($role1);
    }
}
