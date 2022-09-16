<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

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

        // $user = \App\Models\User::factory()->create([
        //     'name' => 'Example User',
        //     'email' => 'test@example.com',
        // ]);
        // $user->assignRole($role1);

        // $user = \App\Models\User::factory()->create([
        //     'name' => 'Example Admin User',
        //     'email' => 'admin@example.com',
        // ]);
        // $user->assignRole($role2);

        // $user = \App\Models\User::factory()->create([
        //     'name' => 'Example Super-Admin User',
        //     'email' => 'superadmin@example.com',
        // ]);
        // $user->assignRole($role3);

        // $this->call([
        //     PermissionsDemoSeeder::class,
        // ]);

        // $user = \App\Models\User::all()->where('name', 'Haris')->first();
        // $user->assignRole($role3);
    }
}
