<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSession extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'class',
        'class_schedule',
        'session_date',
        'starts_at',
        'ends_at',
        'class_link',
        'status',
    ];

    protected $casts = [
        'session_date' => 'date',
        'starts_at' => 'datetime:H:i',
        'ends_at' => 'datetime:H:i',
    ];
}
