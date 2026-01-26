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


        // Master Data (Parent)
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
        // Orang Tua Menu
        $orangTuaMenu = Menu::create([
            'name' => 'Orang Tua',
            'slug' => 'orang-tua',
            'icon' => 'iconsminds-conference',
            'url' => '/orang-tua',
            'parent_id' => $masterData->id,
            'order' => 10,
        ]);
        $orangTuaMenu->permissions()->attach(Permission::where('name', 'view-ortu')->first());


        // Pembelajaran (Parent)
        $pembelajaran = Menu::create([
            'name' => 'Pembelajaran',
            'slug' => 'pembelajaran',
            'icon' => 'iconminds-book-open',
            'url' => '#',
            'order' => 3,
        ]);
        // Mata Pelajaran
        $mataPelajaranMenu = Menu::create([
            'name' => 'Mata Pelajaran',
            'slug' => 'mata-pelajaran',
            'icon' => 'iconsminds-open-book',
            'url' => '/mata-pelajaran',
            'parent_id' => $pembelajaran->id,
            'order' => 1,
        ]);
        $mataPelajaranMenu->permissions()->attach(Permission::where('name', 'view-mata-pelajaran')->first());
        // JadwalMataPelajaran
        $jadwalPelajaranMenu = Menu::create([
            'name' => 'Jadwal Pelajaran',
            'slug' => 'jadwal-pelajaran',
            'icon' => 'iconsminds-calendar-4',
            'url' => '/jadwal-pelajaran',
            'parent_id' => $pembelajaran->id,
            'order' => 2,
        ]);
        $jadwalPelajaranMenu->permissions()->attach(Permission::where('name', 'view-jadwal-pelajaran')->first());



        // Reports (Parent)
        $reports = Menu::create([
            'name' => 'Reports',
            'slug' => 'reports',
            'icon' => 'bi bi-file-earmark-text',
            'url' => '/admin/reports',
            'order' => 3,
        ]);
        $reports->permissions()->attach(Permission::where('name', 'view-reports')->first());

        // Attach menu ke role (contoh: Dashboard untuk semua role)
        $dashboard->roles()->attach(Role::all());

        // Settings hanya untuk admin dan super-admin
        $settings->roles()->attach(Role::whereIn('name', ['admin', 'super-admin'])->get());

        // Reports untuk manager, admin, super-admin
        $reports->roles()->attach(Role::whereIn('name', ['manager', 'admin', 'super-admin'])->get());
    }
}
