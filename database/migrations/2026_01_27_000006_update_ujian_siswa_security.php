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
        Schema::table('ujian_siswa', function (Blueprint $table) {
            $table->string('session_id')->nullable()->after('ip_address');
            $table->integer('violation_count')->default(0)->after('status')->comment('Jumlah pelanggaran (tab switch/fullscreen exit)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ujian_siswa', function (Blueprint $table) {
            $table->dropColumn(['session_id', 'violation_count']);
        });
    }
};
