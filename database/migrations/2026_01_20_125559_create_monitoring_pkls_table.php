<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_pkl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_id')->constrained('pkl')->onDelete('cascade');
            $table->date('tanggal_monitoring');
            $table->text('kegiatan')->nullable();
            $table->text('hambatan')->nullable();
            $table->text('solusi')->nullable();
            $table->text('catatan')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();

            $table->index('pkl_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_pkl');
    }
};
