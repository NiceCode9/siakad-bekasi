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
        Schema::table('mata_pelajaran_kelas', function (Blueprint $table) {
            $table->decimal('kkm', 5, 2)->nullable()->after('jam_per_minggu');
        });

        // Optional: Copy existing KKM values from mata_pelajaran
        DB::table('mata_pelajaran_kelas')->get()->each(function ($mpk) {
            $mapel = DB::table('mata_pelajaran')->where('id', $mpk->mata_pelajaran_id)->first();
            if ($mapel) {
                DB::table('mata_pelajaran_kelas')
                    ->where('id', $mpk->id)
                    ->update(['kkm' => $mapel->kkm]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_pelajaran_kelas', function (Blueprint $table) {
            $table->dropColumn('kkm');
        });
    }
};
