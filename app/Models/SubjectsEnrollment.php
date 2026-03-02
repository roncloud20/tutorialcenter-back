<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectsEnrollment extends Model
{
    use SoftDeletes;

    protected $table = 'subjects_enrollments';

    protected $fillable = [
        'course_enrollment_id',
        'subject_id',
        'student_id',
        'progress',
    ];

    protected $casts = [
        'progress' => 'float',
    ];

    /**
     * The student who is enrolled in this subject.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * The course enrollment associated with this subject enrollment.
     */
    public function enrollment()
    {
        return $this->belongsTo(CoursesEnrollment::class, 'course_enrollment_id');
    }

    /**
     * The subject being enrolled.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}







// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

// class SubjectsEnrollment extends Model
// {
//     use SoftDeletes;

//     protected $table = 'subjects_enrollments';

//     protected $fillable = [
//         'course_enrollment',
//         'subject',
//         'student',
//         'progress',
//     ];

//     protected $casts = [
//         'progress' => 'float',
//     ];

//     public function enrollment()
//     {
//         return $this->belongsTo(CoursesEnrollment::class, 'course_enrollment');
//     }

//     public function subject()
//     {
//         return $this->belongsTo(Subject::class, 'subject');
//     }

// }
