<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoursesEnrollment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'student_id',
        'start_date',
        'end_date',
        'billing_cycle',
        'cost',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
        'cost'       => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subjects()
{
    return $this->hasMany(SubjectsEnrollment::class, 'course_enrollment_id');
}

    public function payments()
    {
        return $this->hasMany(Payment::class, 'course_enrollment_id');
    }
}
