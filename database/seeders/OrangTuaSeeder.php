<?php

namespace Database\Seeders;

use App\Models\OrangTua;
use App\Models\Siswa;
use Illuminate\Database\Seeder;

class OrangTuaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $siswas = Siswa::all();

        $pekerjaan = [
            'PNS', 'TNI/Polri', 'Karyawan Swasta', 'Wiraswasta', 'Pedagang',
            'Petani', 'Buruh', 'Guru', 'Dokter', 'Pengacara', 'Sopir', 'Tukang',
        ];

        $pendidikan = [
            'SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3',
        ];

        $penghasilan = [
            '< 1 Juta', '1-2 Juta', '2-3 Juta', '3-5 Juta', '5-10 Juta', '> 10 Juta',
        ];

        foreach ($siswas as $index => $siswa) {
            // Generate parent names based on student name
            $namaDepan = explode(' ', $siswa->nama)[0];
            
            if ($siswa->jenis_kelamin === 'L') {
                $namaAyah = 'Bapak ' . chr(65 + ($index % 26)) . ' ' . $namaDepan;
                $namaIbu = 'Ibu ' . chr(65 + ($index % 26)) . ' ' . $namaDepan;
            } else {
                $namaAyah = 'Bapak ' . chr(65 + ($index % 26)) . ' Suami ' . $namaDepan;
                $namaIbu = 'Ibu ' . $namaDepan;
            }

            OrangTua::create([
                'siswa_id' => $siswa->id,
                'nama_ayah' => $namaAyah,
                'nik_ayah' => '32' . (1970 + rand(0, 20)) . str_pad($index * 2, 10, '0', STR_PAD_LEFT),
                'tempat_lahir_ayah' => $siswa->tempat_lahir,
                'tanggal_lahir_ayah' => (1970 + rand(0, 20)) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                'pendidikan_ayah' => $pendidikan[array_rand($pendidikan)],
                'pekerjaan_ayah' => $pekerjaan[array_rand($pekerjaan)],
                'penghasilan_ayah' => $penghasilan[array_rand($penghasilan)],
                'no_hp_ayah' => '0813' . str_pad($index * 2, 8, '0', STR_PAD_LEFT),
                
                'nama_ibu' => $namaIbu,
                'nik_ibu' => '32' . (1972 + rand(0, 20)) . str_pad($index * 2 + 1, 10, '0', STR_PAD_LEFT),
                'tempat_lahir_ibu' => $siswa->tempat_lahir,
                'tanggal_lahir_ibu' => (1972 + rand(0, 20)) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                'pendidikan_ibu' => $pendidikan[array_rand($pendidikan)],
                'pekerjaan_ibu' => rand(0, 1) ? 'Ibu Rumah Tangga' : $pekerjaan[array_rand($pekerjaan)],
                'penghasilan_ibu' => rand(0, 1) ? '< 1 Juta' : $penghasilan[array_rand($penghasilan)],
                'no_hp_ibu' => '0814' . str_pad($index * 2 + 1, 8, '0', STR_PAD_LEFT),
                
                'nama_wali' => null,
                'nik_wali' => null,
                'tempat_lahir_wali' => null,
                'tanggal_lahir_wali' => null,
                'pendidikan_wali' => null,
                'pekerjaan_wali' => null,
                'penghasilan_wali' => null,
                'no_hp_wali' => null,
            ]);
        }
    }
}
