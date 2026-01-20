<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materi_ajar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelajaran_guru_id')->constrained('mata_pelajaran_guru')->onDelete('cascade');
            $table->string('judul', 200);
            $table->text('deskripsi')->nullable();
            $table->enum('tipe', ['pdf', 'video', 'slide', 'link', 'lainnya']);
            $table->string('file_path')->nullable();
            $table->string('url')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_published')->default(false);
            $table->date('tanggal_publish')->nullable();
            $table->integer('view_count')->default(0);
            $table->timestamps();

            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi_ajar');
    }
};
