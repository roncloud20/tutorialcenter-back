<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\EmailVerification;
use Illuminate\Support\Facades\DB;
use App\Notifications\StudentEmailVerification;

class EmailVerificationService
{
    public function send($student): bool
    {
        return DB::transaction(function () use ($student) {

            $token = Str::uuid();

            EmailVerification::create([
                'student' => $student->id,
                'token' => $token,
                'expires_at' => now()->addMinutes(30),
            ]);

            $student->notify(new StudentEmailVerification($token));

            return true;
        });
    }
}
