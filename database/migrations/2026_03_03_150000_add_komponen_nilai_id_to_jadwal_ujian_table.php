<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_ujian', function (Blueprint $table) {
            $table->foreignId('komponen_nilai_id')->nullable()->after('bank_soal_id')->constrained('komponen_nilai')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_ujian', function (Blueprint $table) {
            $table->dropForeign(['komponen_nilai_id']);
            $table->dropColumn('komponen_nilai_id');
        });
    }
};
