<?php

namespace App\Notifications;

use Illuminate\Support\Str;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StudentEmailVerification extends Notification
{
    public function __construct(public string $token) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //         ->subject('Student Email Verification')
    //         ->line('Please verify your email address.')
    //         ->action(
    //             'Verify Email',
    //             url('/verify-email?token=' . $this->token)
    //         )
    //         ->line('If you did not create an account, ignore this email.');
    // }

    public function toMail($notifiable)
{
    $verifyUrl = config('app.frontend_url') . '/verify-email?token=' . $this->token;

    return (new MailMessage)
        ->subject('Verify Your Email Address')
        ->greeting('Hello ' . ($notifiable->email ?? 'Student') . ',')
        ->line('Thank you for registering.')
        ->line('Please verify your email address to activate your account.')
        ->action('Verify Email', $verifyUrl)
        ->line('If you did not create an account, please ignore this email.')
        ->salutation('Regards, ' . config('app.name'));
}

}
