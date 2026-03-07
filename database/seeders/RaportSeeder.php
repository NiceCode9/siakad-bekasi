<?php

namespace Database\Seeders;

use App\Models\Raport;
use App\Models\RaportDetail;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\MataPelajaranKelas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RaportSeeder extends Seeder
{
    public function run(): void
    {
        $semester = Semester::where('is_active', true)->first();
        if (!$semester) {
            $this->command->info('No active semester found, skipping RaportSeeder.');
            return;
        }

        $kurikulumId = $semester->tahunAkademik->kurikulum_id;
        $components = \App\Models\KomponenNilai::where('kurikulum_id', $kurikulumId)->get();
        if ($components->isEmpty()) return;

        // Get all classes in this semester
        $classes = Kelas::where('semester_id', $semester->id)->get();

        foreach ($classes as $kelas) {
            $students = $kelas->siswa()->wherePivot('status', 'aktif')->get();
            $subjects = MataPelajaranKelas::where('kelas_id', $kelas->id)->get();

            if ($students->isEmpty() || $subjects->isEmpty()) {
                continue;
            }

            foreach ($students as $student) {
                foreach ($components as $comp) {
                    // Check if raport already exists
                    $raport = Raport::firstOrCreate(
                        [
                            'siswa_id' => $student->id,
                            'semester_id' => $semester->id,
                            'kelas_id' => $kelas->id,
                            'komponen_nilai_id' => $comp->id,
                        ],
                        [
                            'jumlah_sakit' => rand(0, 2),
                            'jumlah_izin' => rand(0, 3),
                            'jumlah_alpha' => rand(0, 1),
                            'catatan_wali_kelas' => 'Terus tingkatkan prestasimu, semangat belajar!',
                            'tanggal_generate' => now(),
                            'status' => 'published',
                            'approved_by' => 1, // Super Admin
                            'approved_at' => now(),
                        ]
                    );

                    foreach ($subjects as $mpk) {
                        $nilaiPengetahuan = rand(75, 95);
                        $nilaiKeterampilan = rand(75, 95);
                        $nilaiAkhir = ($nilaiPengetahuan + $nilaiKeterampilan) / 2;
                        
                        RaportDetail::updateOrCreate(
                            [
                                'raport_id' => $raport->id,
                                'mata_pelajaran_id' => $mpk->mata_pelajaran_id,
                            ],
                            [
                                'nilai_pengetahuan' => $nilaiPengetahuan,
                                'nilai_keterampilan' => $nilaiKeterampilan,
                                'nilai_akhir' => $nilaiAkhir,
                                'predikat' => $this->konversiPredikat($nilaiAkhir),
                                'deskripsi' => 'Menunjukkan penguasaan yang sangat baik dalam materi pelajaran ini.',
                                'jumlah_pertemuan' => 18,
                                'jumlah_hadir' => rand(16, 18),
                                'persentase_kehadiran' => 95.00,
                            ]
                        );
                    }
                }
            }
        }

        $this->command->info('RaportSeeder finished successfully.');
    }

    private function konversiPredikat($nilai)
    {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        return 'D';
    }
}
