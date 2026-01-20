<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('nip', 30)->unique()->nullable();
            $table->string('nuptk', 20)->unique()->nullable();
            $table->string('nama_lengkap', 100);
            $table->string('gelar_depan', 20)->nullable();
            $table->string('gelar_belakang', 50)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'])->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->enum('status_kepegawaian', ['PNS', 'PPPK', 'GTY', 'GTT', 'Honorer'])->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('nip');
            $table->index('nama_lengkap');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru');
    }
};
