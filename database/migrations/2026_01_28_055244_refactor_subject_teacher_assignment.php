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
        // 1. Add guru_id to mata_pelajaran_kelas
        Schema::table('mata_pelajaran_kelas', function (Blueprint $table) {
            $table->foreignId('guru_id')->nullable()->after('kelas_id')->constrained('guru')->onDelete('cascade');
        });

        // 2. Migrate guru_id from mata_pelajaran_guru to mata_pelajaran_kelas
        // Only one teacher per subject-class will be picked
        $mappings = DB::table('mata_pelajaran_guru')->get();
        foreach ($mappings as $map) {
            DB::table('mata_pelajaran_kelas')
                ->where('id', $map->mata_pelajaran_kelas_id)
                ->whereNull('guru_id') // Only update if not already set by another entry
                ->update(['guru_id' => $map->guru_id]);
        }

        // 3. Add mata_pelajaran_kelas_id to children and migrate data
        $children = ['jadwal_pelajaran', 'materi_ajar', 'tugas', 'forum_diskusi'];
        foreach ($children as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('mata_pelajaran_kelas_id')->nullable()->after('id')->constrained('mata_pelajaran_kelas')->onDelete('cascade');
            });

            // Map data: Get mapping from mata_pelajaran_guru
            $mpgTable = DB::table('mata_pelajaran_guru')->get();
            foreach ($mpgTable as $mpg) {
                DB::table($table)
                    ->where('mata_pelajaran_guru_id', $mpg->id)
                    ->update(['mata_pelajaran_kelas_id' => $mpg->mata_pelajaran_kelas_id]);
            }

            // Drop old column
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['mata_pelajaran_guru_id']);
                $table->dropColumn('mata_pelajaran_guru_id');
                $table->foreignId('mata_pelajaran_kelas_id')->nullable(false)->change();
            });
        }

        // 4. Cleanup
        Schema::dropIfExists('mata_pelajaran_guru');
        
        Schema::table('mata_pelajaran_kelas', function (Blueprint $table) {
            $table->foreignId('guru_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversal would be complex and probably not needed for a structure fix, 
        // but if required, we'd need to recreate mata_pelajaran_guru and move IDs back.
    }
};

