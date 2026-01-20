<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pkl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semester')->onDelete('cascade');
            $table->foreignId('perusahaan_pkl_id')->constrained('perusahaan_pkl')->onDelete('restrict');
            $table->foreignId('pembimbing_sekolah_id')->constrained('guru')->onDelete('restrict');
            $table->string('pembimbing_industri', 100)->nullable();
            $table->string('jabatan_pembimbing_industri', 50)->nullable();
            $table->string('telepon_pembimbing_industri', 20)->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('posisi', 100)->nullable();
            $table->string('divisi', 100)->nullable();
            $table->enum('status', ['pending', 'aktif', 'selesai', 'batal'])->default('pending');
            $table->timestamps();

            $table->index('siswa_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pkl');
    }
};
