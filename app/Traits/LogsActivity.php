<?php

namespace App\Traits;

use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('create');
        });

        static::updated(function ($model) {
            $model->logActivity('update');
        });

        static::deleted(function ($model) {
            $model->logActivity('delete');
        });
    }

    protected function logActivity($action)
    {
        $oldData = null;
        $newData = null;

        if ($action === 'create') {
            $newData = json_encode($this->getAttributes());
        } elseif ($action === 'update') {
            // Only log if attributes actually changed
            if (empty($this->getChanges())) {
                return;
            }
            $oldData = json_encode(array_intersect_key($this->getOriginal(), $this->getChanges()));
            $newData = json_encode($this->getChanges());
        } elseif ($action === 'delete') {
            $oldData = json_encode($this->getOriginal());
        }

        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => $action . ' ' . class_basename($this),
            'tabel' => $this->getTable(),
            'tabel_id' => $this->id,
            'data_lama' => $oldData,
            'data_baru' => $newData,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
