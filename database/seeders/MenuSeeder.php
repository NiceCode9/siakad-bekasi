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

        // Master Data (Parent)
        $masterData = Menu::create([
            'name' => 'Master Data',
            'slug' => 'master-data',
            'icon' => 'iconsminds-big-data',
            'url' => '#',
            'order' => 2,
        ]);

        // User Management (Child)
        $userMenu = Menu::create([
            'name' => 'Users',
            'slug' => 'users',
            'icon' => 'iconsminds-male-female',
            'url' => '/users',
            'parent_id' => $masterData->id,
            'order' => 1,
        ]);
        $userMenu->permissions()->attach(Permission::where('name', 'view-users')->first());

        // Role Management (Child)
        $roleMenu = Menu::create([
            'name' => 'Roles',
            'slug' => 'roles',
            'icon' => 'iconsminds-shield',
            'url' => '/roles',
            'parent_id' => $masterData->id,
            'order' => 2,
        ]);
        $roleMenu->permissions()->attach(Permission::where('name', 'view-roles')->first());

        // Permission Management (Child)
        $permissionMenu = Menu::create([
            'name' => 'Permissions',
            'slug' => 'permissions',
            'icon' => 'simple-icon-key',
            'url' => '/permissions',
            'parent_id' => $masterData->id,
            'order' => 3,
        ]);
        $permissionMenu->permissions()->attach(Permission::where('name', 'view-permissions')->first());

        // Menu Management (Child)
        $menuMenu = Menu::create([
            'name' => 'Menus',
            'slug' => 'menus',
            'icon' => 'simple-icon-menu',
            'url' => '/menus',
            'parent_id' => $masterData->id,
            'order' => 4,
        ]);
        $menuMenu->permissions()->attach(Permission::where('name', 'view-menus')->first());

        // Reports (Parent)
        $reports = Menu::create([
            'name' => 'Reports',
            'slug' => 'reports',
            'icon' => 'bi bi-file-earmark-text',
            'url' => '/reports',
            'order' => 3,
        ]);
        $reports->permissions()->attach(Permission::where('name', 'view-reports')->first());

        // Attach menu ke role (contoh: Dashboard untuk semua role)
        $dashboard->roles()->attach(Role::all());

        // Master Data hanya untuk admin dan super-admin
        $masterData->roles()->attach(Role::whereIn('name', ['admin', 'super-admin'])->get());

        // Reports untuk manager, admin, super-admin
        $reports->roles()->attach(Role::whereIn('name', ['manager', 'admin', 'super-admin'])->get());
    }
}
