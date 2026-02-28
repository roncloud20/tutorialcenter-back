<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSchedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'class_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // public function sessions()
    // {
    //     return $this->hasMany(ClassSession::class, 'class_schedule');
    // }
    public function sessions()
    {
        return $this->hasMany(ClassSession::class, 'class_schedule_id');
    }
}
