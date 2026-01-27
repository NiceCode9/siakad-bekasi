<?php

namespace Database\Seeders;

use App\Models\KelompokMapel;
use Illuminate\Database\Seeder;

class KelompokMapelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelompokMapels = [
            [
                'kode' => 'A',
                'nama' => 'Muatan Nasional',
                'urutan' => 1,
            ],
            [
                'kode' => 'B',
                'nama' => 'Muatan Kewilayahan',
                'urutan' => 2,
            ],
            [
                'kode' => 'C1',
                'nama' => 'Dasar Bidang Keahlian',
                'urutan' => 3,
            ],
            [
                'kode' => 'C2',
                'nama' => 'Dasar Program Keahlian',
                'urutan' => 4,
            ],
            [
                'kode' => 'C3',
                'nama' => 'Kompetensi Keahlian',
                'urutan' => 5,
            ],
        ];

        foreach ($kelompokMapels as $kelompok) {
            KelompokMapel::create($kelompok);
        }
    }
}
