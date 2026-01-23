<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Management
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            // Role Management
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',

            // Permission Management
            'view-permissions',
            'create-permissions',
            'edit-permissions',
            'delete-permissions',

            // Menu Management
            'view-menus',
            'create-menus',
            'edit-menus',
            'delete-menus',

            // Dashboard
            'view-dashboard',

            // Reports (example)
            'view-reports',
            'export-reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Super Admin - all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - most permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            'view-dashboard',
            'view-users',
            'create-users',
            'edit-users',
            'view-roles',
            'view-permissions',
            'view-menus',
            'view-reports',
        ]);

        // User - basic permissions
        $user = Role::firstOrCreate(['name' => 'siswa']);
        $user->givePermissionTo([
            'view-dashboard',
        ]);

        // Guru - medium permissions
        $guru = Role::firstOrCreate(['name' => 'guru']);
        $guru->givePermissionTo([
            'view-dashboard',
            'view-users',
            'view-reports',
            'export-reports',
        ]);
    }
}
