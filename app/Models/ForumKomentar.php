<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumKomentar extends Model
{
    use HasFactory;

    protected $table = 'forum_komentar';

    protected $fillable = [
        'forum_diskusi_id',
        'user_id',
        'konten',
    ];

    // Relationships
    public function forumDiskusi()
    {
        return $this->belongsTo(ForumDiskusi::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
