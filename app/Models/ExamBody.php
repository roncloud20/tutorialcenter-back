<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ExamBody extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($examBody) {
            if (empty($examBody->slug)) {
                $examBody->slug = Str::slug($examBody->name);
            }
        });

        static::updating(function ($examBody) {
            if ($examBody->isDirty('name')) {
                $examBody->slug = Str::slug($examBody->name);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // public function examYears()
    // {
    //     return $this->hasMany(ExamYear::class);
    // }

    // public function pastQuestions()
    // {
    //     return $this->hasMany(PastQuestion::class);
    // }
}