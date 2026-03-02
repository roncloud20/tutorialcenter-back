<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Payment extends Model
{
    use SoftDeletes;

    public const STATUS_PENDING    = 'pending';
    public const STATUS_SUCCESSFUL = 'successful';
    public const STATUS_FAILED     = 'failed';
    public const STATUS_REFUNDED   = 'refunded';

    protected $fillable = [
        'course_enrollment_id',
        'amount',
        'currency',
        'payment_method',
        'gateway',
        'gateway_reference',
        'status',
        'billing_cycle',
        'meta',
        'paid_at',
    ];

    protected $casts = [
        'meta'     => 'array',
        'paid_at'  => 'datetime',
        'amount'   => 'decimal:2',
    ];

    public function enrollment()
    {
        return $this->belongsTo(CoursesEnrollment::class);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESSFUL);
    }

    public function markAsSuccessful(array $meta = [])
    {
        if ($this->status === self::STATUS_SUCCESSFUL) {
            return $this;
        }

        return $this->update([
            'status'  => self::STATUS_SUCCESSFUL,
            'paid_at' => now(),
            'meta'    => array_merge($this->meta ?? [], $meta),
        ]);
    }
}







// class Payment extends Model
// {
//     use SoftDeletes;

//     /**
//      * Mass assignable fields
//      */
//     protected $fillable = [
//         'student_id',
//         'course_enrollment_id',
//         'amount',
//         'currency',
//         'payment_method',
//         'gateway',
//         'gateway_reference',
//         'status',
//         'billing_cycle',
//         'meta',
//         'paid_at',
//     ];

//     /**
//      * Casts
//      */
//     protected $casts = [
//         'meta'    => 'array',
//         'paid_at'=> 'datetime',
//         'amount' => 'decimal:2',
//     ];

//     /**
//      * --------------------
//      * Relationships
//      * --------------------
//      */

//     /**
//      * Payment belongs to a student
//      */
//     public function student()
//     {
//         return $this->belongsTo(Student::class);
//     }

//     /**
//      * Payment belongs to a course enrollment
//      */
//     public function enrollment()
//     {
//         return $this->belongsTo(CoursesEnrollment::class, 'course_enrollment_id');
//     }

//     /**
//      * --------------------
//      * Query Scopes
//      * --------------------
//      */

//     public function scopeSuccessful($query)
//     {
//         return $query->where('status', 'successful');
//     }

//     public function scopePending($query)
//     {
//         return $query->where('status', 'pending');
//     }

//     public function scopeFailed($query)
//     {
//         return $query->where('status', 'failed');
//     }

//     /**
//      * --------------------
//      * Helpers
//      * --------------------
//      */

//     public function markAsSuccessful(array $meta = [])
//     {
//         return $this->update([
//             'status'   => 'successful',
//             'paid_at'  => now(),
//             'meta'     => array_merge($this->meta ?? [], $meta),
//         ]);
//     }

//     public function markAsFailed(array $meta = [])
//     {
//         return $this->update([
//             'status' => 'failed',
//             'meta'   => array_merge($this->meta ?? [], $meta),
//         ]);
//     }

//     public function markAsRefunded(array $meta = [])
//     {
//         return $this->update([
//             'status' => 'refunded',
//             'meta'   => array_merge($this->meta ?? [], $meta),
//         ]);
//     }
// }
