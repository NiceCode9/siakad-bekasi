<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('presensi_mapel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_mengajar_id')->constrained('jurnal_mengajar')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->enum('status', ['H', 'I', 'S', 'A'])->default('H')->comment('H=Hadir, I=Izin, S=Sakit, A=Alpha');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['jurnal_mengajar_id', 'siswa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_mapel');
    }
};
