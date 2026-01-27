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
        $dashboard = Menu::create([
            'name' => 'Dashboard',
            'slug' => 'dashboard',
            'icon' => 'iconsminds-home',
            'url' => '/dashboard',
            'order' => 1,
        ]);
        $dashboard->permissions()->attach(Permission::where('name', 'view-dashboard')->first());

        // Settings (Parent)
        $settings = Menu::create([
            'name' => 'Settings',
            'slug' => 'settings',
            'icon' => 'iconsminds-gear',
            'url' => '#',
            'order' => 2,
        ]);

        // User Management (Child)
        $userMenu = Menu::create([
            'name' => 'Users',
            'slug' => 'users',
            'icon' => 'iconsminds-male-female',
            'url' => '/admin/users',
            'parent_id' => $settings->id,
            'order' => 1,
        ]);
        $userMenu->permissions()->attach(Permission::where('name', 'view-users')->first());

        // Role Management (Child)
        $roleMenu = Menu::create([
            'name' => 'Roles',
            'slug' => 'roles',
            'icon' => 'iconsminds-shield',
            'url' => '/admin/roles',
            'parent_id' => $settings->id,
            'order' => 2,
        ]);
        $roleMenu->permissions()->attach(Permission::where('name', 'view-roles')->first());

        // Permission Management (Child)
        $permissionMenu = Menu::create([
            'name' => 'Permissions',
            'slug' => 'permissions',
            'icon' => 'simple-icon-key',
            'url' => '/admin/permissions',
            'parent_id' => $settings->id,
            'order' => 3,
        ]);
        $permissionMenu->permissions()->attach(Permission::where('name', 'view-permissions')->first());

        // Menu Management (Child)
        $menuMenu = Menu::create([
            'name' => 'Menus',
            'slug' => 'menus',
            'icon' => 'simple-icon-menu',
            'url' => '/admin/menu',
            'parent_id' => $settings->id,
            'order' => 4,
        ]);
        $menuMenu->permissions()->attach(Permission::where('name', 'view-menus')->first());

        // Master Data
        $masterData = Menu::create([
            'name' => 'Master Data',
            'slug' => 'master-data',
            'icon' => 'iconsminds-data-cloud',
            'url' => '#',
            'order' => 2,
        ]);
        // kurikulum
        $kurikulumMenu = Menu::create([
            'name' => 'Kurikulum',
            'slug' => 'kurikulum',
            'icon' => 'iconsminds-book',
            'url' => '/kurikulum',
            'parent_id' => $masterData->id,
            'order' => 1,
        ]);
        $kurikulumMenu->permissions()->attach(Permission::where('name', 'view-kurikulum')->first());
        // Tahun Akademik
        $tahunAkademikMenu = Menu::create([
            'name' => 'Tahun Akademik',
            'slug' => 'tahun-akademik',
            'icon' => 'iconsminds-calendar-4',
            'url' => '/tahun-akademik',
            'parent_id' => $masterData->id,
            'order' => 2,
        ]);
        $tahunAkademikMenu->permissions()->attach(Permission::where('name', 'view-tahun-akademik')->first());
        // Semester
        $semesterMenu = Menu::create([
            'name' => 'Semester',
            'slug' => 'semester',
            'icon' => 'iconsminds-timer',
            'url' => '/semester',
            'parent_id' => $masterData->id,
            'order' => 3,
        ]);
        $semesterMenu->permissions()->attach(Permission::where('name', 'view-semester')->first());
        // Jurusan
        $jurusanMenu = Menu::create([
            'name' => 'Jurusan',
            'slug' => 'jurusan',
            'icon' => 'simple-icon-graduation',
            'url' => '/jurusan',
            'parent_id' => $masterData->id,
            'order' => 4,
        ]);
        $jurusanMenu->permissions()->attach(Permission::where('name', 'view-jurusan')->first());
        // Kelas
        $kelasMenu = Menu::create([
            'name' => 'Kelas',
            'slug' => 'kelas',
            'icon' => 'iconsminds-office',
            'url' => '/kelas',
            'parent_id' => $masterData->id,
            'order' => 5,
        ]);
        $kelasMenu->permissions()->attach(Permission::where('name', 'view-jurusan')->first());
        // Mata Pelajaran
        $mataPelajaranMenu = Menu::create([
            'name' => 'Mata Pelajaran',
            'slug' => 'mata-pelajaran',
            'icon' => 'iconsminds-open-book',
            'url' => '/mata-pelajaran',
            'parent_id' => $masterData->id,
            'order' => 6,
        ]);
        $mataPelajaranMenu->permissions()->attach(Permission::where('name', 'view-mata-pelajaran')->first());
        // JadwalMataPelajaran
        $jadwalPelajaranMenu = Menu::create([
            'name' => 'Jadwal Pelajaran',
            'slug' => 'jadwal-pelajaran',
            'icon' => 'iconsminds-calendar-4',
            'url' => '/jadwal-pelajaran',
            'parent_id' => $masterData->id,
            'order' => 7,
        ]);
        $jadwalPelajaranMenu->permissions()->attach(Permission::where('name', 'view-jadwal-pelajaran')->first());

        // Guru Menu
        $guruMenu = Menu::create([
            'name' => 'Guru',
            'slug' => 'guru',
            'icon' => 'iconsminds-business-man-woman',
            'url' => '/guru',
            'parent_id' => $masterData->id,
            'order' => 8,
        ]);
        $guruMenu->permissions()->attach(Permission::where('name', 'view-guru')->first());

        // Siswa Menu
        $siswaMenu = Menu::create([
            'name' => 'Siswa',
            'slug' => 'siswa',
            'icon' => 'iconsminds-student-male-female',
            'url' => '/siswa',
            'parent_id' => $masterData->id,
            'order' => 9,
        ]);
        $siswaMenu->permissions()->attach(Permission::where('name', 'view-siswa')->first());

        // $orangTuaMenu->permissions()->attach(Permission::where('name', 'view-ortu')->first());

        // Kenaikan Kelas
        $kenaikanKelasMenu = Menu::create([
            'name' => 'Kenaikan Kelas',
            'slug' => 'kenaikan-kelas',
            'icon' => 'simple-icon-arrow-up-circle',
            'url' => '/kenaikan-kelas',
            'parent_id' => $masterData->id,
            'order' => 11,
        ]);
        $kenaikanKelasMenu->permissions()->attach(Permission::where('name', 'manage-kenaikan-kelas')->first());
        $kenaikanKelasMenu->roles()->attach(Role::whereIn('name', ['admin', 'super-admin'])->get());



        // Module Pembelajaran / E-Learning
        $cbtMenu = Menu::create([
            'name' => 'Pembelajaran',
            'slug' => 'pembelajaran',
            'icon' => 'iconsminds-tablet-with-text',
            'url' => '#',
            'order' => 20,
        ]);
        $cbtMenu->permissions()->attach(Permission::where('name', 'view-cbt')->first());
        $cbtMenu->roles()->attach(Role::whereIn('name', ['guru', 'admin', 'super-admin'])->get());

        // E-Learning Home
        $elearningMenu = Menu::create([
            'name' => 'E-Learning',
            'slug' => 'elearning',
            'icon' => 'simple-icon-screen-desktop',
            'url' => '/elearning',
            'parent_id' => $cbtMenu->id,
            'order' => 1,
        ]);
        $elearningMenu->permissions()->attach(Permission::where('name', 'view-elearning')->first());
        $elearningMenu->roles()->attach(Role::whereIn('name', ['guru', 'admin', 'super-admin', 'siswa'])->get());

        // Bank Soal
        $bankSoalMenu = Menu::create([
            'name' => 'Bank Soal',
            'slug' => 'bank-soal',
            'icon' => 'simple-icon-question',
            'url' => '/bank-soal',
            'parent_id' => $cbtMenu->id,
            'order' => 2,
        ]);
        $bankSoalMenu->permissions()->attach(Permission::where('name', 'view-bank-soal')->first());
        $bankSoalMenu->roles()->attach(Role::whereIn('name', ['guru', 'admin', 'super-admin'])->get());

        // Jadwal Ujian
        $jadwalUjianMenu = Menu::create([
            'name' => 'Jadwal Ujian',
            'slug' => 'jadwal-ujian',
            'icon' => 'simple-icon-calendar',
            'url' => '/jadwal-ujian',
            'parent_id' => $cbtMenu->id,
            'order' => 3,
        ]);
        $jadwalUjianMenu->permissions()->attach(Permission::where('name', 'view-jadwal-ujian')->first());
        $jadwalUjianMenu->roles()->attach(Role::whereIn('name', ['guru', 'admin', 'super-admin'])->get());

        // Jurnal Mengajar
        $jurnalMengajarMenu = Menu::create([
            'name' => 'Jurnal Mengajar',
            'slug' => 'jurnal-mengajar',
            'icon' => 'simple-icon-book-open',
            'url' => '/jurnal-mengajar',
            'parent_id' => $cbtMenu->id,
            'order' => 4,
        ]);
        $jurnalMengajarMenu->permissions()->attach(Permission::where('name', 'view-jurnal-mengajar')->first());
        $jurnalMengajarMenu->roles()->attach(Role::whereIn('name', ['guru', 'admin', 'super-admin'])->get());

        // Ujian Siswa (Root or Child? usually independent for student dashboard, but let's put under Pembelajaran too or separate)
        // For Students, they might not see 'Pembelajaran' parent if they don't have view-cbt permission.
        // I gave 'view-cbt' to Guru/Admin. Siswa only has 'view-ujian-siswa'.
        // So Siswa won't see parent 'Pembelajaran' if it requires 'view-cbt'.
        // I should probably give 'view-cbt' to Siswa OR make a separate root menu for Siswa.
        // Let's make separate menu "Ujian Saya" for Siswa at root level?
        
        $ujianSiswaMenu = Menu::create([
            'name' => 'Ujian Saya',
            'slug' => 'ujian-siswa',
            'icon' => 'iconsminds-student-hat',
            'url' => '/ujian-siswa',
            'order' => 5,
        ]);
        $ujianSiswaMenu->permissions()->attach(Permission::where('name', 'view-ujian-siswa')->first());
        $ujianSiswaMenu->roles()->attach(Role::where('name', 'siswa')->first());


        // Module PKL
        $pklMenu = Menu::create([
            'name' => 'PKL / Magang',
            'slug' => 'pkl',
            'icon' => 'iconsminds-factory',
            'url' => '#',
            'order' => 21,
        ]);
        $pklMenu->permissions()->attach(Permission::where('name', 'view-pkl')->first());
        $pklMenu->roles()->attach(Role::whereIn('name', ['admin', 'super-admin', 'guru'])->get());

        // Tempat PKL
        $tempatPklMenu = Menu::create([
            'name' => 'Data Industri',
            'slug' => 'tempat-pkl',
            'icon' => 'simple-icon-organization',
            'url' => '/tempat-pkl',
            'parent_id' => $pklMenu->id,
            'order' => 1,
        ]);
        $tempatPklMenu->permissions()->attach(Permission::where('name', 'view-tempat-pkl')->first());
        $tempatPklMenu->roles()->attach(Role::whereIn('name', ['admin', 'super-admin'])->get());

        // Penempatan
        $pklSiswaMenu = Menu::create([
            'name' => 'Penempatan Siswa',
            'slug' => 'pkl-siswa',
            'icon' => 'simple-icon-people',
            'url' => '/pkl-siswa',
            'parent_id' => $pklMenu->id,
            'order' => 2,
        ]);
        $pklSiswaMenu->permissions()->attach(Permission::where('name', 'view-pkl-siswa')->first());
        $pklSiswaMenu->roles()->attach(Role::whereIn('name', ['admin', 'super-admin'])->get());

        // Jurnal PKL (Siswa)
        $jurnalPklSiswa = Menu::create([
            'name' => 'Jurnal PKL Saya',
            'slug' => 'jurnal-pkl-siswa',
            'icon' => 'simple-icon-notebook',
            'url' => '/jurnal-pkl',
            'parent_id' => $pklMenu->id,
            'order' => 3,
        ]);
        $jurnalPklSiswa->permissions()->attach(Permission::where('name', 'view-jurnal-pkl')->first());
        $jurnalPklSiswa->roles()->attach(Role::where('name', 'siswa')->first());

        // Jurnal PKL (Guru/Pembimbing)
        $jurnalPklGuru = Menu::create([
            'name' => 'Monitoring Jurnal',
            'slug' => 'jurnal-pkl-pembimbing',
            'icon' => 'simple-icon-check',
            'url' => '/jurnal-pkl/pembimbing',
            'parent_id' => $pklMenu->id,
            'order' => 4,
        ]);
        $jurnalPklGuru->permissions()->attach(Permission::where('name', 'approve-jurnal-pkl')->first());
        $jurnalPklGuru->roles()->attach(Role::whereIn('name', ['guru', 'admin', 'super-admin'])->get());

        // Nilai PKL
        $nilaiPklMenu = Menu::create([
            'name' => 'Penilaian PKL',
            'slug' => 'pkl-nilai',
            'icon' => 'simple-icon-calculator',
            'url' => '/id/pkl-nilai',
            'parent_id' => $pklMenu->id,
            'order' => 5,
        ]);
        $nilaiPklMenu->permissions()->attach(Permission::where('name', 'view-pkl-nilai')->first());
        $nilaiPklMenu->roles()->attach(Role::whereIn('name', ['guru', 'admin', 'super-admin', 'kepala-sekolah'])->get());

        // Reports (Parent)
        $reports = Menu::create([
            'name' => 'Reports',
            'slug' => 'reports',
            'icon' => 'iconsminds-folder-with-document',
            'url' => '#',
            'order' => 30,
        ]);
        $reports->permissions()->attach(Permission::where('name', 'view-reports')->first());

        // Legger Nilai (Child)
        $leggerMenu = Menu::create([
            'name' => 'Legger Nilai',
            'slug' => 'legger',
            'icon' => 'simple-icon-notebook',
            'url' => '/legger',
            'parent_id' => $reports->id,
            'order' => 1,
        ]);
        $leggerMenu->permissions()->attach(Permission::where('name', 'view-legger')->first());
        $leggerMenu->roles()->attach(Role::whereIn('name', ['guru', 'admin', 'super-admin', 'kepala-sekolah'])->get());

        // Raport (Child)
        $raportMenu = Menu::create([
            'name' => 'Raport',
            'slug' => 'raport',
            'icon' => 'simple-icon-graduation',
            'url' => '/raport',
            'parent_id' => $reports->id,
            'order' => 2,
        ]);
        $raportMenu->permissions()->attach(Permission::where('name', 'view-raport')->first());
        $raportMenu->roles()->attach(Role::whereIn('name', ['guru', 'admin', 'super-admin', 'kepala-sekolah', 'siswa'])->get());

        // Attach menu ke role
        $dashboard->roles()->attach(Role::all());
        $settings->roles()->attach(Role::whereIn('name', ['admin', 'super-admin'])->get());
        $cbtMenu->roles()->attach(Role::whereIn('name', ['guru', 'admin', 'super-admin'])->get());
        $reports->roles()->attach(Role::whereIn('name', ['manager', 'admin', 'super-admin', 'kepala-sekolah', 'guru'])->get());
    }
}
