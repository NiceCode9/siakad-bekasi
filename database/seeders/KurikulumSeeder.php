<?php

namespace Database\Seeders;

use App\Models\Kurikulum;
use Illuminate\Database\Seeder;

class KurikulumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kurikulums = [
            [
                'kode' => 'K13',
                'nama' => 'Kurikulum 2013',
                'deskripsi' => 'Kurikulum 2013 adalah kurikulum yang menekankan pada peningkatan dan keseimbangan soft skills dan hard skills yang meliputi aspek kompetensi sikap, keterampilan, dan pengetahuan.',
                'tahun_mulai' => 2013,
                'is_active' => false,
            ],
            [
                'kode' => 'MERDEKA',
                'nama' => 'Kurikulum Merdeka',
                'deskripsi' => 'Kurikulum Merdeka adalah kurikulum dengan pembelajaran intrakurikuler yang beragam di mana konten akan lebih optimal agar peserta didik memiliki cukup waktu untuk mendalami konsep dan menguatkan kompetensi.',
                'tahun_mulai' => 2022,
                'is_active' => true,
            ],
        ];

        foreach ($kurikulums as $kurikulum) {
            Kurikulum::create($kurikulum);
        }
    }
}
