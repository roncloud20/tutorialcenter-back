<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'banner',
        'courses',
        'departments',
        'status',
        'assignees',
    ];

    protected $casts = [
        'courses' => 'array',
        'departments' => 'array',
        'assignees' => 'array',
    ];

    // public function classes()
    // {
    //     return $this->hasMany(Classes::class, 'subject');
    // }

    public function classes()
    {
        return $this->hasMany(Classes::class, 'subject_id');
    }
}
