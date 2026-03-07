<?php

namespace Database\Seeders;

use App\Models\Nilai;
use App\Models\NilaiSikap;
use App\Models\NilaiEkstrakurikuler;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\MataPelajaranKelas;
use App\Models\KomponenNilai;
use App\Models\Ekstrakurikuler;
use App\Models\Semester;
use App\Models\Guru;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NilaiSeeder extends Seeder
{
    public function run(): void
    {
        $semester = Semester::active()->first();
        if (!$semester) return;

        $kurikulumId = $semester->tahunAkademik->kurikulum_id;
        $components = KomponenNilai::where('kurikulum_id', $kurikulumId)->get();
        if ($components->isEmpty()) return;

        $ekskuls = Ekstrakurikuler::all();
        $classes = Kelas::where('semester_id', $semester->id)->limit(3)->get();

        $validJenisNilai = ['tugas', 'ulangan_harian', 'uts', 'uas', 'praktik', 'proyek', 'lainnya'];

        foreach ($classes as $kelas) {
            $siswas = $kelas->siswa()->wherePivot('status', 'aktif')->get();
            $mpks = MataPelajaranKelas::where('kelas_id', $kelas->id)->get();

            foreach ($siswas as $siswa) {
                foreach ($mpks as $mpk) {
                    foreach ($components as $comp) {
                        // Skip UTS and UAS to allow for CBT sync testing
                        if (in_array($comp->kode, ['UTS', 'UAS'])) {
                            continue;
                        }

                        Nilai::create([
                            'siswa_id' => $siswa->id,
                            'mata_pelajaran_kelas_id' => $mpk->id,
                            'komponen_nilai_id' => $comp->id,
                            'semester_id' => $semester->id,
                            'nilai' => rand(75, 95),
                            'penginput_id' => $mpk->guru_id,
                            'tanggal_input' => now(),
                        ]);
                    }
                }

                // Attitude Grades
                NilaiSikap::create([
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $kelas->id,
                    'semester_id' => $semester->id,
                    'aspek' => 'spiritual',
                    'nilai' => 85, // Added numeric value
                    'predikat' => 'B',
                    'deskripsi' => 'Baik',
                    'penginput_id' => $kelas->wali_kelas_id ?? 1,
                ]);

                NilaiSikap::create([
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $kelas->id,
                    'semester_id' => $semester->id,
                    'aspek' => 'sosial',
                    'nilai' => 85, // Added numeric value
                    'predikat' => 'B',
                    'deskripsi' => 'Baik',
                    'penginput_id' => $kelas->wali_kelas_id ?? 1,
                ]);

                // Ekskul Grades
                if ($ekskuls->isNotEmpty()) {
                    NilaiEkstrakurikuler::create([
                        'siswa_id' => $siswa->id,
                        'semester_id' => $semester->id,
                        'ekstrakurikuler_id' => $ekskuls->random()->id,
                        'predikat' => 'A',
                        'keterangan' => 'Sangat Baik',
                        'nilai' => 90,
                    ]);
                }
            }
        }
    }
}
