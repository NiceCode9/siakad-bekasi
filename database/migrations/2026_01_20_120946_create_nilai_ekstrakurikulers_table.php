<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semester')->onDelete('cascade');
            $table->string('nama_ekskul', 100);
            $table->decimal('nilai', 5, 2)->nullable();
            $table->string('predikat', 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['siswa_id', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_ekstrakurikuler');
    }
};
