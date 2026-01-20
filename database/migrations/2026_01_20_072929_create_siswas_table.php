<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->foreignId('orang_tua_id')->nullable()->constrained('orang_tua')->onDelete('set null');
            $table->string('nisn', 10)->unique();
            $table->string('nis', 20)->unique();
            $table->string('nik', 16)->unique()->nullable();
            $table->string('nama_lengkap', 100);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'])->nullable();
            $table->integer('anak_ke')->nullable();
            $table->integer('jumlah_saudara')->nullable();
            $table->text('alamat')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('kelurahan', 50)->nullable();
            $table->string('kecamatan', 50)->nullable();
            $table->string('kota', 50)->nullable();
            $table->string('provinsi', 50)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('asal_sekolah', 100)->nullable();
            $table->year('tahun_lulus_smp')->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable();
            $table->decimal('berat_badan', 5, 2)->nullable();
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O'])->nullable();
            $table->string('foto')->nullable();
            $table->enum('status', ['aktif', 'lulus', 'pindah', 'keluar', 'DO'])->default('aktif');
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->timestamps();

            $table->index('nisn');
            $table->index('nis');
            $table->index('nama_lengkap');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
