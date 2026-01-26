<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kurikulum;

class KurikulumSeeder extends Seeder
{
    public function run(): void
    {
        $kurikulum = [
            [
                'kode' => 'K13',
                'nama' => 'Kurikulum 2013',
                'deskripsi' => 'Kurikulum 2013 Revisi',
                'tahun_mulai' => 2013,
                'is_active' => false,
            ],
            [
                'kode' => 'KM',
                'nama' => 'Kurikulum Merdeka',
                'deskripsi' => 'Kurikulum Merdeka',
                'tahun_mulai' => 2022,
                'is_active' => true,
            ],
        ];

        foreach ($kurikulum as $data) {
            Kurikulum::create($data);
        }
    }
}
