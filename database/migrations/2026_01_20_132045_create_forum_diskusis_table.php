<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_diskusi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelajaran_guru_id')->constrained('mata_pelajaran_guru')->onDelete('cascade');
            $table->foreignId('pembuat_id')->constrained('users')->onDelete('cascade');
            $table->string('judul', 200);
            $table->text('konten');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->integer('view_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_diskusi');
    }
};
