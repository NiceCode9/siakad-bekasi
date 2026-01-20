<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orang_tua', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->nullable()->constrained('users')->onDelete('set null');
            $table->string('nik_ayah', 16)->nullable();
            $table->string('nama_ayah', 100)->nullable();
            $table->string('pekerjaan_ayah', 50)->nullable();
            $table->string('pendidikan_ayah', 50)->nullable();
            $table->string('penghasilan_ayah', 50)->nullable();
            $table->string('telepon_ayah', 20)->nullable();
            $table->string('nik_ibu', 16)->nullable();
            $table->string('nama_ibu', 100)->nullable();
            $table->string('pekerjaan_ibu', 50)->nullable();
            $table->string('pendidikan_ibu', 50)->nullable();
            $table->string('penghasilan_ibu', 50)->nullable();
            $table->string('telepon_ibu', 20)->nullable();
            $table->string('nama_wali', 100)->nullable();
            $table->string('pekerjaan_wali', 50)->nullable();
            $table->string('telepon_wali', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orang_tua');
    }
};
