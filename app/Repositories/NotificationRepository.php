<?php

namespace App\Repositories;

use App\Notification;

final class NotificationRepository
{

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @return Notification
     */
    public static function sendToUserId(int $userId, string $message, string $type)
    {
        return Notification::create([
            'user_id' => $userId,
            'message' => $message,
            'type' => $type,
            'read' => false,
        ]);
    }

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @return Collection
     */
    public static function unreadsByUserId(int $userId) 
    {
        return Notification::whereRead(false)->whereUserId($userId)->get();
    }

    /**
     * Undocumented function
     *
     * @param array $notificationIds
     * @return bool
     */
    public static function markAsRead(array $notificationIds) {
        return Notification::whereIn('id', $notificationIds)->update([
            'read' => true, 
        ]);
    }
}
