<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Dashboard
        $dashboard = Menu::firstOrCreate(
            ['slug' => 'dashboard'],
            [
                'name' => 'Dashboard',
                'icon' => 'iconsminds-home',
                'url' => '/dashboard',
                'order' => 1,
            ]
        );
        $dashboard->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-dashboard'])->id]);

        // Settings (Parent)
        $settings = Menu::firstOrCreate(
            ['slug' => 'settings'],
            [
                'name' => 'Settings',
                'icon' => 'iconsminds-gear',
                'url' => '#',
                'order' => 2,
            ]
        );

        // User Management (Child)
        $userMenu = Menu::firstOrCreate(
            ['slug' => 'users'],
            [
                'name' => 'Users',
                'icon' => 'iconsminds-male-female',
                'url' => '/admin/users',
                'parent_id' => $settings->id,
                'order' => 1,
            ]
        );
        $userMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-users'])->id]);

        // Role Management (Child)
        $roleMenu = Menu::firstOrCreate(
            ['slug' => 'roles'],
            [
                'name' => 'Roles',
                'icon' => 'iconsminds-shield',
                'url' => '/admin/roles',
                'parent_id' => $settings->id,
                'order' => 2,
            ]
        );
        $roleMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-roles'])->id]);

        // Permission Management (Child)
        $permissionMenu = Menu::firstOrCreate(
            ['slug' => 'permissions'],
            [
                'name' => 'Permissions',
                'icon' => 'simple-icon-key',
                'url' => '/admin/permissions',
                'parent_id' => $settings->id,
                'order' => 3,
            ]
        );
        $permissionMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-permissions'])->id]);

        // Menu Management (Child)
        $menuMenu = Menu::firstOrCreate(
            ['slug' => 'menus'],
            [
                'name' => 'Menus',
                'icon' => 'simple-icon-menu',
                'url' => '/admin/menu',
                'parent_id' => $settings->id,
                'order' => 4,
            ]
        );
        $menuMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-menus'])->id]);

        // Pengaturan Sistem (Child)
        $pengaturanMenu = Menu::firstOrCreate(
            ['slug' => 'pengaturan'],
            [
                'name' => 'Pengaturan Sistem',
                'icon' => 'simple-icon-settings',
                'url' => '/admin/pengaturan',
                'parent_id' => $settings->id,
                'order' => 5,
            ]
        );
        $pengaturanMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-pengaturan'])->id]);

        // Log Aktivitas (Child)
        $logMenu = Menu::firstOrCreate(
            ['slug' => 'log-aktivitas'],
            [
                'name' => 'Log Aktivitas',
                'icon' => 'simple-icon-clock',
                'url' => '/admin/log-aktivitas',
                'parent_id' => $settings->id,
                'order' => 6,
            ]
        );
        $logMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-log-aktivitas'])->id]);

        // Salin Data Semester (Child)
        $rollOverMenu = Menu::firstOrCreate(
            ['slug' => 'roll-over'],
            [
                'name' => 'Salin Data Semester',
                'icon' => 'simple-icon-rocket',
                'url' => '/admin/roll-over',
                'parent_id' => $settings->id,
                'order' => 7,
            ]
        );
        $rollOverMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-roll-over'])->id]);

        // Master Data
        $masterData = Menu::firstOrCreate(
            ['slug' => 'master-data'],
            [
                'name' => 'Master Data',
                'icon' => 'iconsminds-data-cloud',
                'url' => '#',
                'order' => 20,
            ]
        );
        
        // kurikulum
        $kurikulumMenu = Menu::firstOrCreate(
            ['slug' => 'kurikulum'],
            [
                'name' => 'Kurikulum',
                'icon' => 'iconsminds-book',
                'url' => '/kurikulum',
                'parent_id' => $masterData->id,
                'order' => 1,
            ]
        );
        $kurikulumMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-kurikulum'])->id]);

        // Tahun Akademik
        $tahunAkademikMenu = Menu::firstOrCreate(
            ['slug' => 'tahun-akademik'],
            [
                'name' => 'Tahun Akademik',
                'icon' => 'iconsminds-calendar-4',
                'url' => '/tahun-akademik',
                'parent_id' => $masterData->id,
                'order' => 2,
            ]
        );
        $tahunAkademikMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-tahun-akademik'])->id]);

        // Semester
        $semesterMenu = Menu::firstOrCreate(
            ['slug' => 'semester'],
            [
                'name' => 'Semester',
                'icon' => 'iconsminds-timer',
                'url' => '/semester',
                'parent_id' => $masterData->id,
                'order' => 3,
            ]
        );
        $semesterMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-semester'])->id]);

        // Jurusan
        $jurusanMenu = Menu::firstOrCreate(
            ['slug' => 'jurusan'],
            [
                'name' => 'Jurusan',
                'icon' => 'simple-icon-graduation',
                'url' => '/jurusan',
                'parent_id' => $masterData->id,
                'order' => 4,
            ]
        );
        $jurusanMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-jurusan'])->id]);

        // Kelas
        $kelasMenu = Menu::firstOrCreate(
            ['slug' => 'kelas'],
            [
                'name' => 'Kelas',
                'icon' => 'iconsminds-office',
                'url' => '/kelas',
                'parent_id' => $masterData->id,
                'order' => 5,
            ]
        );
        $kelasMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-kelas'])->id]);

        // Mata Pelajaran
        $mataPelajaranMenu = Menu::firstOrCreate(
            ['slug' => 'mata-pelajaran'],
            [
                'name' => 'Mata Pelajaran',
                'icon' => 'iconsminds-open-book',
                'url' => '/mata-pelajaran',
                'parent_id' => $masterData->id,
                'order' => 6,
            ]
        );
        $mataPelajaranMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-mata-pelajaran'])->id]);

        // JadwalMataPelajaran
        $jadwalPelajaranMenu = Menu::firstOrCreate(
            ['slug' => 'jadwal-pelajaran'],
            [
                'name' => 'Jadwal Pelajaran',
                'icon' => 'iconsminds-calendar-4',
                'url' => '/jadwal-pelajaran',
                'parent_id' => $masterData->id,
                'order' => 7,
            ]
        );
        $jadwalPelajaranMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-jadwal-pelajaran'])->id]);

        // Guru Menu
        $guruMenu = Menu::firstOrCreate(
            ['slug' => 'guru'],
            [
                'name' => 'Guru',
                'icon' => 'iconsminds-business-man-woman',
                'url' => '/guru',
                'parent_id' => $masterData->id,
                'order' => 8,
            ]
        );
        $guruMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-guru'])->id]);

        // Siswa Menu
        $siswaMenu = Menu::firstOrCreate(
            ['slug' => 'siswa'],
            [
                'name' => 'Siswa',
                'icon' => 'iconsminds-student-male-female',
                'url' => '/siswa',
                'parent_id' => $masterData->id,
                'order' => 9,
            ]
        );
        $siswaMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-siswa'])->id]);

        // Kenaikan Kelas
        $kenaikanKelasMenu = Menu::firstOrCreate(
            ['slug' => 'kenaikan-kelas'],
            [
                'name' => 'Kenaikan Kelas',
                'icon' => 'simple-icon-arrow-up-circle',
                'url' => '/kenaikan-kelas',
                'parent_id' => $masterData->id,
                'order' => 11,
            ]
        );
        $kenaikanKelasMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'manage-kenaikan-kelas'])->id]);

        // Module Pembelajaran / E-Learning
        $cbtMenu = Menu::firstOrCreate(
            ['slug' => 'pembelajaran'],
            [
                'name' => 'Pembelajaran',
                'icon' => 'iconsminds-tablet-with-text',
                'url' => '#',
                'order' => 30,
            ]
        );
        $cbtMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-cbt'])->id]);

        // E-Learning Home
        $elearningMenu = Menu::firstOrCreate(
            ['slug' => 'elearning'],
            [
                'name' => 'E-Learning',
                'icon' => 'simple-icon-screen-desktop',
                'url' => '/elearning',
                'parent_id' => $cbtMenu->id,
                'order' => 1,
            ]
        );
        $elearningMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-elearning'])->id]);

        // Bank Soal
        $bankSoalMenu = Menu::firstOrCreate(
            ['slug' => 'bank-soal'],
            [
                'name' => 'Bank Soal',
                'icon' => 'simple-icon-question',
                'url' => '/bank-soal',
                'parent_id' => $cbtMenu->id,
                'order' => 2,
            ]
        );
        $bankSoalMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-bank-soal'])->id]);

        // Jadwal Ujian
        $jadwalUjianMenu = Menu::firstOrCreate(
            ['slug' => 'jadwal-ujian'],
            [
                'name' => 'Jadwal Ujian',
                'icon' => 'simple-icon-calendar',
                'url' => '/jadwal-ujian',
                'parent_id' => $cbtMenu->id,
                'order' => 3,
            ]
        );
        $jadwalUjianMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-jadwal-ujian'])->id]);

        // Jurnal Mengajar
        $jurnalMengajarMenu = Menu::firstOrCreate(
            ['slug' => 'jurnal-mengajar'],
            [
                'name' => 'Jurnal Mengajar',
                'icon' => 'simple-icon-book-open',
                'url' => '/jurnal-mengajar',
                'parent_id' => $cbtMenu->id,
                'order' => 4,
            ]
        );
        $jurnalMengajarMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-jurnal-mengajar'])->id]);

        // Presensi Siswa
        $presensiMenu = Menu::firstOrCreate(
            ['slug' => 'presensi'],
            [
                'name' => 'Presensi Siswa',
                'icon' => 'simple-icon-check',
                'url' => '/presensi',
                'parent_id' => $cbtMenu->id,
                'order' => 5,
            ]
        );
        $presensiMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-presensi'])->id]);

        // Nilai Akademik
        $nilaiAkademikMenu = Menu::firstOrCreate(
            ['slug' => 'nilai'],
            [
                'name' => 'Nilai Akademik',
                'icon' => 'simple-icon-calculator',
                'url' => '/nilai',
                'parent_id' => $cbtMenu->id,
                'order' => 6,
            ]
        );
        $nilaiAkademikMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-nilai'])->id]);

        // Nilai Sikap
        $nilaiSikapMenu = Menu::firstOrCreate(
            ['slug' => 'nilai-sikap'],
            [
                'name' => 'Nilai Sikap',
                'icon' => 'simple-icon-heart',
                'url' => '/nilai-sikap',
                'parent_id' => $cbtMenu->id,
                'order' => 7,
            ]
        );
        $nilaiSikapMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-nilai-sikap'])->id]);

        // Nilai Ekstrakurikuler
        $nilaiEkskulMenu = Menu::firstOrCreate(
            ['slug' => 'nilai-ekstrakurikuler'],
            [
                'name' => 'Nilai Ekstrakurikuler',
                'icon' => 'simple-icon-star',
                'url' => '/nilai-ekstrakurikuler',
                'parent_id' => $cbtMenu->id,
                'order' => 8,
            ]
        );
        $nilaiEkskulMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-nilai-ekstrakurikuler'])->id]);

        // Dashboard Nilai
        $dashboardNilaiMenu = Menu::firstOrCreate(
            ['slug' => 'dashboard-nilai'],
            [
                'name' => 'Dashboard Nilai Siswa',
                'icon' => 'simple-icon-grid',
                'url' => '/dashboard-nilai',
                'parent_id' => $cbtMenu->id,
                'order' => 9,
            ]
        );
        $dashboardNilaiMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-dashboard-nilai'])->id]);

        // Ujian Saya
        $ujianSiswaMenu = Menu::firstOrCreate(
            ['slug' => 'ujian-siswa'],
            [
                'name' => 'Ujian Saya',
                'icon' => 'iconsminds-student-hat',
                'url' => '/ujian-siswa',
                'order' => 40,
            ]
        );
        $ujianSiswaMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-ujian-siswa'])->id]);

        // Module PKL
        $pklMenu = Menu::firstOrCreate(
            ['slug' => 'pkl'],
            [
                'name' => 'PKL / Magang',
                'icon' => 'iconsminds-factory',
                'url' => '#',
                'order' => 50,
            ]
        );
        $pklMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-pkl'])->id]);

        // Tempat PKL
        $tempatPklMenu = Menu::firstOrCreate(
            ['slug' => 'tempat-pkl'],
            [
                'name' => 'Data Industri',
                'icon' => 'simple-icon-organization',
                'url' => '/tempat-pkl',
                'parent_id' => $pklMenu->id,
                'order' => 1,
            ]
        );
        $tempatPklMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-tempat-pkl'])->id]);

        // Penempatan
        $pklSiswaMenu = Menu::firstOrCreate(
            ['slug' => 'pkl-siswa'],
            [
                'name' => 'Penempatan Siswa',
                'icon' => 'simple-icon-people',
                'url' => '/pkl-siswa',
                'parent_id' => $pklMenu->id,
                'order' => 2,
            ]
        );
        $pklSiswaMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-pkl-siswa'])->id]);

        // Nilai PKL
        $nilaiPklMenu = Menu::firstOrCreate(
            ['slug' => 'pkl-nilai'],
            [
                'name' => 'Penilaian PKL',
                'icon' => 'simple-icon-calculator',
                'url' => '/pkl-nilai',
                'parent_id' => $pklMenu->id,
                'order' => 5,
            ]
        );
        $nilaiPklMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-pkl-nilai'])->id]);

        // Reports
        $reports = Menu::firstOrCreate(
            ['slug' => 'reports'],
            [
                'name' => 'Reports',
                'icon' => 'iconsminds-folder-with-document',
                'url' => '#',
                'order' => 60,
            ]
        );
        $reports->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-reports'])->id]);

        $leggerMenu = Menu::firstOrCreate(
            ['slug' => 'legger'],
            [
                'name' => 'Legger Nilai',
                'icon' => 'simple-icon-notebook',
                'url' => '/legger',
                'parent_id' => $reports->id,
                'order' => 1,
            ]
        );
        $leggerMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-legger'])->id]);

        $raportMenu = Menu::firstOrCreate(
            ['slug' => 'raport'],
            [
                'name' => 'Raport',
                'icon' => 'simple-icon-graduation',
                'url' => '/raport',
                'parent_id' => $reports->id,
                'order' => 2,
            ]
        );
        $raportMenu->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-raport'])->id]);

        // Assign Roles
        $adminRoles = Role::whereIn('name', ['admin', 'super-admin'])->get();
        $academicRoles = Role::whereIn('name', ['guru', 'admin', 'super-admin', 'kepala-sekolah', 'siswa'])->get();
        $allRoles = Role::all();

        $dashboard->roles()->syncWithoutDetaching($allRoles);
        $settings->roles()->syncWithoutDetaching($adminRoles);
        $masterData->roles()->syncWithoutDetaching($adminRoles);
        $cbtMenu->roles()->syncWithoutDetaching($academicRoles);
        $elearningMenu->roles()->syncWithoutDetaching($academicRoles);
        $presensiMenu->roles()->syncWithoutDetaching($academicRoles);
        $nilaiAkademikMenu->roles()->syncWithoutDetaching($academicRoles);
        $nilaiSikapMenu->roles()->syncWithoutDetaching(Role::whereIn('name', ['admin', 'super-admin', 'guru'])->get());
        $nilaiEkskulMenu->roles()->syncWithoutDetaching(Role::whereIn('name', ['admin', 'super-admin', 'guru'])->get());
        $dashboardNilaiMenu->roles()->syncWithoutDetaching(Role::whereIn('name', ['admin', 'super-admin', 'guru'])->get());
        
        $reports->roles()->syncWithoutDetaching($academicRoles);
        $pklMenu->roles()->syncWithoutDetaching($academicRoles);
        $ujianSiswaMenu->roles()->syncWithoutDetaching(Role::where('name', 'siswa')->get());
    }
}
