<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komponen_nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kurikulum_id')->constrained('kurikulum')->onDelete('cascade');
            $table->string('kode', 20)->unique();
            $table->string('nama', 100);
            $table->enum('kategori', ['pengetahuan', 'keterampilan', 'sikap']);
            $table->decimal('bobot', 5, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komponen_nilai');
    }
};
