<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StaffActivityNotification extends Notification
{
    protected $type;
    protected $message;
    protected $data;

    public function __construct(string $type, string $message, array $data = [])
    {
        $this->type = $type;
        $this->message = $message;
        $this->data = $data;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // or ['mail'], or both
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->type,
            'message' => $this->message,
            'data' => $this->data,
            'meta' => [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
            'time' => now(),
        ];
    }
}