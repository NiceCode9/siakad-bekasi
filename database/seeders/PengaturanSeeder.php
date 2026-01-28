<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Kategori: Umum
            [
                'kunci' => 'nama_sekolah',
                'nilai' => 'SIAKAD SMK Bekasi',
                'tipe' => 'string',
                'kategori' => 'Umum',
                'deskripsi' => 'Nama institusi pendidikan',
            ],
            [
                'kunci' => 'alamat_sekolah',
                'nilai' => 'Jl. Pendidikan No. 123, Bekasi, Jawa Barat',
                'tipe' => 'string',
                'kategori' => 'Umum',
                'deskripsi' => 'Alamat lengkap institusi',
            ],
            [
                'kunci' => 'logo_sekolah',
                'nilai' => null,
                'tipe' => 'string',
                'kategori' => 'Umum',
                'deskripsi' => 'Path file logo sekolah',
            ],

            // Kategori: Akademik
            [
                'kunci' => 'nama_kepala_sekolah',
                'nilai' => 'Drs. Haji Ahmad Maulana, M.Pd.',
                'tipe' => 'string',
                'kategori' => 'Akademik',
                'deskripsi' => 'Nama lengkap Kepala Sekolah beserta gelar',
            ],
            [
                'kunci' => 'nip_kepala_sekolah',
                'nilai' => '197501012000031001',
                'tipe' => 'string',
                'kategori' => 'Akademik',
                'deskripsi' => 'NIP Kepala Sekolah',
            ],
            [
                'kunci' => 'kkm_default',
                'nilai' => '75',
                'tipe' => 'number',
                'kategori' => 'Akademik',
                'deskripsi' => 'Nilai Kriteria Ketuntasan Minimal standar',
            ],

            // Kategori: Kontak
            [
                'kunci' => 'email_sekolah',
                'nilai' => 'info@smkbekasi.sch.id',
                'tipe' => 'string',
                'kategori' => 'Kontak',
                'deskripsi' => 'Email resmi sekolah',
            ],
            [
                'kunci' => 'telepon_sekolah',
                'nilai' => '021-88889999',
                'tipe' => 'string',
                'kategori' => 'Kontak',
                'deskripsi' => 'Nomor telepon resmi sekolah',
            ],
            [
                'kunci' => 'website_sekolah',
                'nilai' => 'https://smkbekasi.sch.id',
                'tipe' => 'string',
                'kategori' => 'Kontak',
                'deskripsi' => 'Alamat website resmi',
            ],
        ];

        foreach ($settings as $setting) {
            Pengaturan::firstOrCreate(
                ['kunci' => $setting['kunci']],
                [
                    'nilai' => $setting['nilai'],
                    'tipe' => $setting['tipe'],
                    'kategori' => $setting['kategori'],
                    'deskripsi' => $setting['deskripsi'],
                ]
            );
        }
    }
}
