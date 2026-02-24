<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->string('file')->nullable()->after('tipe_media');
        });

        // Copy existing data
        DB::table('soal')->get()->each(function ($soal) {
            $filePath = null;
            if ($soal->tipe_media == 'image') $filePath = $soal->gambar;
            elseif ($soal->tipe_media == 'audio') $filePath = $soal->audio;
            elseif ($soal->tipe_media == 'video') $filePath = $soal->video;
            else $filePath = $soal->gambar; // Fallback to gambar

            if ($filePath) {
                DB::table('soal')->where('id', $soal->id)->update(['file' => $filePath]);
            }
        });

        Schema::table('soal', function (Blueprint $table) {
            $table->dropColumn(['gambar', 'audio', 'video']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->string('gambar')->nullable()->after('pembahasan');
            $table->string('audio')->nullable()->after('gambar');
            $table->string('video')->nullable()->after('audio');
        });

        // Restore data
        DB::table('soal')->get()->each(function ($soal) {
            if ($soal->file) {
                $column = 'gambar';
                if ($soal->tipe_media == 'audio') $column = 'audio';
                elseif ($soal->tipe_media == 'video') $column = 'video';

                DB::table('soal')->where('id', $soal->id)->update([$column => $soal->file]);
            }
        });

        Schema::table('soal', function (Blueprint $table) {
            $table->dropColumn('file');
        });
    }
};
