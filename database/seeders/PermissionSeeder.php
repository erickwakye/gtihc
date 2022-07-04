<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'create role']);
        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'create permission']);
        Permission::create(['name' => 'create payment']);
        Permission::create(['name' => 'create sample']);
        Permission::create(['name' => 'create mixing']);
        Permission::create(['name' => 'create control']);
        Permission::create(['name' => 'create result']);
        Permission::create(['name' => 'create reports']);
    }
}
