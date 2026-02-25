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
            $table->text('capaian_kompetensi')->nullable()->after('kkm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_pelajaran_kelas', function (Blueprint $table) {
            $table->dropColumn('capaian_kompetensi');
        });
    }
};
