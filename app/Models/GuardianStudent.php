<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuardianStudent extends Pivot
{
    use SoftDeletes;

    protected $table = 'guardian_students';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'student_id',
        'guardian_id',
        'relationship',
    ];

    /**
     * Cast attributes.
     */
    protected $casts = [
        'student_id' => 'integer',
        'guardian_id' => 'integer',
        'relationship' => 'string',
    ];

    /**
     * Relationships
     */

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }
}