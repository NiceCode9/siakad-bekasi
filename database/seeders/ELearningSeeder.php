<?php

namespace Database\Seeders;

use App\Models\MateriAjar;
use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use App\Models\ForumDiskusi;
use App\Models\ForumKomentar;
use App\Models\MataPelajaranKelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;

class ELearningSeeder extends Seeder
{
    public function run(): void
    {
        $mpks = MataPelajaranKelas::limit(5)->get();
        if ($mpks->isEmpty()) return;

        $siswas = Siswa::whereIn('id', function($q) {
            $q->select('siswa_id')->from('siswa_kelas');
        })->limit(5)->get();

        foreach ($mpks as $mpk) {
            $teacherUser = $mpk->guru->user;

            // 1. Create Materi Ajar
            MateriAjar::create([
                'mata_pelajaran_kelas_id' => $mpk->id,
                'judul' => 'Pengenalan ' . $mpk->mataPelajaran->nama,
                'deskripsi' => 'Materi pendahuluan dasar-dasar ' . $mpk->mataPelajaran->nama,
                'tipe' => 'link',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'is_published' => true,
                'tanggal_publish' => now(),
            ]);

            // 2. Create Tugas
            $tugas = Tugas::create([
                'mata_pelajaran_kelas_id' => $mpk->id,
                'judul' => 'Tugas Mandiri 1: ' . $mpk->mataPelajaran->nama,
                'deskripsi' => 'Kerjakan modul halaman 1-10 dan kumpulkan dalam format PDF.',
                'tanggal_buat' => now(),
                'tanggal_deadline' => now()->addWeek(),
                'bobot' => 10,
                'is_published' => true,
            ]);

            // Create some submissions
            foreach ($siswas as $siswa) {
                if (rand(0, 1)) {
                    PengumpulanTugas::create([
                        'tugas_id' => $tugas->id,
                        'siswa_id' => $siswa->id,
                        'jawaban' => 'Saya sudah mengerjakan tugas ini, Pak.',
                        'tanggal_submit' => now(),
                        'status' => 'tepat_waktu',
                    ]);
                }
            }

            // 3. Create Forum Diskusi
            $forum = ForumDiskusi::create([
                'mata_pelajaran_kelas_id' => $mpk->id,
                'pembuat_id' => $teacherUser->id,
                'judul' => 'Diskusi Materi Pekan 1 ' . $mpk->mataPelajaran->nama,
                'konten' => 'Silakan tanyakan hal-hal yang belum dipahami dari materi yang sudah diberikan.',
                'is_pinned' => true,
            ]);

            // Add some comments
            foreach ($siswas as $siswa) {
                ForumKomentar::create([
                    'forum_diskusi_id' => $forum->id,
                    'user_id' => $siswa->user->id,
                    'konten' => 'Pak, untuk bagian sub-bab 2 apakah ada video penjelasannya?',
                ]);
            }
        }
    }
}
