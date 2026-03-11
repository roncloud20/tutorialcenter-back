<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassStaff extends Pivot
{
    use SoftDeletes;
    protected $table = 'class_staff';

    protected $fillable = [
        'class_id',
        'staff_id',
        'role',
    ];

    public $timestamps = true; // Important if pivot has timestamps
}

