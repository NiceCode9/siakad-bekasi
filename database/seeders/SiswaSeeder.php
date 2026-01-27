<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\SiswaKelas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $siswaRole = Role::where('name', 'siswa')->first();
        $kelasList = Kelas::all();

        $namaPria = [
            'Ahmad Rizki', 'Budi Santoso', 'Dimas Prasetyo', 'Eko Saputra', 'Fajar Ramadhan',
            'Gilang Permana', 'Hadi Wijaya', 'Irfan Hakim', 'Joko Susilo', 'Kurniawan',
            'Lukman Hakim', 'Muhammad Iqbal', 'Nanda Pratama', 'Oki Setiawan', 'Putra Mahardika',
            'Reza Pahlevi', 'Sandi Firmansyah', 'Taufik Hidayat', 'Umar Bakri', 'Wahyu Nugroho',
        ];

        $namaWanita = [
            'Ayu Lestari', 'Bella Safitri', 'Citra Dewi', 'Diah Permata', 'Eka Putri',
            'Fitri Handayani', 'Gita Maharani', 'Hana Pertiwi', 'Indah Sari', 'Jasmine Azzahra',
            'Kartika Sari', 'Lina Marlina', 'Maya Anggraini', 'Nisa Aulia', 'Olivia Rahmawati',
            'Putri Wulandari', 'Qonita Zahra', 'Rina Susanti', 'Siti Nurhaliza', 'Tiara Kusuma',
        ];

        $tempatLahir = [
            'Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang',
            'Bekasi', 'Tangerang', 'Depok', 'Bogor', 'Malang',
        ];

        $agama = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha'];

        $siswas = [];
        $siswaIndex = 1;

        foreach ($kelasList as $kelas) {
            // Determine number of students per class (20-25)
            $jumlahSiswa = rand(20, 25);

            for ($i = 0; $i < $jumlahSiswa; $i++) {
                $jenisKelamin = rand(0, 1) ? 'L' : 'P';
                $nama = $jenisKelamin === 'L' 
                    ? $namaPria[array_rand($namaPria)] 
                    : $namaWanita[array_rand($namaWanita)];
                
                // Add unique suffix to avoid duplicates
                $nama .= ' ' . chr(65 + ($siswaIndex % 26));

                $tahunLahir = 2008 + (ord($kelas->tingkat) - ord('X'));
                $bulanLahir = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
                $hariLahir = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
                $tanggalLahir = $tahunLahir . '-' . $bulanLahir . '-' . $hariLahir;

                $nisn = str_pad($siswaIndex, 10, '0', STR_PAD_LEFT);
                $nis = str_pad($siswaIndex, 6, '0', STR_PAD_LEFT);
                $nik = '32' . $tahunLahir . str_pad($siswaIndex, 8, '0', STR_PAD_LEFT);
                $email = strtolower(str_replace(' ', '.', $nama)) . $siswaIndex . '@siswa.smk.sch.id';
                $telepon = '0812' . str_pad($siswaIndex, 8, '0', STR_PAD_LEFT);

                $siswaData = [
                    'nisn' => $nisn,
                    'nis' => $nis,
                    'nik' => $nik,
                    'nama_lengkap' => $nama,
                    'tempat_lahir' => $tempatLahir[array_rand($tempatLahir)],
                    'tanggal_lahir' => $tanggalLahir,
                    'jenis_kelamin' => $jenisKelamin,
                    'agama' => $agama[array_rand($agama)],
                    'anak_ke' => rand(1, 3),
                    'jumlah_saudara' => rand(1, 4),
                    'telepon' => $telepon,
                    'email' => $email,
                    'alamat' => 'Jl. Contoh No. ' . $siswaIndex . ', Jakarta',
                    'rt' => str_pad(rand(1, 15), 3, '0', STR_PAD_LEFT),
                    'rw' => str_pad(rand(1, 10), 3, '0', STR_PAD_LEFT),
                    'kelurahan' => 'Kelurahan Contoh',
                    'kecamatan' => 'Kecamatan Contoh',
                    'kota' => 'Jakarta',
                    'provinsi' => 'DKI Jakarta',
                    'kode_pos' => '12345',
                    'status' => 'aktif',
                    'foto' => null,
                ];

                // Create user account
                $user = User::create([
                    'username' => strtolower(str_replace(' ', '', $nama)) . $siswaIndex,
                    'email' => $email,
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]);

                // Assign role
                $user->assignRole($siswaRole);

                // Create siswa record
                $siswa = Siswa::create(array_merge($siswaData, [
                    'user_id' => $user->id,
                ]));

                // Create siswa_kelas relationship
                SiswaKelas::create([
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $kelas->id,
                    'tanggal_masuk' => $kelas->semester->tanggal_mulai,
                    'status' => 'Aktif',
                ]);

                $siswaIndex++;
            }
        }
    }
}
