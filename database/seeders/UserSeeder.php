<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::create([
            'username' => 'nicecode',
            'email' => 'nicecode@example.com',
            'password' => Hash::make('sembarang'),
        ]);
        $superAdmin->assignRole('super-admin');

        // Admin
        $admin = User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Guru
        $guru = User::create([
            'username' => 'guru',
            'email' => 'guru@example.com',
            'password' => Hash::make('password'),
        ]);
        $guru->assignRole('guru');

        // Siswa
        $siswa = User::create([
            'username' => 'siswa',
            'email' => 'siswa@example.com',
            'password' => Hash::make('password'),
        ]);
        $siswa->assignRole('siswa');

        // User dengan multiple roles
        // $multiRole = User::create([
        //     'username' => 'Multi Role User',
        //     'email' => 'multirole@example.com',
        //     'password' => Hash::make('password'),
        // ]);
        // $multiRole->assignRole(['user', 'manager']);

        // // User dengan direct permission (di luar role)
        // $directPerm = User::create([
        //     'username' => 'Direct Permission User',
        //     'email' => 'directperm@example.com',
        //     'password' => Hash::make('password'),
        // ]);
        // $directPerm->assignRole('user');
        // $directPerm->givePermissionTo(['view-reports', 'view-users']); // Direct permission
    }
}
