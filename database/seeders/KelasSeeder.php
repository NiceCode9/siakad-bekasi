<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Semester;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $semester = Semester::where('is_active', true)->first();

        if (!$semester) {
            return;
        }

        $jurusan = Jurusan::where('is_active', true)->get();

        $tingkat = ['X', 'XI', 'XII'];
        $nomor = ['1', '2', '3'];

        foreach ($jurusan as $jrs) {
            foreach ($tingkat as $tgt) {
                foreach ($nomor as $nm) {
                    Kelas::create([
                        'semester_id' => $semester->id,
                        'jurusan_id' => $jrs->id,
                        'kode' => $tgt . ' ' . $jrs->singkatan . ' ' . $nm,
                        'nama' => $tgt . ' ' . $jrs->singkatan . ' ' . $nm,
                        'tingkat' => $tgt,
                        'kuota' => 30,
                        'ruang_kelas' => '',
                    ]);
                }
            }
        }
    }
}
