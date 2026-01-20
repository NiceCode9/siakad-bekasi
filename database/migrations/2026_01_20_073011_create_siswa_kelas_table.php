<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->enum('status', ['aktif', 'pindah', 'keluar'])->default('aktif');
            $table->timestamps();

            $table->index('siswa_id');
            $table->index('kelas_id');
            $table->unique(['siswa_id', 'kelas_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_kelas');
    }
};
