<?php

namespace Database\Seeders;

use App\Models\MataPelajaranKelas;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Jurusan;
use Illuminate\Database\Seeder;

class MataPelajaranKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelas = Kelas::all();
        $gurus = Guru::all();

        // Mapping guru berdasarkan spesialisasi (simplified)
        $guruBySubject = [
            'PAI' => $gurus->random(),
            'PKN' => $gurus->random(),
            'BIND' => $gurus->random(),
            'MTK' => $gurus->random(),
            'SEJ' => $gurus->random(),
            'BING' => $gurus->random(),
            'SBD' => $gurus->random(),
            'PJOK' => $gurus->random(),
            'BJAWA' => $gurus->random(),
            'SIM' => $gurus->random(),
            'FIS' => $gurus->random(),
            'KIM' => $gurus->random(),
            'SKKNI' => $gurus->random(),
            'KOMJAR' => $gurus->random(),
            'PROGDAS' => $gurus->random(),
            'DDGRAF' => $gurus->random(),
            'PBO' => $gurus->random(),
            'BASDAT' => $gurus->random(),
            'PWEB' => $gurus->random(),
            'PMOBILE' => $gurus->random(),
            'PPL' => $gurus->random(),
            'ADMSRV' => $gurus->random(),
            'ADMSYS' => $gurus->random(),
            'DIAGJAR' => $gurus->random(),
            'ANIMASI2D' => $gurus->random(),
            'DESGRAF' => $gurus->random(),
            'PRODVID' => $gurus->random(),
        ];

        foreach ($kelas as $k) {
            $jurusan = $k->jurusan;
            $tingkat = $k->tingkat;

            // Mata Pelajaran Umum (Kelompok A & B) - untuk semua kelas
            $umum = MataPelajaran::whereIn('kelompok_mapel_id', [1, 2])->get();
            foreach ($umum as $mapel) {
                MataPelajaranKelas::create([
                    'mata_pelajaran_id' => $mapel->id,
                    'kelas_id' => $k->id,
                    'guru_id' => $guruBySubject[$mapel->kode]->id ?? $gurus->random()->id,
                    'jam_per_minggu' => $this->getJamPerMinggu($mapel->kode),
                ]);
            }

            // Mata Pelajaran Kejuruan C1 (Dasar Bidang Keahlian) - untuk semua kelas IT
            $c1 = MataPelajaran::where('kelompok_mapel_id', 3)->get();
            foreach ($c1 as $mapel) {
                MataPelajaranKelas::create([
                    'mata_pelajaran_id' => $mapel->id,
                    'kelas_id' => $k->id,
                    'guru_id' => $guruBySubject[$mapel->kode]->id ?? $gurus->random()->id,
                    'jam_per_minggu' => $this->getJamPerMinggu($mapel->kode),
                ]);
            }

            // Mata Pelajaran C2 (Dasar Program Keahlian) - untuk semua kelas IT
            $c2 = MataPelajaran::where('kelompok_mapel_id', 4)->get();
            foreach ($c2 as $mapel) {
                MataPelajaranKelas::create([
                    'mata_pelajaran_id' => $mapel->id,
                    'kelas_id' => $k->id,
                    'guru_id' => $guruBySubject[$mapel->kode]->id ?? $gurus->random()->id,
                    'jam_per_minggu' => $this->getJamPerMinggu($mapel->kode),
                ]);
            }

            // Mata Pelajaran C3 (Kompetensi Keahlian) - sesuai jurusan
            if ($tingkat !== 'X') { // C3 hanya untuk kelas XI dan XII
                $mapelC3 = $this->getMapelByJurusan($jurusan->kode);
                foreach ($mapelC3 as $kodeMapel) {
                    $mapel = MataPelajaran::where('kode', $kodeMapel)->first();
                    if ($mapel) {
                        MataPelajaranKelas::create([
                            'mata_pelajaran_id' => $mapel->id,
                            'kelas_id' => $k->id,
                            'guru_id' => $guruBySubject[$mapel->kode]->id ?? $gurus->random()->id,
                            'jam_per_minggu' => $this->getJamPerMinggu($mapel->kode),
                        ]);
                    }
                }
            }
        }
    }

    private function getMapelByJurusan($kodeJurusan)
    {
        return match ($kodeJurusan) {
            'RPL' => ['PBO', 'BASDAT', 'PWEB', 'PMOBILE', 'PPL'],
            'TKJ' => ['ADMSRV', 'ADMSYS', 'DIAGJAR', 'PPL'],
            'MM' => ['ANIMASI2D', 'DESGRAF', 'PRODVID', 'PPL'],
            default => [],
        };
    }

    private function getJamPerMinggu($kodeMapel)
    {
        // Simplified jam per minggu
        return match ($kodeMapel) {
            'PAI', 'PKN', 'BIND', 'MTK', 'BING' => 4,
            'SEJ', 'SBD', 'PJOK', 'BJAWA' => 2,
            'SIM', 'FIS', 'KIM' => 3,
            'SKKNI', 'KOMJAR', 'PROGDAS', 'DDGRAF' => 4,
            'PBO', 'BASDAT', 'PWEB', 'PMOBILE', 'PPL' => 6,
            'ADMSRV', 'ADMSYS', 'DIAGJAR' => 6,
            'ANIMASI2D', 'DESGRAF', 'PRODVID' => 6,
            default => 2,
        };
    }
}
