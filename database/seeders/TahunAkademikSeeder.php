<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TahunAkademik;
use App\Models\Kurikulum;
use Carbon\Carbon;

class TahunAkademikSeeder extends Seeder
{
      public function run(): void
      {
            $kurikulum = Kurikulum::where('kode', 'KM')->first();

            $tahunAkademik = [
                  [
                        'kode' => 'TA-2024-2025',
                        'nama' => 'Tahun Akademik 2024-2025',
                        'kurikulum_id' => $kurikulum?->id ?? 1,
                        'tanggal_mulai' => Carbon::create(2024, 7, 1),
                        'tanggal_selesai' => Carbon::create(2025, 6, 30),
                        'is_active' => true,
                  ],
                  [
                        'kode' => 'TA-2023-2024',
                        'nama' => 'Tahun Akademik 2023-2024',
                        'kurikulum_id' => $kurikulum?->id ?? 1,
                        'tanggal_mulai' => Carbon::create(2023, 7, 1),
                        'tanggal_selesai' => Carbon::create(2024, 6, 30),
                        'is_active' => false,
                  ],
            ];

            foreach ($tahunAkademik as $data) {
                  TahunAkademik::create($data);
            }
      }
}
