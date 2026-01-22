<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardian extends Model
{
    use SoftDeletes;

    protected $fillable = [
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
        'students',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'students' => 'array',
        'date_of_birth' => 'date',
        'email_verified_at' => 'datetime',
        'tel_verified_at' => 'datetime',
    ];
}
