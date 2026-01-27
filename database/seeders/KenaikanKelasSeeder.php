<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KenaikanKelas;
use App\Models\TahunAkademik;
use App\Models\User;

class KenaikanKelasSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = TahunAkademik::where('is_active', true)->first();
        if (!$tahun) return;

        KenaikanKelas::create([
            'tahun_akademik_id' => $tahun->id,
            'tanggal_proses' => now(),
            'status' => 'draft',
            'total_siswa' => 0,
            'total_naik' => 0,
            'total_tidak_naik' => 0,
            'processed_by' => User::first()->id,
            'keterangan' => 'Uji coba proses kenaikan kelas',
        ]);
    }
}
