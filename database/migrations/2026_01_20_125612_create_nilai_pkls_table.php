<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_pkl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_id')->unique()->constrained('pkl')->onDelete('cascade');
            $table->decimal('nilai_sikap_kerja', 5, 2)->nullable()->comment('bobot 30%');
            $table->decimal('nilai_keterampilan', 5, 2)->nullable()->comment('bobot 40%');
            $table->decimal('nilai_laporan', 5, 2)->nullable()->comment('bobot 30%');
            $table->decimal('nilai_dari_industri', 5, 2)->nullable()->comment('dari pembimbing industri');
            $table->decimal('nilai_dari_sekolah', 5, 2)->nullable()->comment('dari pembimbing sekolah');
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->text('catatan_industri')->nullable();
            $table->text('catatan_sekolah')->nullable();
            $table->string('file_laporan')->nullable();
            $table->date('tanggal_penilaian')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_pkl');
    }
};
