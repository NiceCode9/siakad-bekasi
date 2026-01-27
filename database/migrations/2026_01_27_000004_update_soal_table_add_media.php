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
        Schema::table('soal', function (Blueprint $table) {
            $table->string('tipe_media', 20)->nullable()->after('pembahasan'); // image, audio, video
            $table->string('audio')->nullable()->after('gambar');
            $table->string('video')->nullable()->after('audio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->dropColumn(['tipe_media', 'audio', 'video']);
        });
    }
};
