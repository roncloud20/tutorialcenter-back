<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdminNotification extends Notification {
    protected $type;
    protected $message;
    protected $data;

    public function __construct(string $type, string $message, array $data = [])
    {
        $this->type = $type;
        $this->message = $message;
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database']; // add 'mail' if needed
    }

    public function toArray($notifiable)
    {
        return [
            'type' => $this->type,
            'message' => $this->message,
            'data' => $this->data,

            'meta' => [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],

            'time' => now(),
        ];
    }

    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //         ->subject('Admin Alert')
    //         ->line($this->message)
    //         ->line('Time: ' . now());
    // }
}
