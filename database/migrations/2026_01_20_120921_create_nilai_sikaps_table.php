<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_sikap', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semester')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->enum('aspek', ['spiritual', 'sosial']);
            $table->decimal('nilai', 5, 2);
            $table->string('predikat', 2)->nullable();
            $table->text('deskripsi')->nullable();
            $table->foreignId('penginput_id')->constrained('guru')->onDelete('restrict');
            $table->timestamps();

            $table->unique(['siswa_id', 'semester_id', 'aspek']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_sikap');
    }
};
