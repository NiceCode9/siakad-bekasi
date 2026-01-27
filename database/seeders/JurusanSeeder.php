<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jurusans = [
            [
                'kode' => 'RPL',
                'nama' => 'Rekayasa Perangkat Lunak',
                'singkatan' => 'RPL',
                'deskripsi' => 'Program keahlian yang mempelajari dan mendalami cara-cara pengembangan perangkat lunak termasuk pembuatan, pemeliharaan, manajemen kualitas, dan manajemen proyek perangkat lunak.',
                'is_active' => true,
            ],
            [
                'kode' => 'TKJ',
                'nama' => 'Teknik Komputer dan Jaringan',
                'singkatan' => 'TKJ',
                'deskripsi' => 'Program keahlian yang mempelajari tentang cara instalasi PC, instalasi LAN, maintenance PC dan troubleshooting PC serta jaringan.',
                'is_active' => true,
            ],
            [
                'kode' => 'MM',
                'nama' => 'Multimedia',
                'singkatan' => 'MM',
                'deskripsi' => 'Program keahlian yang mempelajari tentang penggunaan komputer guna membuat dan menggabungkan teks, grafik, audio, gambar bergerak (video dan animasi) dengan menggabungkan link dan tool yang memungkinkan pemakai melakukan navigasi, berinteraksi, berkreasi dan berkomunikasi.',
                'is_active' => true,
            ],
            [
                'kode' => 'AKL',
                'nama' => 'Akuntansi dan Keuangan Lembaga',
                'singkatan' => 'AKL',
                'deskripsi' => 'Program keahlian yang mempelajari dan mendalami kompetensi akuntansi keuangan, akuntansi perpajakan, komputer akuntansi, administrasi dan pengelolaan keuangan.',
                'is_active' => true,
            ],
            [
                'kode' => 'OTKP',
                'nama' => 'Otomatisasi dan Tata Kelola Perkantoran',
                'singkatan' => 'OTKP',
                'deskripsi' => 'Program keahlian yang mempelajari tentang pengelolaan dan penanganan administrasi kantor dan kesekretarisan dengan menggunakan teknologi perkantoran modern.',
                'is_active' => true,
            ],
        ];

        foreach ($jurusans as $jurusan) {
            Jurusan::create($jurusan);
        }
    }
}
