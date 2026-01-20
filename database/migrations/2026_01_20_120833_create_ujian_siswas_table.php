<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ujian_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ujian_id')->constrained('jadwal_ujian')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->string('token_siswa', 50)->nullable();
            $table->dateTime('waktu_mulai')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
            $table->dateTime('waktu_submit')->nullable();
            $table->integer('sisa_waktu')->nullable()->comment('dalam detik');
            $table->enum('status', ['belum_mulai', 'sedang_mengerjakan', 'selesai', 'tidak_hadir'])->default('belum_mulai');
            $table->decimal('nilai', 5, 2)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('siswa_id');
            $table->index('status');
            $table->unique(['jadwal_ujian_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian_siswa');
    }
};
