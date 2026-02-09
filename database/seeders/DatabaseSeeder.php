<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Role & Permission Setup
            RolePermissionSeeder::class,
            MenuSeeder::class,

            // Master Data - Academic Structure
            KurikulumSeeder::class,
            TahunAkademikSeeder::class,
            SemesterSeeder::class,
            JurusanSeeder::class,

            // Master Data - Subject & Assessment
            KelompokMapelSeeder::class,
            KomponenNilaiSeeder::class,
            MataPelajaranSeeder::class,
            EkstrakurikulerSeeder::class,

            // User Accounts
            UserSeeder::class,

            // Sample Operational Data
            GuruSeeder::class,
            KelasSeeder::class,
            SiswaSeeder::class,

            // Academic Assignments & Schedules
            MataPelajaranKelasSeeder::class,
            JadwalPelajaranSeeder::class,

            // Module Specific Data
            CbtSeeder::class,
            PklSeeder::class,
            ELearningSeeder::class,
            OperasionalSeeder::class,
            NilaiSeeder::class,
            RaportSeeder::class,

            // System Setup
            PengaturanSeeder::class,
        ]);
    }
}
