<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_pelajaran_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->integer('jam_per_minggu')->default(2);
            $table->timestamps();

            $table->unique(['mata_pelajaran_id', 'kelas_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_pelajaran_kelas');
    }
};
