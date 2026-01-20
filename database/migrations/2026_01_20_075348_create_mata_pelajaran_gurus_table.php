<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_pelajaran_guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelajaran_kelas_id')->constrained('mata_pelajaran_kelas')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('guru')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['mata_pelajaran_kelas_id', 'guru_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_pelajaran_guru');
    }
};
