<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jawaban_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_siswa_id')->constrained('ujian_siswa')->onDelete('cascade');
            $table->foreignId('soal_ujian_id')->constrained('soal_ujian')->onDelete('cascade');
            $table->text('jawaban')->nullable();
            // Untuk PG Kompleks & Menjodohkan, simpan detail per item
            $table->json('jawaban_detail')->nullable()->comment('Detail jawaban untuk tipe kompleks');
            $table->boolean('is_benar')->nullable();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->dateTime('waktu_jawab')->nullable();
            $table->boolean('ragu_ragu')->default(false);
            // Untuk essay: catatan koreksi guru
            $table->text('catatan_koreksi')->nullable();
            $table->timestamps();

            $table->unique(['ujian_siswa_id', 'soal_ujian_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_siswa');
    }
};
