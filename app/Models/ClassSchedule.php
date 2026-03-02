<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSchedule extends Model
{
    use SoftDeletes;

    protected $table = 'class_schedules';

    protected $fillable = [
        'class_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    /**
     * Cast time fields correctly
     */
    protected $casts = [
        'start_time' => 'string',
        'end_time'   => 'string',
    ];

    /**
     * Relationships
     */

    // Each schedule belongs to one class
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    // A schedule can have many sessions
    public function sessions()
    {
        return $this->hasMany(ClassSession::class, 'class_schedule_id');
    }
}





// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

// class ClassSchedule extends Model
// {
//     use SoftDeletes;

//     protected $fillable = [
//         'class_id',
//         'day_of_week',
//         'start_time',
//         'end_time',
//     ];

//     protected $casts = [
//         'start_time' => 'datetime:H:i',
//         'end_time' => 'datetime:H:i',
//     ];

//     // public function sessions()
//     // {
//     //     return $this->hasMany(ClassSession::class, 'class_schedule');
//     // }
//     public function sessions()
//     {
//         return $this->hasMany(ClassSession::class, 'class_schedule_id');
//     }
// }
