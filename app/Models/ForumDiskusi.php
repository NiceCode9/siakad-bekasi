<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumDiskusi extends Model
{
    use HasFactory;

    protected $table = 'forum_diskusi';

    protected $fillable = [
        'mata_pelajaran_guru_id',
        'pembuat_id',
        'judul',
        'konten',
        'is_pinned',
        'is_locked',
        'view_count',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
    ];

    // Relationships
    public function mataPelajaranGuru()
    {
        return $this->belongsTo(MataPelajaranGuru::class);
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }

    public function forumKomentar()
    {
        return $this->hasMany(ForumKomentar::class);
    }
}
