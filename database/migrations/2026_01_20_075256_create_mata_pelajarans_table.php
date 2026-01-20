<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kurikulum_id')->constrained('kurikulum')->onDelete('restrict');
            $table->foreignId('kelompok_mapel_id')->nullable()->constrained('kelompok_mapel')->onDelete('set null');
            $table->string('kode', 20)->unique();
            $table->string('nama', 100);
            $table->enum('jenis', ['umum', 'kejuruan', 'muatan_lokal']);
            $table->enum('kategori', ['wajib', 'peminatan', 'lintas_minat'])->default('wajib');
            $table->decimal('kkm', 5, 2)->default(75.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('kode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_pelajaran');
    }
};
