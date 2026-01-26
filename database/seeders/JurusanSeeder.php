<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jurusan;

class JurusanSeeder extends Seeder
{
      public function run(): void
      {
            $jurusan = [
                  [
                        'kode' => 'TKJ',
                        'nama' => 'Teknik Komputer dan Jaringan',
                        'singkatan' => 'TKJ',
                        'deskripsi' => 'Program keahlian Teknik Komputer dan Jaringan',
                        'is_active' => true,
                  ],
                  [
                        'kode' => 'RPL',
                        'nama' => 'Rekayasa Perangkat Lunak',
                        'singkatan' => 'RPL',
                        'deskripsi' => 'Program keahlian Rekayasa Perangkat Lunak',
                        'is_active' => true,
                  ],
                  [
                        'kode' => 'MM',
                        'nama' => 'Multimedia',
                        'singkatan' => 'MM',
                        'deskripsi' => 'Program keahlian Multimedia',
                        'is_active' => true,
                  ],
                  [
                        'kode' => 'AKT',
                        'nama' => 'Akuntansi',
                        'singkatan' => 'AKT',
                        'deskripsi' => 'Program keahlian Akuntansi',
                        'is_active' => true,
                  ],
                  [
                        'kode' => 'OTKP',
                        'nama' => 'Otomatisasi Tata Kelola Perkantoran',
                        'singkatan' => 'OTKP',
                        'deskripsi' => 'Program keahlian Otomatisasi Tata Kelola Perkantoran',
                        'is_active' => true,
                  ],
            ];

            foreach ($jurusan as $data) {
                  Jurusan::create($data);
            }
      }
}
