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
        Schema::table('monitoring_pkl', function (Blueprint $table) {
            if (!Schema::hasColumn('monitoring_pkl', 'status')) {
                $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending')->after('foto');
            }
            if (!Schema::hasColumn('monitoring_pkl', 'catatan_pembimbing')) {
                $table->text('catatan_pembimbing')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_pkl', function (Blueprint $table) {
            $table->dropColumn(['status', 'catatan_pembimbing']);
        });
    }
};
