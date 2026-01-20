<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku_induk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->unique()->constrained('siswa')->onDelete('cascade');
            $table->string('nomor_induk', 50)->unique();
            $table->string('nomor_peserta_ujian', 50)->nullable();
            $table->string('nomor_seri_ijazah', 50)->nullable();
            $table->string('nomor_seri_skhun', 50)->nullable();
            $table->date('tanggal_lulus')->nullable();
            $table->text('riwayat_pendidikan')->nullable();
            $table->text('riwayat_kesehatan')->nullable();
            $table->text('catatan_khusus')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku_induk');
    }
};
