<?php

namespace Database\Seeders;

use App\Models\PresensiSiswa;
use App\Models\JurnalMengajar;
use App\Models\PresensiMapel;
use App\Models\PrestasiSiswa;
use App\Models\PelanggaranSiswa;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\JadwalPelajaran;
use App\Models\Guru;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class OperasionalSeeder extends Seeder
{
    public function run(): void
    {
        $semester = Semester::active()->first();
        if (!$semester) return;

        $gurus = Guru::limit(5)->get();
        $classes = Kelas::where('semester_id', $semester->id)->get();
        
        foreach ($classes as $kelas) {
            $siswas = $kelas->siswa()->wherePivot('status', 'aktif')->get();
            if ($siswas->isEmpty()) continue;

            // 1. Presensi Siswa (Daily) - Last 5 days
            for ($i = 0; $i < 5; $i++) {
                $tanggal = now()->subDays($i);
                if ($tanggal->isWeekend()) continue;

                foreach ($siswas as $siswa) {
                    PresensiSiswa::create([
                        'siswa_id' => $siswa->id,
                        'kelas_id' => $kelas->id,
                        'tanggal' => $tanggal->format('Y-m-d'),
                        'status' => ['H', 'H', 'H', 'I', 'S', 'A'][rand(0, 5)], // Mostly Hadir
                        'user_id' => $kelas->wali_kelas_id ? Guru::find($kelas->wali_kelas_id)->user_id : $gurus->random()->user_id,
                    ]);
                }
            }

            // 2. Jurnal Mengajar & Presensi Mapel
            $jadwals = JadwalPelajaran::whereHas('mataPelajaranKelas', function($q) use ($kelas) {
                $q->where('kelas_id', $kelas->id);
            })->get();

            foreach ($jadwals as $jadwal) {
                $jurnal = JurnalMengajar::create([
                    'jadwal_pelajaran_id' => $jadwal->id,
                    'tanggal' => now()->subDays(rand(1, 10)),
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai,
                    'materi' => 'Pembahasan bab ' . rand(1, 5),
                    'metode_pembelajaran' => 'Ceramah dan Diskusi',
                    'jumlah_hadir' => $siswas->count(),
                    'jumlah_tidak_hadir' => 0,
                    'is_approved' => true,
                ]);

                // Create individual mapel attendance for each student in that journal
                foreach ($siswas as $siswa) {
                    PresensiMapel::create([
                        'jurnal_mengajar_id' => $jurnal->id,
                        'siswa_id' => $siswa->id,
                        'status' => 'H',
                    ]);
                }
            }

            // 3. Student Affairs (Prestasi & Pelanggaran)
            $randomSiswa = $siswas->random();
            PrestasiSiswa::create([
                'siswa_id' => $randomSiswa->id,
                'jenis' => 'akademik',
                'nama_prestasi' => 'Juara 1 Lomba LKS',
                'tingkat' => 'provinsi',
                'peringkat' => '1',
                'penyelenggara' => 'Dinas Pendidikan',
                'tanggal' => now()->subMonths(1),
            ]);

            $randomSiswa2 = $siswas->random();
            PelanggaranSiswa::create([
                'siswa_id' => $randomSiswa2->id,
                'tanggal' => now()->subDays(2),
                'jenis_pelanggaran' => 'Terlambat Masuk Sekolah',
                'kategori' => 'ringan',
                'poin' => 5,
                'status' => 'selesai',
                'pelapor_id' => $gurus->random()->id,
            ]);
        }
    }
}
