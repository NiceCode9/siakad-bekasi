<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semester', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademik')->onDelete('cascade');
            $table->enum('nama', ['Ganjil', 'Genap']);
            $table->string('kode', 20)->unique();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->index('is_active');
            $table->unique(['tahun_akademik_id', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semester');
    }
};
