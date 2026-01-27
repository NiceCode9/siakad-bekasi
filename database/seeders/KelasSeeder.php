<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeSemester = Semester::where('is_active', true)->first();
        $jurusans = Jurusan::all();
        $gurus = Guru::all();

        $kelasList = [];
        $guruIndex = 0;

        foreach ($jurusans as $jurusan) {
            // Tingkat X (Grade 10)
            $kelasList[] = [
                'semester_id' => $activeSemester->id,
                'jurusan_id' => $jurusan->id,
                'nama' => 'X ' . $jurusan->singkatan . ' 1',
                'kode' => 'X-' . $jurusan->kode . '-1',
                'tingkat' => 'X',
                'wali_kelas_id' => $gurus[$guruIndex % $gurus->count()]->id,
                'ruang_kelas' => 'R-' . (count($kelasList) + 1),
            ];
            $guruIndex++;

            // Tingkat XI (Grade 11)
            $kelasList[] = [
                'semester_id' => $activeSemester->id,
                'jurusan_id' => $jurusan->id,
                'nama' => 'XI ' . $jurusan->singkatan . ' 1',
                'kode' => 'XI-' . $jurusan->kode . '-1',
                'tingkat' => 'XI',
                'wali_kelas_id' => $gurus[$guruIndex % $gurus->count()]->id,
                'ruang_kelas' => 'R-' . (count($kelasList) + 1),
            ];
            $guruIndex++;

            // Tingkat XII (Grade 12)
            $kelasList[] = [
                'semester_id' => $activeSemester->id,
                'jurusan_id' => $jurusan->id,
                'nama' => 'XII ' . $jurusan->singkatan . ' 1',
                'kode' => 'XII-' . $jurusan->kode . '-1',
                'tingkat' => 'XII',
                'wali_kelas_id' => $gurus[$guruIndex % $gurus->count()]->id,
                'ruang_kelas' => 'R-' . (count($kelasList) + 1),
            ];
            $guruIndex++;
        }

        // Add additional parallel classes for popular majors (RPL, TKJ)
        $rpl = Jurusan::where('kode', 'RPL')->first();
        $tkj = Jurusan::where('kode', 'TKJ')->first();

        if ($rpl) {
            $kelasList[] = [
                'semester_id' => $activeSemester->id,
                'jurusan_id' => $rpl->id,
                'nama' => 'X ' . $rpl->singkatan . ' 2',
                'kode' => 'X-' . $rpl->kode . '-2',
                'tingkat' => 'X',
                'wali_kelas_id' => $gurus[$guruIndex % $gurus->count()]->id,
                'ruang_kelas' => 'R-' . (count($kelasList) + 1),
            ];
            $guruIndex++;

            $kelasList[] = [
                'semester_id' => $activeSemester->id,
                'jurusan_id' => $rpl->id,
                'nama' => 'XI ' . $rpl->singkatan . ' 2',
                'kode' => 'XI-' . $rpl->kode . '-2',
                'tingkat' => 'XI',
                'wali_kelas_id' => $gurus[$guruIndex % $gurus->count()]->id,
                'ruang_kelas' => 'R-' . (count($kelasList) + 1),
            ];
            $guruIndex++;
        }

        if ($tkj) {
            $kelasList[] = [
                'semester_id' => $activeSemester->id,
                'jurusan_id' => $tkj->id,
                'nama' => 'X ' . $tkj->singkatan . ' 2',
                'kode' => 'X-' . $tkj->kode . '-2',
                'tingkat' => 'X',
                'wali_kelas_id' => $gurus[$guruIndex % $gurus->count()]->id,
                'ruang_kelas' => 'R-' . (count($kelasList) + 1),
            ];
        }

        foreach ($kelasList as $kelas) {
            Kelas::create($kelas);
        }
    }
}
