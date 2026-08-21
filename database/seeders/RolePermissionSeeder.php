<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create some example permissions
        $permissions = ['view dashboard', 'manage users', 'manage roles', 'manage jobs'];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Create roles and assign permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $user = Role::firstOrCreate(['name' => 'user']);

        $admin->syncPermissions(Permission::all());
        $manager->syncPermissions(Permission::whereIn('name', ['view dashboard', 'manage jobs'])->get());
    }
}
