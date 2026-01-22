<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoursesEnrollment extends Model
{
    use SoftDeletes;

    protected $table = 'courses_enrollments';

    protected $fillable = [
        'course',
        'student',
        'start_date',
        'end_date',
        'billing_cycle',
        'cost',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'cost' => 'decimal:2',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student');
    }
}
