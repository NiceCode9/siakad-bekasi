<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('raport_detail', function (Blueprint $table) {
            $table->integer('jumlah_pertemuan')->default(0)->after('deskripsi');
            $table->integer('jumlah_hadir')->default(0)->after('jumlah_pertemuan');
            $table->decimal('persentase_kehadiran', 5, 2)->nullable()->after('jumlah_hadir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raport_detail', function (Blueprint $table) {
            $table->dropColumn(['jumlah_pertemuan', 'jumlah_hadir', 'persentase_kehadiran']);
        });
    }
};
