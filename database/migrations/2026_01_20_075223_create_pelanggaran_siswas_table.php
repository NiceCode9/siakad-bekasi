<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggaran_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('jenis_pelanggaran', 200);
            $table->enum('kategori', ['ringan', 'sedang', 'berat']);
            $table->integer('poin')->default(0);
            $table->text('kronologi')->nullable();
            $table->text('sanksi')->nullable();
            $table->foreignId('pelapor_id')->nullable()->constrained('guru')->onDelete('set null');
            $table->enum('status', ['proses', 'selesai'])->default('proses');
            $table->timestamps();

            $table->index('siswa_id');
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggaran_siswa');
    }
};
