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

            // kurikulum
            'view-kurikulum',
            'create-kurikulum',
            'edit-kurikulum',
            'delete-kurikulum',
            // Tahun Akademik
            'view-tahun-akademik',
            'create-tahun-akademik',
            'edit-tahun-akademik',
            'delete-tahun-akademik',
            // Semester
            'view-semester',
            'create-semester',
            'edit-semester',
            'delete-semester',
            // Jurusan
            'view-jurusan',
            'create-jurusan',
            'edit-jurusan',
            'delete-jurusan',
            // Kelas
            'view-kelas',
            'create-kelas',
            'edit-kelas',
            'delete-kelas',
            // Mata Pelajaran
            'view-mata-pelajaran',
            'create-mata-pelajaran',
            'edit-mata-pelajaran',
            'delete-mata-pelajaran',
            // Jadwal Pelajaran
            'view-jadwal-pelajaran',
            'create-jadwal-pelajaran',
            'edit-jadwal-pelajaran',
            'delete-jadwal-pelajaran',

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
