<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soal_opsi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soal_id')->constrained('soal')->onDelete('cascade');

            // Tipe opsi:
            // - 'pernyataan' untuk PG Kompleks
            // - 'kolom_kiri' untuk Menjodohkan (pertanyaan)
            // - 'kolom_kanan' untuk Menjodohkan (jawaban)
            $table->enum('tipe', ['pernyataan', 'kolom_kiri', 'kolom_kanan']);

            $table->integer('urutan')->default(0);
            $table->text('teks');

            // Untuk PG Kompleks: apakah pernyataan ini benar?
            $table->boolean('is_benar')->nullable();

            // Untuk Menjodohkan: ID pasangan yang benar
            $table->unsignedBigInteger('pasangan_id')->nullable();

            $table->timestamps();

            $table->index('soal_id');
            $table->index(['soal_id', 'tipe']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soal_opsi');
    }
};
