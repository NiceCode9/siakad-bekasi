<?php

namespace Database\Seeders;

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

            // Kenaikan Kelas
            'manage-kenaikan-kelas',

            // Reports (example)
            'view-reports',
            'export-reports',

            // CBT
            'view-cbt',
            'view-bank-soal', 'create-bank-soal', 'edit-bank-soal', 'delete-bank-soal',
            'view-jadwal-ujian', 'create-jadwal-ujian', 'edit-jadwal-ujian', 'delete-jadwal-ujian',
            'view-ujian-siswa',

            // Penilaian
            'view-nilai',
            'view-nilai-sikap',
            'view-nilai-ekstrakurikuler',
            'view-dashboard-nilai',

            // Elearning
            'view-elearning',
            'view-materi', 'create-materi', 'edit-materi', 'delete-materi',
            'view-tugas', 'create-tugas', 'edit-tugas', 'delete-tugas', 'submit-tugas', 'view-submission', 'grade-submission',
            'view-forum', 'create-forum', 'edit-forum', 'delete-forum', 'post-forum', 'reply-forum',
            'view-kelas-elearning', 'manage-kelas-elearning', 'view-anggota-kelas', 'manage-anggota-kelas',
            'view-nilai-elearning', 'manage-nilai-elearning',

            // PKL
            'view-pkl',
            'view-tempat-pkl', 'create-tempat-pkl', 'edit-tempat-pkl', 'delete-tempat-pkl',
            'view-pkl-siswa', 'create-pkl-siswa', 'edit-pkl-siswa', 'delete-pkl-siswa',
            'view-jurnal-pkl', 'create-jurnal-pkl', 'edit-jurnal-pkl', 'delete-jurnal-pkl', 'approve-jurnal-pkl',
            'view-pkl-nilai', 'edit-pkl-nilai',

            // Raport
            'view-raport',
            'manage-raport',
            'approve-raport',

            // Presensi
            'view-presensi',
            'create-presensi',
            'edit-presensi',
            'delete-presensi',

            // Buku Induk
            'manage-buku-induk',
            'view-buku-induk',
            'create-buku-induk',
            'edit-buku-induk',
            'delete-buku-induk',

            // Jurnal Mengajar
            'view-jurnal-mengajar',
            'create-jurnal-mengajar',
            'edit-jurnal-mengajar',
            'delete-jurnal-mengajar',
            'approve-jurnal-mengajar',

            // Module 11: Legger
            'view-legger',
            'create-legger',
            'edit-legger',
            'delete-legger',

            // Guru Piket & BK & Kesiswaan
            'manage-absensi-harian',
            'view-pelanggaran',
            'laporkan-pelanggaran',
            'proses-pelanggaran',
            'view-resume-pelanggaran',
            'view-riwayat-konseling',
            'view-profil-siswa',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Super Admin - all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Kepala Sekolah
        $kepsek = Role::firstOrCreate(['name' => 'kepala-sekolah']);
        $kepsek->givePermissionTo([
            'view-dashboard',
            'view-reports',
            'approve-raport',
            'view-pelanggaran',
            'view-riwayat-konseling',
            'view-tempat-pkl',
            'view-nilai-pkl',
        ]);

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
            'view-cbt',
            'view-bank-soal',
            'view-jadwal-ujian',
            // PKL
            'view-pkl',
            'view-tempat-pkl', 'create-tempat-pkl', 'edit-tempat-pkl', 'delete-tempat-pkl',
            'view-pkl-siswa', 'create-pkl-siswa', 'edit-pkl-siswa', 'delete-pkl-siswa',
            'view-jurnal-pkl', 'create-jurnal-pkl', 'edit-jurnal-pkl', 'delete-jurnal-pkl', 'approve-jurnal-pkl',
            'view-pkl-nilai', 'edit-pkl-nilai',
            // Raport
            'view-raport',
            'manage-raport',
            'approve-raport',
            // Violations
            'view-pelanggaran',
            'proses-pelanggaran',
        ]);

        // User - basic permissions
        $user = Role::firstOrCreate(['name' => 'siswa']);
        $user->givePermissionTo([
            'view-dashboard',
            'view-ujian-siswa',
            'view-pkl',
            'view-jurnal-pkl', 'create-jurnal-pkl', 'edit-jurnal-pkl',
            'view-raport',
        ]);

        // Guru - medium permissions
        $guru = Role::firstOrCreate(['name' => 'guru']);
        $guru->givePermissionTo([
            'view-dashboard',
            'view-users',
            'view-reports',
            'export-reports',
            'view-cbt',
            'view-bank-soal', 'create-bank-soal', 'edit-bank-soal', 'delete-bank-soal',
            'view-jadwal-ujian', 'create-jadwal-ujian', 'edit-jadwal-ujian', 'delete-jadwal-ujian',
            'view-pkl',
            'view-jurnal-pkl', 'approve-jurnal-pkl',
            'view-pkl-nilai', 'edit-pkl-nilai',
            'view-raport',
            'view-jurnal-mengajar',
            'manage-raport',
        ]);

        // Staf Kesiswaan
        $kesiswaan = Role::firstOrCreate(['name' => 'staf-kesiswaan']);
        $kesiswaan->givePermissionTo([
            'view-dashboard',
            'view-users',
            'view-reports',
            'view-pelanggaran',
            'laporkan-pelanggaran',
            'proses-pelanggaran',
            'view-resume-pelanggaran',
            'view-profil-siswa',
        ]);

        // Guru BK
        $bk = Role::firstOrCreate(['name' => 'guru-bk']);
        $bk->givePermissionTo([
            'view-dashboard',
            'view-users',
            'view-reports',
            'view-pelanggaran',
            'view-resume-pelanggaran',
            'view-riwayat-konseling',
            'view-profil-siswa',
        ]);

        // Guru Piket
        $piket = Role::firstOrCreate(['name' => 'guru-piket']);
        $piket->givePermissionTo([
            'view-dashboard',
            'view-pelanggaran',
            'laporkan-pelanggaran',
            'manage-absensi-harian',
        ]);
    }
}
