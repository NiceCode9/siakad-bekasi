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
        Schema::table('mata_pelajaran_kelas', function (Blueprint $table) {
            $table->dropColumn('capaian_kompetensi');
            $table->text('capaian_kompetensi_a')->nullable()->after('kkm');
            $table->text('capaian_kompetensi_b')->nullable()->after('capaian_kompetensi_a');
            $table->text('capaian_kompetensi_c')->nullable()->after('capaian_kompetensi_b');
            $table->text('capaian_kompetensi_d')->nullable()->after('capaian_kompetensi_c');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_pelajaran_kelas', function (Blueprint $table) {
            $table->dropColumn(['capaian_kompetensi_a', 'capaian_kompetensi_b', 'capaian_kompetensi_c', 'capaian_kompetensi_d']);
            $table->text('capaian_kompetensi')->nullable()->after('kkm');
        });
    }
};
