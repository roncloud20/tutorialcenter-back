<?php
namespace App\Services;

use App\Models\Staff;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Notification;

class AdminNotificationService
{
    public static function notify(string $type, string $message, array $data = [])
    {
        // Get all admins
        $admins = Staff::admins()->get();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send(
            $admins,
            new AdminNotification($type, $message, $data)
        );
    }
}