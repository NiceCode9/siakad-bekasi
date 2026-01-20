<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_ujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('semester')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_kelas_id')->constrained('mata_pelajaran_kelas')->onDelete('cascade');
            $table->foreignId('bank_soal_id')->constrained('bank_soal')->onDelete('restrict');
            $table->enum('jenis_ujian', ['ulangan_harian', 'uts', 'uas', 'ujian_praktik', 'ujian_sekolah']);
            $table->string('nama_ujian', 200);
            $table->text('keterangan')->nullable();
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai');
            $table->integer('durasi')->comment('dalam menit');
            $table->integer('jumlah_soal');
            $table->boolean('acak_soal')->default(true);
            $table->boolean('acak_opsi')->default(true);
            $table->boolean('tampilkan_nilai')->default(false);
            $table->string('token', 10)->nullable();
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft');
            $table->timestamps();

            $table->index(['tanggal_mulai', 'tanggal_selesai']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_ujian');
    }
};
