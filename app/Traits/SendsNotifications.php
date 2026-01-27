<?php

namespace App\Traits;

use App\Models\Notifikasi;
use App\Models\User;

trait SendsNotifications
{
    /**
     * Send notification to a single user
     */
    public function notifyUser($userId, $title, $message, $type = 'info', $link = null)
    {
        return Notifikasi::create([
            'user_id' => $userId,
            'judul' => $title,
            'pesan' => $message,
            'tipe' => $type,
            'link' => $link,
            'is_read' => false
        ]);
    }

    /**
     * Send notification to all users with specific roles
     */
    public function notifyRoles($roles, $title, $message, $type = 'info', $link = null)
    {
        $users = User::role($roles)->get();
        foreach ($users as $user) {
            $this->notifyUser($user->id, $title, $message, $type, $link);
        }
    }
}
