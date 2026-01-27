<?php

namespace Database\Seeders;

use App\Models\KomponenNilai;
use App\Models\Kurikulum;
use Illuminate\Database\Seeder;

class KomponenNilaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merdeka = Kurikulum::where('kode', 'MERDEKA')->first();

        $komponenNilais = [
            // Komponen Pengetahuan
            [
                'kurikulum_id' => $merdeka->id,
                'kode' => 'TUGAS',
                'nama' => 'Tugas',
                'kategori' => 'pengetahuan',
                'bobot' => 20.00,
                'keterangan' => 'Nilai dari tugas-tugas yang diberikan guru',
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kode' => 'UH',
                'nama' => 'Ulangan Harian',
                'kategori' => 'pengetahuan',
                'bobot' => 30.00,
                'keterangan' => 'Nilai dari ulangan harian per bab/materi',
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kode' => 'UTS',
                'nama' => 'Ujian Tengah Semester',
                'kategori' => 'pengetahuan',
                'bobot' => 20.00,
                'keterangan' => 'Nilai ujian tengah semester',
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kode' => 'UAS',
                'nama' => 'Ujian Akhir Semester',
                'kategori' => 'pengetahuan',
                'bobot' => 30.00,
                'keterangan' => 'Nilai ujian akhir semester',
            ],

            // Komponen Keterampilan
            [
                'kurikulum_id' => $merdeka->id,
                'kode' => 'PRAKTIK',
                'nama' => 'Praktik',
                'kategori' => 'keterampilan',
                'bobot' => 40.00,
                'keterangan' => 'Nilai dari praktik/praktikum',
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kode' => 'PROYEK',
                'nama' => 'Proyek',
                'kategori' => 'keterampilan',
                'bobot' => 30.00,
                'keterangan' => 'Nilai dari proyek yang dikerjakan',
            ],
            [
                'kurikulum_id' => $merdeka->id,
                'kode' => 'PORTOFOLIO',
                'nama' => 'Portofolio',
                'kategori' => 'keterampilan',
                'bobot' => 30.00,
                'keterangan' => 'Nilai dari kumpulan karya/portofolio siswa',
            ],
        ];

        foreach ($komponenNilais as $komponen) {
            KomponenNilai::create($komponen);
        }
    }
}
