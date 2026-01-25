<?php

namespace App\Services;

use App\Notifications\StaffEmailVerificationNotification;
use Illuminate\Support\Str;
use App\Models\EmailVerification;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\StudentEmailVerification;

class EmailVerificationService
{
    public function send(Model $user): void
    {
        // Remove existing tokens
        EmailVerification::where('verifiable_type', get_class($user))
            ->where('verifiable_id', $user->id)
            ->delete();

        $token = Str::uuid();

        EmailVerification::create([
            'verifiable_type' => get_class($user),
            'verifiable_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addMinutes(30),
        ]);
        if(get_class($user) === 'App\Models\Student') {
            $user->notify(new StudentEmailVerification($token));
            return;
        } else if(get_class($user) === 'App\Models\Staff') {
            $user->notify(new StaffEmailVerificationNotification($token));
            return;
        }
    }
}