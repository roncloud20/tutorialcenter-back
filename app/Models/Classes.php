<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classes extends Model
{
    use SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'subject',
        'title',
        'description',
        'staffs',
        'status',
    ];

    protected $casts = [
        'staffs' => 'array',
    ];

    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class, 'class');
    }

    public function sessions()
    {
        return $this->hasMany(ClassSession::class, 'class');
    }
}
