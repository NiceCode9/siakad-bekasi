<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semester')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->nullable()->constrained('mata_pelajaran')->onDelete('cascade')->comment('NULL jika legger semua mapel');
            $table->date('tanggal_generate');
            $table->foreignId('generated_by')->constrained('guru')->onDelete('restrict');
            $table->string('file_path')->nullable();
            $table->timestamps();

            $table->index(['kelas_id', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legger');
    }
};
