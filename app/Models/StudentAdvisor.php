<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAdvisor extends Model
{
    protected $fillable = [
        'student_id',
        'staff_id',
        'role',
        'assigned_at'
    ];

    protected $casts = [
        'assigned_at' => 'datetime'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}