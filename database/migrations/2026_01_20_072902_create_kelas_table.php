<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('semester')->onDelete('cascade');
            $table->foreignId('jurusan_id')->constrained('jurusan')->onDelete('restrict');
            $table->enum('tingkat', ['X', 'XI', 'XII']);
            $table->string('nama', 50);
            $table->string('kode', 20)->unique();
            $table->foreignId('wali_kelas_id')->nullable()->constrained('guru')->onDelete('set null');
            $table->integer('kuota')->default(36);
            $table->string('ruang_kelas', 50)->nullable();
            $table->timestamps();

            $table->index('semester_id');
            $table->index('tingkat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
