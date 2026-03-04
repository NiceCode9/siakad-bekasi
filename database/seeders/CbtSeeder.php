<?php

namespace Database\Seeders;

use App\Models\BankSoal;
use App\Models\Soal;
use App\Models\JadwalUjian;
use App\Models\MataPelajaran;
use App\Models\Guru;
use App\Models\Semester;
use App\Models\MataPelajaranKelas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CbtSeeder extends Seeder
{
    public function run(): void
    {
        $semester = Semester::active()->first();
        if (!$semester) return;

        $gurus = Guru::limit(5)->get();
        $mapels = MataPelajaran::limit(5)->get();

        foreach ($mapels as $idx => $mapel) {
            $guru = $gurus[$idx] ?? $gurus->first();
            
            // 1. Create Bank Soal
            $bankSoal = BankSoal::create([
                'mata_pelajaran_id' => $mapel->id,
                'pembuat_id' => $guru->id,
                'kode' => 'BANK-' . $mapel->kode . '-' . rand(100,999),
                'nama' => 'Bank Soal ' . $mapel->nama,
                'deskripsi' => 'Kumpulan soal untuk mata pelajaran ' . $mapel->nama,
                'tingkat_kesulitan' => ['mudah', 'sedang', 'sulit'][rand(0, 2)],
                'is_active' => true,
            ]);

            // 2. Create Soal (Pilihan Ganda)
            for ($i = 1; $i <= 10; $i++) {
                Soal::create([
                    'bank_soal_id' => $bankSoal->id,
                    'tipe_soal' => 'pilihan_ganda',
                    'pertanyaan' => "Contoh pertanyaan ke-$i untuk mata pelajaran " . $mapel->nama . "?",
                    'opsi_a' => 'Pilihan Jawaban A',
                    'opsi_b' => 'Pilihan Jawaban B',
                    'opsi_c' => 'Pilihan Jawaban C',
                    'opsi_d' => 'Pilihan Jawaban D',
                    'opsi_e' => 'Pilihan Jawaban E',
                    'kunci_jawaban' => ['A', 'B', 'C', 'D', 'E'][rand(0, 4)],
                    'bobot' => 10,
                    'urutan' => $i,
                ]);
            }

            // 3. Create Jadwal Ujian
            $mpk = MataPelajaranKelas::where('mata_pelajaran_id', $mapel->id)->first();
            if ($mpk) {
                $komponenUTS = \App\Models\KomponenNilai::where('kode', 'UTS')->first();

                $jadwals = [
                    [
                        'jenis_ujian' => 'ulangan_harian',
                        'nama_ujian' => 'UH 1 ' . $mapel->nama,
                        'komponen_nilai_id' => null, // Test Fuzzy Match
                    ],
                    [
                        'jenis_ujian' => 'uts',
                        'nama_ujian' => 'UTS Ganjil ' . $mapel->nama,
                        'komponen_nilai_id' => $komponenUTS->id ?? null, // Test Explicit Match
                    ],
                    [
                        'jenis_ujian' => 'uas',
                        'nama_ujian' => 'UAS Ganjil ' . $mapel->nama,
                        'komponen_nilai_id' => null, // Test Fuzzy Match
                    ]
                ];

                foreach($jadwals as $jData) {
                    $jadwal = JadwalUjian::create(array_merge([
                        'semester_id' => $semester->id,
                        'mata_pelajaran_kelas_id' => $mpk->id,
                        'bank_soal_id' => $bankSoal->id,
                        'keterangan' => 'Ujian simulasi seeder',
                        'tanggal_mulai' => now()->subDay(),
                        'tanggal_selesai' => now()->addDays(2),
                        'durasi' => 60,
                        'jumlah_soal' => 10,
                        'acak_soal' => true,
                        'acak_opsi' => true,
                        'tampilkan_nilai' => true,
                        'token' => strtoupper(Str::random(6)),
                        'status' => 'aktif',
                    ], $jData));

                    // --- NEW: Populate SoalUjian (Fix empty questions) ---
                    $soals = Soal::where('bank_soal_id', $bankSoal->id)->limit(10)->get();
                    foreach ($soals as $index => $soal) {
                        \App\Models\SoalUjian::create([
                            'jadwal_ujian_id' => $jadwal->id,
                            'soal_id' => $soal->id,
                            'urutan' => $index + 1,
                        ]);
                    }

                    // 4. Create sample UjianSiswa for testing
                    $students = \App\Models\SiswaKelas::where('kelas_id', $mpk->kelas_id)->limit(3)->get();
                    foreach($students as $sk) {
                        \App\Models\UjianSiswa::create([
                            'jadwal_ujian_id' => $jadwal->id,
                            'siswa_id' => $sk->siswa_id,
                            'status' => 'belum_mulai',
                            'token_siswa' => $jadwal->token,
                            'session_id' => null,
                            'ip_address' => null,
                        ]);

                        // --- NEW: Create Today's Attendance (Fix Blocked Access) ---
                        // \App\Models\PresensiSiswa::updateOrCreate(
                        //     [
                        //         'siswa_id' => $sk->siswa_id,
                        //         'kelas_id' => $mpk->kelas_id,
                        //         'tanggal' => \Carbon\Carbon::today(),
                        //     ],
                        //     [
                        //         'status' => 'H', // Hadir
                        //         'keterangan' => 'Hadir (Seeded for Testing)',
                        //         'user_id' => 1,
                        //     ]
                        // );
                    }
                }
            }
        }
    }
}
