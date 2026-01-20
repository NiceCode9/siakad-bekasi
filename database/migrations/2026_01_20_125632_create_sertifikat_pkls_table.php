<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikat_pkl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_id')->unique()->constrained('pkl')->onDelete('cascade');
            $table->string('nomor_sertifikat', 50)->unique();
            $table->date('tanggal_terbit');
            $table->string('file_sertifikat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikat_pkl');
    }
};
