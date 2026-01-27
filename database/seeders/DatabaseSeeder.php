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
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
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
            OrangTuaSeeder::class,
        ]);
    }
}
