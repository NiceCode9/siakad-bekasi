<?php

namespace Database\Seeders;

use App\Models\User;
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

        // Kepala Sekolah
        $kepsek = User::create([
            'username' => 'kepsek',
            'email' => 'kepsek@example.com',
            'password' => Hash::make('password'),
        ]);
        $kepsek->assignRole('kepala-sekolah');

        // Testing Guru
        $guruTes = User::create([
            'username' => 'guru_tes',
            'email' => 'guru.tes@example.com',
            'password' => Hash::make('password'),
        ]);
        $guruTes->assignRole('guru');

        // Testing Siswa
        $siswaTes = User::create([
            'username' => 'siswa_tes',
            'email' => 'siswa.tes@example.com',
            'password' => Hash::make('password'),
        ]);
        $siswaTes->assignRole('siswa');

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
