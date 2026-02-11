<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GuruPiketMenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Parent Menu: Piket & Kesiswaan
        $piketMenu = Menu::firstOrCreate(
            ['slug' => 'piket-kesiswaan'],
            [
                'name' => 'Piket & Kesiswaan',
                'icon' => 'iconsminds-police', // Adjust icon as needed
                'url' => '#',
                'order' => 25, // Between Master Data and Pembelajaran
            ]
        );

        // Roles allowed to see the parent menu
        $piketRoles = Role::whereIn('name', ['guru', 'staf-kesiswaan', 'guru-bk', 'admin', 'super-admin'])->get();
        $piketMenu->roles()->syncWithoutDetaching($piketRoles);

        // 2. Child: Presensi Harian
        $presensiHarian = Menu::firstOrCreate(
            ['slug' => 'presensi-harian'],
            [
                'name' => 'Presensi Harian',
                'icon' => 'simple-icon-calendar',
                'url' => '/presensi-harian',
                'parent_id' => $piketMenu->id,
                'order' => 1,
            ]
        );
        $presensiHarian->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'manage-absensi-harian'])->id]);
        $presensiHarian->roles()->syncWithoutDetaching($piketRoles); // Filtered by permission anyway

        // 3. Child: Data Pelanggaran
        $pelanggaran = Menu::firstOrCreate(
            ['slug' => 'pelanggaran-siswa'],
            [
                'name' => 'Data Pelanggaran',
                'icon' => 'simple-icon-ban',
                'url' => '/pelanggaran-siswa',
                'parent_id' => $piketMenu->id,
                'order' => 2,
            ]
        );
        $pelanggaran->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-pelanggaran'])->id]);
        $pelanggaran->roles()->syncWithoutDetaching($piketRoles);

        // 4. Child: Rekap Pelanggaran
        $rekap = Menu::firstOrCreate(
            ['slug' => 'rekap-pelanggaran-siswa'],
            [
                'name' => 'Rekap Pelanggaran',
                'icon' => 'simple-icon-chart',
                'url' => '/pelanggaran-siswa/resume',
                'parent_id' => $piketMenu->id,
                'order' => 3,
            ]
        );
        $rekap->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['name' => 'view-resume-pelanggaran'])->id]);
        $rekap->roles()->syncWithoutDetaching($piketRoles);
    }
}
