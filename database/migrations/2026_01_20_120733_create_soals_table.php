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
            $table->enum('tipe_soal', [
                'pilihan_ganda',
                'pilihan_ganda_kompleks',
                'menjodohkan',
                'isian_singkat',
                'uraian'
            ]);
            $table->text('pertanyaan');
            $table->text('opsi_a')->nullable();
            $table->text('opsi_b')->nullable();
            $table->text('opsi_c')->nullable();
            $table->text('opsi_d')->nullable();
            $table->text('opsi_e')->nullable();
            $table->text('kunci_jawaban')->nullable();
            $table->decimal('bobot', 5, 2)->default(1.00);
            $table->text('pembahasan')->nullable();
            $table->string('gambar')->nullable();
            $table->integer('urutan')->default(0);
            // Metadata tambahan untuk validasi
            $table->json('metadata')->nullable()->comment('Config tambahan per tipe soal');
            $table->timestamps();

            $table->index('bank_soal_id');
            $table->index('tipe_soal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soal');
    }
};
