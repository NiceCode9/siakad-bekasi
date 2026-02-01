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
                'tingkat_kesulitan' => ['Mudah', 'Sedang', 'Sulit'][rand(0, 2)],
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

            // 3. Create Jadwal Ujian if there's a MataPelajaranKelas linked to this mapel
            $mpk = MataPelajaranKelas::where('mata_pelajaran_id', $mapel->id)->first();
            if ($mpk) {
                JadwalUjian::create([
                    'semester_id' => $semester->id,
                    'mata_pelajaran_kelas_id' => $mpk->id,
                    'bank_soal_id' => $bankSoal->id,
                    'jenis_ujian' => 'ulangan_harian',
                    'nama_ujian' => 'Ulangan Harian ' . $mapel->nama,
                    'keterangan' => 'Ujian bab 1',
                    'tanggal_mulai' => now(),
                    'tanggal_selesai' => now()->addDays(2),
                    'durasi' => 60,
                    'jumlah_soal' => 10,
                    'acak_soal' => true,
                    'acak_opsi' => true,
                    'tampilkan_nilai' => true,
                    'token' => strtoupper(Str::random(6)),
                    'status' => 'aktif',
                ]);
            }
        }
    }
}
