<?php

namespace Database\Seeders;

use App\Models\Kurikulum;
use App\Models\TahunAkademik;
use Illuminate\Database\Seeder;

class TahunAkademikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $k13 = Kurikulum::where('kode', 'K13')->first();
        $merdeka = Kurikulum::where('kode', 'MERDEKA')->first();

        $tahunAkademiks = [
            [
                'kode' => '2023/2024',
                'nama' => 'Tahun Akademik 2023/2024',
                'kurikulum_id' => $k13->id,
                'tanggal_mulai' => '2023-07-17',
                'tanggal_selesai' => '2024-06-29',
                'is_active' => false,
            ],
            [
                'kode' => '2024/2025',
                'nama' => 'Tahun Akademik 2024/2025',
                'kurikulum_id' => $merdeka->id,
                'tanggal_mulai' => '2024-07-15',
                'tanggal_selesai' => '2025-06-28',
                'is_active' => true,
            ],
            [
                'kode' => '2025/2026',
                'nama' => 'Tahun Akademik 2025/2026',
                'kurikulum_id' => $merdeka->id,
                'tanggal_mulai' => '2025-07-14',
                'tanggal_selesai' => '2026-06-27',
                'is_active' => false,
            ],
        ];

        foreach ($tahunAkademiks as $tahunAkademik) {
            TahunAkademik::create($tahunAkademik);
        }
    }
}
