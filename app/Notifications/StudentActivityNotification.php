<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StudentActivityNotification extends Notification
{
    protected $student;
    protected $type;
    protected $data;

    public function __construct($student, string $type, array $data = [])
    {
        $this->student = $student;
        $this->type = $type;
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database']; // add 'mail' later if needed
    }

    public function toArray($notifiable)
    {
        return [
            'type' => $this->type,

            'student' => [
                'id' => $this->student->id,
                'name' => $this->student->firstname . ' ' . $this->student->surname,
            ],

            'message' => $this->buildMessage(),

            'data' => $this->data,

            'meta' => [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],

            'time' => now(),
        ];
    }

    // Build a user-friendly message based on the activity type
    protected function buildMessage()
    {
        return match ($this->type) {

            'login' => "{$this->student->firstname} just logged in",

            'attendance' => "{$this->student->firstname} attended a class",

            'assignment_submitted' => "{$this->student->firstname} submitted an assignment",

            'payment_successful' => "{$this->student->firstname} made a payment",

            'schedule_update' => "Class schedule updated for {$this->student->firstname}",

            default => "New activity from {$this->student->firstname}",
        };
    }

    // // Optional: If you want to send email notifications as well
    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //         ->subject('Student Activity Alert')
    //         ->line($this->buildMessage())
    //         ->line('Time: ' . now());
    // }
}

