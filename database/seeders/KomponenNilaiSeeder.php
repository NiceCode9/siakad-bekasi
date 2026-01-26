<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KomponenNilai;
use App\Models\Kurikulum;

class KomponenNilaiSeeder extends Seeder
{
    public function run(): void
    {
        $k13 = Kurikulum::where('kode', 'K13')->first();
        $km = Kurikulum::where('kode', 'KM')->first();

        // Komponen untuk K13
        if ($k13) {
            KomponenNilai::create([
                'kurikulum_id' => $k13->id,
                'kode' => 'KI3',
                'nama' => 'Kompetensi Pengetahuan (KI-3)',
                'kategori' => 'pengetahuan',
                'bobot' => 50.00,
            ]);

            KomponenNilai::create([
                'kurikulum_id' => $k13->id,
                'kode' => 'KI4',
                'nama' => 'Kompetensi Keterampilan (KI-4)',
                'kategori' => 'keterampilan',
                'bobot' => 50.00,
            ]);
        }

        // Komponen untuk Kurikulum Merdeka
        if ($km) {
            KomponenNilai::create([
                'kurikulum_id' => $km->id,
                'kode' => 'SUMATIF',
                'nama' => 'Penilaian Sumatif',
                'kategori' => 'pengetahuan',
                'bobot' => 60.00,
            ]);

            KomponenNilai::create([
                'kurikulum_id' => $km->id,
                'kode' => 'FORMATIF',
                'nama' => 'Penilaian Formatif',
                'kategori' => 'pengetahuan',
                'bobot' => 40.00,
            ]);
        }
    }
}
