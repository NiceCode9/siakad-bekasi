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
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('nama_ayah', 100)->after('orang_tua_id')->nullable();
            $table->string('pekerjaan_ayah', 100)->after('nama_ayah')->nullable();
            $table->string('nama_ibu', 100)->after('pekerjaan_ayah')->nullable();
            $table->string('pekerjaan_ibu', 100)->after('nama_ibu')->nullable();
            $table->text('alamat_ortu')->after('pekerjaan_ibu')->nullable();
            $table->string('telepon_ortu', 20)->after('alamat_ortu')->nullable();
        });

        // Migrate data from orang_tua to siswa
        $siswas = DB::table('siswa')
            ->join('orang_tua', 'siswa.orang_tua_id', '=', 'orang_tua.id')
            ->select('siswa.id', 'orang_tua.*')
            ->get();

        foreach ($siswas as $s) {
            DB::table('siswa')->where('id', $s->id)->update([
                'nama_ayah' => $s->nama_ayah,
                'pekerjaan_ayah' => $s->pekerjaan_ayah,
                'nama_ibu' => $s->nama_ibu,
                'pekerjaan_ibu' => $s->pekerjaan_ibu,
                'alamat_ortu' => $s->alamat,
                'telepon_ortu' => $s->telepon_ayah ?? $s->telepon_ibu ?? $s->telepon_wali,
            ]);
        }

        Schema::table('siswa', function (Blueprint $table) {
            $table->dropForeign(['orang_tua_id']);
            $table->dropColumn('orang_tua_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->foreignId('orang_tua_id')->nullable()->constrained('orang_tua')->onDelete('set null')->after('user_id');
            $table->dropColumn(['nama_ayah', 'pekerjaan_ayah', 'nama_ibu', 'pekerjaan_ibu', 'alamat_ortu', 'telepon_ortu']);
        });
    }
};
