<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Raport extends Model
{
    use HasFactory;

    protected $table = 'raport';

    protected $fillable = [
        'siswa_id',
        'semester_id',
        'kelas_id',
        'jumlah_sakit',
        'jumlah_izin',
        'jumlah_alpha',
        'catatan_wali_kelas',
        'tanggal_generate',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_generate' => 'date',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function raportDetail()
    {
        return $this->hasMany(RaportDetail::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
