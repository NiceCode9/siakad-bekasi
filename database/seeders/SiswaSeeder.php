<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\OrangTua;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $siswaRole = Role::where('name', 'siswa')->first();
        $kelasList = Kelas::all();

        $namaPria = [
            'Ahmad Rizki', 'Budi Santoso', 'Dimas Prasetyo', 'Eko Saputra', 'Fajar Ramadhan',
            'Gilang Permana', 'Hadi Wijaya', 'Irfan Hakim', 'Joko Susilo', 'Kurniawan',
        ];
        $namaWanita = [
            'Ayu Lestari', 'Bella Safitri', 'Citra Dewi', 'Diah Permata', 'Eka Putri',
            'Fitri Handayani', 'Gita Maharani', 'Hana Pertiwi', 'Indah Sari', 'Jasmine Azzahra',
        ];

        $pekerjaan = ['PNS', 'Karyawan Swasta', 'Wiraswasta', 'Buruh', 'Petani'];
        $pendidikan = ['SMA', 'Diploma', 'Sarjana'];
        $penghasilan = ['1-2 Juta', '3-5 Juta', '> 5 Juta'];

        $siswaIndex = 1;

        // Create Testing Siswa
        $userSiswaTes = User::where('username', 'siswa_tes')->first();
        if ($userSiswaTes && $kelasList->isNotEmpty()) {
            $kelasFirst = $kelasList->first();
            
            $ortuTes = OrangTua::create([
                'nama_ayah' => 'Ayah Siswa Tes',
                'nama_ibu' => 'Ibu Siswa Tes',
            ]);

            $siswaTes = Siswa::create([
                'user_id' => $userSiswaTes->id,
                'orang_tua_id' => $ortuTes->id,
                'nisn' => '9999999999',
                'nis' => '999999',
                'nik' => '9999999999999999',
                'nama_lengkap' => 'Siswa Testing',
                'jenis_kelamin' => 'L',
                'email' => $userSiswaTes->email,
                'status' => 'aktif',
                'tanggal_masuk' => now(),
            ]);

            SiswaKelas::create([
                'siswa_id' => $siswaTes->id,
                'kelas_id' => $kelasFirst->id,
                'status' => 'aktif',
                'tanggal_masuk' => now(),
            ]);
        }

        foreach ($kelasList as $kelas) {
            $jumlahSiswa = 5;

            for ($i = 0; $i < $jumlahSiswa; $i++) {
                $jenisKelamin = rand(0, 1) ? 'L' : 'P';
                $nama = $jenisKelamin === 'L' 
                    ? $namaPria[array_rand($namaPria)] 
                    : $namaWanita[array_rand($namaWanita)];
                
                $nama .= ' ' . chr(65 + ($siswaIndex % 26)) . $siswaIndex;
                $email = strtolower(str_replace(' ', '', $nama)) . '@siswa.siakad.com';

                $ortu = OrangTua::create([
                    'nama_ayah' => 'Bpk. ' . $nama,
                    'pekerjaan_ayah' => $pekerjaan[array_rand($pekerjaan)],
                    'pendidikan_ayah' => $pendidikan[array_rand($pendidikan)],
                    'penghasilan_ayah' => $penghasilan[array_rand($penghasilan)],
                    'telepon_ayah' => '0812' . rand(10000000, 99999999),
                    'nama_ibu' => 'Ibu ' . $nama,
                    'pekerjaan_ibu' => 'IRT',
                ]);

                $user = User::create([
                    'username' => strtolower(str_replace([' ', '.'], '', $nama)),
                    'email' => $email,
                    'password' => Hash::make('password123'),
                ]);
                $user->assignRole($siswaRole);

                $siswa = Siswa::create([
                    'user_id' => $user->id,
                    'orang_tua_id' => $ortu->id,
                    'nisn' => str_pad($siswaIndex, 10, '0', STR_PAD_LEFT),
                    'nis' => str_pad($siswaIndex, 6, '0', STR_PAD_LEFT), // Added NIS
                    'nik' => '3212' . str_pad($siswaIndex, 12, '0', STR_PAD_LEFT), // Added NIK
                    'nama_lengkap' => $nama,
                    'jenis_kelamin' => $jenisKelamin,
                    'tempat_lahir' => 'Jakarta',
                    'tanggal_lahir' => '2008-01-01',
                    'agama' => 'Islam',
                    'telepon' => '0812' . rand(10000, 99999),
                    'email' => $email,
                    'status' => 'aktif',
                ]);

                SiswaKelas::create([
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $kelas->id,
                    'tanggal_masuk' => now()->subMonth(),
                    'status' => 'aktif',
                ]);

                $siswaIndex++;
            }
        }
    }
}
