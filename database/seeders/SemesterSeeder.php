<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Semester;
use App\Models\TahunAkademik;
use Carbon\Carbon;

class SemesterSeeder extends Seeder
{
      public function run(): void
      {
            $tahunAkademik = TahunAkademik::where('is_active', true)->first();

            if (!$tahunAkademik) {
                  return;
            }

            $semester = [
                  [
                        'tahun_akademik_id' => $tahunAkademik->id,
                        'nama' => 'Ganjil',
                        'kode' => 'SM-GANJIL',
                        'tanggal_mulai' => Carbon::create(2024, 7, 1),
                        'tanggal_selesai' => Carbon::create(2024, 12, 31),
                        'is_active' => true,
                  ],
                  [
                        'tahun_akademik_id' => $tahunAkademik->id,
                        'nama' => 'Genap',
                        'kode' => 'SM-GENAP',
                        'tanggal_mulai' => Carbon::create(2025, 1, 1),
                        'tanggal_selesai' => Carbon::create(2025, 6, 30),
                        'is_active' => false,
                  ],
            ];

            foreach ($semester as $data) {
                  Semester::create($data);
            }
      }
}
