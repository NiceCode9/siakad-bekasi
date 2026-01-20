<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raport_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raport_id')->constrained('raport')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->decimal('nilai_pengetahuan', 5, 2)->nullable();
            $table->decimal('nilai_keterampilan', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->string('predikat', 2)->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->unique(['raport_id', 'mata_pelajaran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raport_detail');
    }
};
