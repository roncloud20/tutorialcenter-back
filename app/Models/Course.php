<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'banner',
        'status',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function enrollments()
    {
        return $this->hasMany(CoursesEnrollment::class, 'course_id');
    }
}
