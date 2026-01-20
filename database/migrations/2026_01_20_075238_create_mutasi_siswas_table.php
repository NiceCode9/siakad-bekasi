<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->enum('jenis_mutasi', ['masuk', 'pindah', 'keluar', 'DO', 'lulus']);
            $table->date('tanggal');
            $table->string('dari_sekolah', 100)->nullable();
            $table->string('ke_sekolah', 100)->nullable();
            $table->text('alasan')->nullable();
            $table->string('nomor_surat', 50)->nullable();
            $table->string('file_surat')->nullable();
            $table->timestamps();

            $table->index('siswa_id');
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_siswa');
    }
};
