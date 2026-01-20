<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perusahaan_pkl', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 200);
            $table->string('bidang_usaha', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('nama_kontak', 100)->nullable();
            $table->string('jabatan_kontak', 50)->nullable();
            $table->string('telepon_kontak', 20)->nullable();
            $table->integer('kuota')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perusahaan_pkl');
    }
};
