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
            $table->decimal('nilai_akhir_manual', 5, 2)->nullable()->after('nilai_akhir');
            $table->boolean('is_manual_override')->default(false)->after('nilai_akhir_manual');
            $table->text('override_reason')->nullable()->after('is_manual_override');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raport_detail', function (Blueprint $table) {
            $table->dropColumn(['nilai_akhir_manual', 'is_manual_override', 'override_reason']);
        });
    }
};
