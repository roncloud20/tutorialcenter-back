<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classes extends Model
{
    use SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'subject_id',
        'title',
        'description',
        'status',
    ];

    public function staffs()
    {
        return $this->belongsToMany(
            Staff::class,
            'class_staff',
            'class_id',   // Foreign key on pivot referencing Classes
            'staff_id'    // Foreign key on pivot referencing Staff
        )
            ->using(ClassStaff::class) // optional but correct since you created Pivot model
            ->withPivot('role')
            ->withTimestamps();
    }
    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class, 'class_id');
    }

    public function sessions()
    {
        return $this->hasMany(ClassSession::class, 'class_id');
    }
}
