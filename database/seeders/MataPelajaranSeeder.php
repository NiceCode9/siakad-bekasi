<?php

namespace Database\Seeders;

use App\Models\KelompokMapel;
use App\Models\Kurikulum;
use App\Models\MataPelajaran;
use Illuminate\Database\Seeder;

class MataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merdeka = Kurikulum::where('kode', 'MERDEKA')->first();
        
        // Get kelompok mapel
        $kelompokA = KelompokMapel::where('kode', 'A')->first();
        $kelompokB = KelompokMapel::where('kode', 'B')->first();
        $kelompokC1 = KelompokMapel::where('kode', 'C1')->first();
        $kelompokC2 = KelompokMapel::where('kode', 'C2')->first();
        $kelompokC3 = KelompokMapel::where('kode', 'C3')->first();

        $mataPelajarans = [
            // Kelompok A - Muatan Nasional
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokA->id,
                'kode' => 'PAI',
                'nama' => 'Pendidikan Agama Islam dan Budi Pekerti',
                'jenis' => 'umum',
                'kategori' => 'wajib',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokA->id,
                'kode' => 'PKN',
                'nama' => 'Pendidikan Pancasila dan Kewarganegaraan',
                'jenis' => 'umum',
                'kategori' => 'wajib',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokA->id,
                'kode' => 'BIND',
                'nama' => 'Bahasa Indonesia',
                'jenis' => 'umum',
                'kategori' => 'wajib',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokA->id,
                'kode' => 'MTK',
                'nama' => 'Matematika',
                'jenis' => 'umum',
                'kategori' => 'wajib',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokA->id,
                'kode' => 'SEJ',
                'nama' => 'Sejarah Indonesia',
                'jenis' => 'umum',
                'kategori' => 'wajib',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokA->id,
                'kode' => 'BING',
                'nama' => 'Bahasa Inggris',
                'jenis' => 'umum',
                'kategori' => 'wajib',
                'kkm' => 75.00,
                'is_active' => true,
            ],

            // Kelompok B - Muatan Kewilayahan
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokB->id,
                'kode' => 'SBD',
                'nama' => 'Seni Budaya',
                'jenis' => 'umum',
                'kategori' => 'wajib',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokB->id,
                'kode' => 'PJOK',
                'nama' => 'Pendidikan Jasmani, Olahraga dan Kesehatan',
                'jenis' => 'umum',
                'kategori' => 'wajib',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokB->id,
                'kode' => 'BJAWA',
                'nama' => 'Bahasa Jawa',
                'jenis' => 'muatan_lokal',
                'kategori' => 'wajib',
                'kkm' => 75.00,
                'is_active' => true,
            ],

            // Kelompok C1 - Dasar Bidang Keahlian (untuk semua jurusan IT)
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokC1->id,
                'kode' => 'SIM',
                'nama' => 'Simulasi dan Komunikasi Digital',
                'jenis' => 'kejuruan',
                'kategori' => 'wajib',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokC1->id,
                'kode' => 'FIS',
                'nama' => 'Fisika',
                'jenis' => 'kejuruan',
                'kategori' => 'wajib',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokC1->id,
                'kode' => 'KIM',
                'nama' => 'Kimia',
                'jenis' => 'kejuruan',
                'kategori' => 'wajib',
                'kkm' => 75.00,
                'is_active' => true,
            ],

            // Kelompok C2 - Dasar Program Keahlian (RPL/TKJ/MM)
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokC2->id,
                'kode' => 'SKKNI',
                'nama' => 'Sistem Komputer',
                'jenis' => 'kejuruan',
                'kategori' => 'wajib',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokC2->id,
                'kode' => 'KOMJAR',
                'nama' => 'Komputer dan Jaringan Dasar',
                'jenis' => 'kejuruan',
                'kategori' => 'wajib',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokC2->id,
                'kode' => 'PROGDAS',
                'nama' => 'Pemrograman Dasar',
                'jenis' => 'kejuruan',
                'kategori' => 'wajib',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokC2->id,
                'kode' => 'DDGRAF',
                'nama' => 'Dasar Design Grafis',
                'jenis' => 'kejuruan',
                'kategori' => 'wajib',
                'kkm' => 75.00,
                'is_active' => true,
            ],

            // Kelompok C3 - Kompetensi Keahlian RPL
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokC3->id,
                'kode' => 'PBO',
                'nama' => 'Pemrograman Berorientasi Objek',
                'jenis' => 'kejuruan',
                'kategori' => 'peminatan',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokC3->id,
                'kode' => 'BASDAT',
                'nama' => 'Basis Data',
                'jenis' => 'kejuruan',
                'kategori' => 'peminatan',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokC3->id,
                'kode' => 'PWEB',
                'nama' => 'Pemrograman Web',
                'jenis' => 'kejuruan',
                'kategori' => 'peminatan',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokC3->id,
                'kode' => 'PMOBILE',
                'nama' => 'Pemrograman Mobile',
                'jenis' => 'kejuruan',
                'kategori' => 'peminatan',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokC3->id,
                'kode' => 'PPL',
                'nama' => 'Produk Kreatif dan Kewirausahaan',
                'jenis' => 'kejuruan',
                'kategori' => 'peminatan',
                'kkm' => 75.00,
                'is_active' => true,
            ],

            // Kompetensi Keahlian TKJ
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokC3->id,
                'kode' => 'ADMSRV',
                'nama' => 'Administrasi Infrastruktur Jaringan',
                'jenis' => 'kejuruan',
                'kategori' => 'peminatan',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokC3->id,
                'kode' => 'ADMSYS',
                'nama' => 'Administrasi Sistem Jaringan',
                'jenis' => 'kejuruan',
                'kategori' => 'peminatan',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokC3->id,
                'kode' => 'DIAGJAR',
                'nama' => 'Diagnosa WAN',
                'jenis' => 'kejuruan',
                'kategori' => 'peminatan',
                'kkm' => 75.00,
                'is_active' => true,
            ],

            // Kompetensi Keahlian Multimedia
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokC3->id,
                'kode' => 'ANIMASI2D',
                'nama' => 'Animasi 2D dan 3D',
                'jenis' => 'kejuruan',
                'kategori' => 'peminatan',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokC3->id,
                'kode' => 'DESGRAF',
                'nama' => 'Design Grafis Percetakan',
                'jenis' => 'kejuruan',
                'kategori' => 'peminatan',
                'kkm' => 75.00,
                'is_active' => true,
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kelompok_mapel_id' => $kelompokC3->id,
                'kode' => 'PRODVID',
                'nama' => 'Produksi dan Siaran Program Video',
                'jenis' => 'kejuruan',
                'kategori' => 'peminatan',
                'kkm' => 75.00,
                'is_active' => true,
            ],
        ];

        foreach ($mataPelajarans as $mapel) {
            MataPelajaran::create($mapel);
        }
    }
}
