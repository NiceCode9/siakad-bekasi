<?php

namespace Database\Seeders;

use App\Models\Ekstrakurikuler;
use Illuminate\Database\Seeder;

class EkstrakurikulerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ekstrakurikulers = [
            [
                'nama' => 'Pramuka',
                'pembina_id' => null, // Will be assigned after GuruSeeder
                'hari' => 'Jumat',
                'jam_mulai' => '14:00:00',
                'jam_selesai' => '16:00:00',
                'is_active' => true,
            ],
            [
                'nama' => 'Paskibra',
                'pembina_id' => null,
                'hari' => 'Sabtu',
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '09:00:00',
                'is_active' => true,
            ],
            [
                'nama' => 'PMR (Palang Merah Remaja)',
                'pembina_id' => null,
                'hari' => 'Rabu',
                'jam_mulai' => '15:00:00',
                'jam_selesai' => '17:00:00',
                'is_active' => true,
            ],
            [
                'nama' => 'Futsal',
                'pembina_id' => null,
                'hari' => 'Selasa',
                'jam_mulai' => '15:30:00',
                'jam_selesai' => '17:30:00',
                'is_active' => true,
            ],
            [
                'nama' => 'Basket',
                'pembina_id' => null,
                'hari' => 'Kamis',
                'jam_mulai' => '15:30:00',
                'jam_selesai' => '17:30:00',
                'is_active' => true,
            ],
            [
                'nama' => 'Robotika',
                'pembina_id' => null,
                'hari' => 'Sabtu',
                'jam_mulai' => '13:00:00',
                'jam_selesai' => '15:00:00',
                'is_active' => true,
            ],
            [
                'nama' => 'English Club',
                'pembina_id' => null,
                'hari' => 'Rabu',
                'jam_mulai' => '14:00:00',
                'jam_selesai' => '15:30:00',
                'is_active' => true,
            ],
            [
                'nama' => 'Seni Tari',
                'pembina_id' => null,
                'hari' => 'Jumat',
                'jam_mulai' => '15:00:00',
                'jam_selesai' => '17:00:00',
                'is_active' => true,
            ],
            [
                'nama' => 'Karate',
                'pembina_id' => null,
                'hari' => 'Senin',
                'jam_mulai' => '15:30:00',
                'jam_selesai' => '17:00:00',
                'is_active' => true,
            ],
            [
                'nama' => 'Jurnalistik',
                'pembina_id' => null,
                'hari' => 'Kamis',
                'jam_mulai' => '14:00:00',
                'jam_selesai' => '16:00:00',
                'is_active' => true,
            ],
        ];

        foreach ($ekstrakurikulers as $ekskul) {
            Ekstrakurikuler::create($ekskul);
        }
    }
}
