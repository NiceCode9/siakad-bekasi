<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_soal_id')->constrained('bank_soal')->onDelete('cascade');
            $table->enum('tipe_soal', ['pilihan_ganda', 'essay', 'benar_salah', 'menjodohkan']);
            $table->text('pertanyaan');
            $table->text('opsi_a')->nullable();
            $table->text('opsi_b')->nullable();
            $table->text('opsi_c')->nullable();
            $table->text('opsi_d')->nullable();
            $table->text('opsi_e')->nullable();
            $table->string('kunci_jawaban')->nullable();
            $table->decimal('bobot', 5, 2)->default(1.00);
            $table->text('pembahasan')->nullable();
            $table->string('gambar')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->index('bank_soal_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soal');
    }
};
