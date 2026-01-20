<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kenaikan_kelas_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kenaikan_kelas_id')->constrained('kenaikan_kelas')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('kelas_asal_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('kelas_tujuan_id')->nullable()->constrained('kelas')->onDelete('set null');
            $table->enum('status_kenaikan', ['naik', 'tidak_naik', 'lulus', 'mengulang']);
            $table->decimal('rata_rata_nilai', 5, 2)->nullable();
            $table->integer('jumlah_mapel_remidi')->default(0);
            $table->integer('total_absensi')->default(0);
            $table->decimal('nilai_sikap', 5, 2)->nullable();
            $table->text('alasan_tidak_naik')->nullable();
            $table->timestamps();

            $table->unique(['kenaikan_kelas_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kenaikan_kelas_detail');
    }
};
