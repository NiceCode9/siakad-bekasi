<?php

namespace App\Services;

use App\Models\UjianSiswa;
use App\Models\JadwalUjian;
use App\Models\Nilai;
use App\Models\KomponenNilai;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GradeSyncService
{
    /**
     * Synchronize a student's exam score to the grading (Nilai) system.
     */
    public function syncExamScore(UjianSiswa $ujianSiswa)
    {
        $jadwal = $ujianSiswa->jadwalUjian;
        if (!$jadwal) return false;

        $finalScore = $ujianSiswa->nilai;
        $semester = $jadwal->semester;

        // 1. Resolve Component
        $komponen = $this->resolveKomponenNilai($jadwal);
        if (!$komponen) {
            Log::warning("GradeSync: Could not resolve KomponenNilai for JadwalUjian ID: {$jadwal->id}");
            return false;
        }

        // 2. Map jenis_nilai (Sync with Nilai table enum)
        $mapJenis = [
            'ulangan_harian' => 'ulangan_harian',
            'uts' => 'uts',
            'uas' => 'uas',
            'ujian_praktik' => 'praktik',
            'ujian_sekolah' => 'uas',
        ];
        $jenisNilai = $mapJenis[$jadwal->jenis_ujian] ?? 'lainnya';

        // 3. Manual Entry Protection
        // Check if a record exists that was NOT created by CBT (ujian_siswa_id is null)
        $existingManual = Nilai::where([
            'siswa_id' => $ujianSiswa->siswa_id,
            'mata_pelajaran_kelas_id' => $jadwal->mata_pelajaran_kelas_id,
            'komponen_nilai_id' => $komponen->id,
            'semester_id' => $jadwal->semester_id,
        ])->whereNull('ujian_siswa_id')->first();

        if ($existingManual) {
            Log::info("GradeSync: Skipped sync for Siswa ID: {$ujianSiswa->siswa_id} because a manual grade already exists.");
            return false;
        }

        // 4. Update or Create Nilai
        Nilai::updateOrCreate(
            [
                'siswa_id' => $ujianSiswa->siswa_id,
                'mata_pelajaran_kelas_id' => $jadwal->mata_pelajaran_kelas_id,
                'komponen_nilai_id' => $komponen->id,
                'semester_id' => $jadwal->semester_id,
            ],
            [
                'jenis_nilai' => $jenisNilai,
                'nilai' => $finalScore,
                'ujian_siswa_id' => $ujianSiswa->id,
                'penginput_id' => $jadwal->mataPelajaranKelas->guru_id ?? 1, // Fallback to ID 1 if guru not found
                'tanggal_input' => now(),
                'keterangan' => 'Nilai otomatis dari CBT: ' . $jadwal->nama_ujian
            ]
        );

        return true;
    }

    /**
     * Resolve the KomponenNilai using explicit mapping or fuzzy search.
     */
    public function resolveKomponenNilai(JadwalUjian $jadwal)
    {
        // 1. Explicit Mapping
        if ($jadwal->komponen_nilai_id) {
            return $jadwal->komponenNilai;
        }

        // 2. Fuzzy Search
        $komponen = KomponenNilai::where('kurikulum_id', $jadwal->semester->tahunAkademik->kurikulum_id ?? 0)
            ->where(function($q) use ($jadwal) {
                $q->where('nama', 'like', '%' . $jadwal->jenis_ujian . '%')
                  ->orWhere('kode', 'like', '%' . $jadwal->jenis_ujian . '%');

                if ($jadwal->jenis_ujian == 'uts') $q->orWhere('nama', 'like', '%tengah%');
                if ($jadwal->jenis_ujian == 'uas') $q->orWhere('nama', 'like', '%akhir%');
            })->first();

        if (!$komponen) {
            $searchLabel = str_replace('_', ' ', $jadwal->jenis_ujian);
            $komponen = KomponenNilai::where('kurikulum_id', $jadwal->semester->tahunAkademik->kurikulum_id ?? 0)
                ->where('nama', 'like', '%' . $searchLabel . '%')
                ->first();
        }

        // 3. Persist the mapping if found to avoid repeated fuzzy search
        if ($komponen) {
            $jadwal->update(['komponen_nilai_id' => $komponen->id]);
        }

        return $komponen;
    }
}
