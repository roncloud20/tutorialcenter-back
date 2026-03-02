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
        'status',
        'departments',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function classes()
    {
        return $this->hasMany(Classes::class, 'subject_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class)->withTimestamps();
    }

    public function staff()
    {
        return $this->belongsToMany(Staff::class)
                    ->withPivot('role')
                    ->withTimestamps();
    }
}

// class Subject extends Model
// {
//     use SoftDeletes;

//     protected $fillable = [
//         'name',
//         'description',
//         'banner',
//         'courses',
//         'departments',
//         'status',
//         'assignees',
//     ];

//     protected $casts = [
//         'courses' => 'array',
//         'departments' => 'array',
//         'assignees' => 'array',
//     ];

//     public function classes()
//     {
//         return $this->hasMany(Classes::class, 'subject_id');
//     }
// }
