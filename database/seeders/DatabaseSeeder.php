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
            RolePermissionSeeder::class,
            MenuSeeder::class,
            UserSeeder::class,
            KurikulumSeeder::class,
            TahunAkademikSeeder::class,
            SemesterSeeder::class,
            JurusanSeeder::class,
            KelasSeeder::class,
            KelompokMapelSeeder::class,
            KomponenNilaiSeeder::class,
        ]);
    }
}
