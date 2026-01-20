<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringPkl extends Model
{
    use HasFactory;

    protected $table = 'monitoring_pkl';

    protected $fillable = [
        'pkl_id',
        'tanggal_monitoring',
        'kegiatan',
        'hambatan',
        'solusi',
        'catatan',
        'foto',
    ];

    protected $casts = [
        'tanggal_monitoring' => 'date',
    ];

    // Relationships
    public function pkl()
    {
        return $this->belongsTo(Pkl::class);
    }
}
