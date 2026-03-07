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
        Schema::table('nilai', function (Blueprint $table) {
            $table->string('jenis_nilai')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            // Reverting back to enum. Note: if there are values now that don't fit the enum, this might fail.
            $table->enum('jenis_nilai', ['tugas', 'ulangan_harian', 'uts', 'uas', 'praktik', 'proyek', 'lainnya'])->change();
        });
    }
};
