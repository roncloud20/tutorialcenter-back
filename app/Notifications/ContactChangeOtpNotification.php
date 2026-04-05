<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ContactChangeOtpNotification extends Notification
{
    use Queueable;

    protected $code;
    protected $type;
    protected $expiresIn;

    public function __construct($code, $type, $expiresIn = 10)
    {
        $this->code = $code;
        $this->type = $type;
        $this->expiresIn = $expiresIn;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Verify Your ' . ucfirst($this->type))
            ->greeting('Hello!')
            ->line("You requested to change your {$this->type}.")
            ->line('Use the OTP below to verify your request:')
            ->line("OTP: {$this->code}")
            ->line("This OTP expires in {$this->expiresIn} minutes.")
            ->line('If you did not request this, please ignore this message.');
    }
}