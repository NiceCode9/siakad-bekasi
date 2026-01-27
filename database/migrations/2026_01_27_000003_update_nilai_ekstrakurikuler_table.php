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
        Schema::table('nilai_ekstrakurikuler', function (Blueprint $table) {
            // Add new foreign key
            $table->foreignId('ekstrakurikuler_id')->nullable()->after('semester_id')->constrained('ekstrakurikuler')->onDelete('cascade');
        });

        // We might want to migrate data if there was any, but for now assuming development environment or fresh start for this feature.
        
        Schema::table('nilai_ekstrakurikuler', function (Blueprint $table) {
            // Drop old column
             $table->dropColumn('nama_ekskul');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_ekstrakurikuler', function (Blueprint $table) {
            $table->string('nama_ekskul')->nullable();
            $table->dropForeign(['ekstrakurikuler_id']);
            $table->dropColumn('ekstrakurikuler_id');
        });
    }
};
