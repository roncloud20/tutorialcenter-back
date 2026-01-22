<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes, Notifiable, HasApiTokens;

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
        'department',
        'guardians',
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'password' => 'hashed',
        'guardians' => 'array',
        'date_of_birth' => 'date',
        'email_verified_at' => 'datetime',
        'tel_verified_at' => 'datetime',
    ];

    public function courseEnrollments()
    {
        return $this->hasMany(CoursesEnrollment::class, 'student');
    }

    public function subjectEnrollments()
    {
        return $this->hasMany(SubjectsEnrollment::class, 'student');
    }
}
