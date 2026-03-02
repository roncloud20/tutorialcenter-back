<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSession extends Model
{
    use SoftDeletes;

    protected $table = 'class_sessions';

    protected $fillable = [
        'class_id',
        'class_schedule_id',
        'session_date',
        'starts_at',
        'ends_at',
        'class_link',
        'recording_link',
        'status',
    ];

    /**
     * Cast attributes properly
     */
    protected $casts = [
        'session_date' => 'date',
        'starts_at'    => 'string',  // because DB column is TIME
        'ends_at'      => 'string',  // because DB column is TIME
    ];

    /**
     * Relationships
     */

    // Each session belongs to one class
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    // Each session belongs to one schedule
    public function schedule()
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id');
    }
}




// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

// class ClassSession extends Model
// {
//     use SoftDeletes;

//     protected $fillable = [
//         'class_id',
//         'class_schedule',
//         'session_date',
//         'starts_at',
//         'ends_at',
//         'class_link',
//         'status',
//     ];

//     protected $casts = [
//         'session_date' => 'date',
//         'starts_at' => 'datetime:H:i',
//         'ends_at' => 'datetime:H:i',
//     ];
// }
