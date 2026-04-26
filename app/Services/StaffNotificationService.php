<?php 
namespace App\Services;

use App\Models\Staff;
use App\Notifications\StaffActivityNotification;
use Illuminate\Support\Facades\Notification;

class StaffNotificationService
{
    public static function notify(string $type, string $message, array $data = [])
    {
        // Get particular staff members
        $staffMembers = Staff::all();

        if ($staffMembers->isEmpty()) {
            return;
        }

        Notification::send(
            $staffMembers,
            new StaffActivityNotification($type, $message, $data)
        );
    }
}