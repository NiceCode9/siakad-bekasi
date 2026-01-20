<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_kelas_id')->constrained('mata_pelajaran_kelas')->onDelete('cascade');
            $table->foreignId('komponen_nilai_id')->constrained('komponen_nilai')->onDelete('restrict');
            $table->foreignId('semester_id')->constrained('semester')->onDelete('cascade');
            $table->enum('jenis_nilai', ['tugas', 'ulangan_harian', 'uts', 'uas', 'praktik', 'proyek', 'lainnya']);
            $table->decimal('nilai', 5, 2);
            $table->text('keterangan')->nullable();
            $table->foreignId('ujian_siswa_id')->nullable()->constrained('ujian_siswa')->onDelete('set null')->comment('jika nilai dari CBT');
            $table->foreignId('penginput_id')->constrained('guru')->onDelete('restrict');
            $table->date('tanggal_input');
            $table->timestamps();

            $table->index('siswa_id');
            $table->index('mata_pelajaran_kelas_id');
            $table->index('semester_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai');
    }
};
