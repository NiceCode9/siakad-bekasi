<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelajaran_guru_id')->constrained('mata_pelajaran_guru')->onDelete('cascade');
            $table->string('judul', 200);
            $table->text('deskripsi')->nullable();
            $table->string('file_lampiran')->nullable();
            $table->date('tanggal_buat');
            $table->dateTime('tanggal_deadline');
            $table->decimal('bobot', 5, 2)->default(1.00);
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index('tanggal_deadline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};
