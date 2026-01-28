<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MateriAjar extends Model
{
    use HasFactory;

    protected $table = 'materi_ajar';

    protected $fillable = [
        'mata_pelajaran_kelas_id',
        'judul',
        'deskripsi',
        'tipe',
        'file_path',
        'url',
        'urutan',
        'is_published',
        'tanggal_publish',
        'view_count',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'tanggal_publish' => 'date',
    ];

    // Relationships
    public function mataPelajaranKelas()
    {
        return $this->belongsTo(MataPelajaranKelas::class);
    }


    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
