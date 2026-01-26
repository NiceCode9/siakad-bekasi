<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KelompokMapel;

class KelompokMapelSeeder extends Seeder
{
    public function run(): void
    {
        $kelompok = [
            ['kode' => 'A', 'nama' => 'Kelompok A (Umum)', 'urutan' => 1],
            ['kode' => 'B', 'nama' => 'Kelompok B (Umum)', 'urutan' => 2],
            ['kode' => 'C1', 'nama' => 'Kelompok C1 (Dasar Kejuruan)', 'urutan' => 3],
            ['kode' => 'C2', 'nama' => 'Kelompok C2 (Dasar Program Keahlian)', 'urutan' => 4],
            ['kode' => 'C3', 'nama' => 'Kelompok C3 (Kompetensi Keahlian)', 'urutan' => 5],
        ];

        foreach ($kelompok as $data) {
            KelompokMapel::create($data);
        }
    }
}
