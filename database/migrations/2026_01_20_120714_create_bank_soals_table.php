<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->foreignId('pembuat_id')->constrained('guru')->onDelete('restrict');
            $table->string('kode', 50)->unique();
            $table->string('nama', 200);
            $table->text('deskripsi')->nullable();
            $table->enum('tingkat_kesulitan', ['mudah', 'sedang', 'sulit'])->default('sedang');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('mata_pelajaran_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_soal');
    }
};
