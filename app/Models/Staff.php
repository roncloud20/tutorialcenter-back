<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Staff extends Model
{
    use SoftDeletes, Notifiable, HasApiTokens;

    protected $table = 'staffs';

    protected $fillable = [
        'staff_id',
        'firstname',
        'surname',
        'email',
        'tel',
        'password',
        'gender',
        'profile_picture',
        'date_of_birth',
        'email_verified_at',
        'tel_verified_at',
        'location',
        'address',
        'role',
        'inducted_by',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'date_of_birth' => 'date',
        'email_verified_at' => 'datetime',
        'tel_verified_at' => 'datetime',
    ];

    public function emailVerification()
    {
        return $this->morphOne(EmailVerification::class, 'verifiable');
    }

}
