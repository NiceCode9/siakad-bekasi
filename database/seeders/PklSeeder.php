<?php

namespace Database\Seeders;

use App\Models\PerusahaanPkl;
use App\Models\Pkl;
use App\Models\NilaiPkl;
use App\Models\MonitoringPkl;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class PklSeeder extends Seeder
{
    public function run(): void
    {
        $semester = Semester::active()->first();
        if (!$semester) return;

        $gurus = Guru::limit(5)->get();
        $siswas = Siswa::whereIn('id', function($q) {
            $q->select('siswa_id')->from('siswa_kelas');
        })->limit(10)->get();

        $companies = [
            ['nama' => 'PT. Teknologi Maju', 'bidang' => 'IT & Software'],
            ['nama' => 'Bengkel Motor Sejahtera', 'bidang' => 'Otomotif'],
            ['nama' => 'Kantin Sehat SMK', 'bidang' => 'Tata Boga'],
        ];

        foreach ($companies as $idx => $cData) {
            $perusahaan = PerusahaanPkl::create([
                'nama' => $cData['nama'],
                'bidang_usaha' => $cData['bidang'],
                'alamat' => 'Jl. Industri No. ' . rand(1, 100),
                'telepon' => '021' . rand(1111111, 9999999),
                'email' => strtolower(str_replace(' ', '', $cData['nama'])) . '@example.com',
                'nama_kontak' => 'Bpk. ' . ['Anto', 'Budi', 'Cahyo'][$idx],
                'jabatan_kontak' => 'HR Manager',
                'kuota' => 5,
                'is_active' => true,
            ]);

            // Assign 2 students per company
            for ($i = 0; $i < 2; $i++) {
                $siswa = $siswas->shift();
                if (!$siswa) break;

                $pkl = Pkl::create([
                    'siswa_id' => $siswa->id,
                    'semester_id' => $semester->id,
                    'perusahaan_pkl_id' => $perusahaan->id,
                    'pembimbing_sekolah_id' => $gurus->random()->id,
                    'pembimbing_industri' => 'Supervisor ' . $perusahaan->nama,
                    'tanggal_mulai' => now()->subMonths(2),
                    'tanggal_selesai' => now()->addMonth(),
                    'posisi' => 'Intern',
                    'status' => 'aktif',
                ]);

                // Create some monitoring logs
                MonitoringPkl::create([
                    'pkl_id' => $pkl->id,
                    'tanggal_monitoring' => now()->subWeeks(2),
                    'kegiatan' => 'Belajar troubleshooting jaringan',
                    'hambatan' => 'Kurang alat tes kabel',
                    'solusi' => 'Meminjam dari kantor cabang',
                    'catatan' => 'Bagus, lanjutkan.',
                ]);

                // Create finished PKL for some students to test report
                if ($idx == 0) {
                    $pkl->update(['status' => 'selesai']);
                    NilaiPkl::create([
                        'pkl_id' => $pkl->id,
                        'nilai_sikap_kerja' => rand(80, 95),
                        'nilai_keterampilan' => rand(85, 95),
                        'nilai_laporan' => rand(80, 90),
                        'nilai_dari_industri' => 90,
                        'nilai_dari_sekolah' => 88,
                        'nilai_akhir' => 89,
                        'catatan_industri' => 'Siswa sangat kompeten',
                        'tanggal_penilaian' => now(),
                    ]);
                }
            }
        }
    }
}
