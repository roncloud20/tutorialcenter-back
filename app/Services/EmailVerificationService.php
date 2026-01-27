<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\Student;
use App\Models\Guardian;
use Illuminate\Support\Str;
use App\Models\EmailVerification;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\StudentEmailVerification;
use App\Notifications\StaffEmailVerificationNotification;
use App\Notifications\GuardianEmailVerificationNotification;

class EmailVerificationService
{
    public function send(Model $user): void
    {
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

        if ($user instanceof Student) {
            $user->notify(new StudentEmailVerification($token));
            return;
        }

        if ($user instanceof Staff) {
            $user->notify(new StaffEmailVerificationNotification($token));
            return;
        }

        if ($user instanceof Guardian) {
            $user->notify(new GuardianEmailVerificationNotification($token));
            return;
        }
    }
}