<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ClassStaff extends Pivot
{
    protected $table = 'class_staff';

    protected $fillable = [
        'class_id',
        'staff_id',
        'role',
    ];

    public $timestamps = true; // Important if pivot has timestamps
}




// namespace App\Models;

// use Illuminate\Database\Eloquent\Relations\Pivot;

// class ClassStaff extends Pivot
// {
//     protected $table = 'class_staff';

//     protected $fillable = [
//         'class_id',
//         'staff_id',
//         'role'
//     ];
// }

