<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestasi_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->enum('jenis', ['akademik', 'non_akademik', 'olahraga', 'seni', 'lainnya']);
            $table->string('nama_prestasi', 200);
            $table->enum('tingkat', ['kelas', 'sekolah', 'kecamatan', 'kota', 'provinsi', 'nasional', 'internasional']);
            $table->string('peringkat', 50)->nullable();
            $table->string('penyelenggara', 100)->nullable();
            $table->date('tanggal')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('file_sertifikat')->nullable();
            $table->timestamps();

            $table->index('siswa_id');
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasi_siswa');
    }
};
