<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PastQuestionGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'exam_year_id',
        'title',
        'content',
        'type',
        'image',
        'sort_order',
    ];

    public function examYear()
    {
        return $this->belongsTo(ExamYear::class);
    }

    public function questions()
    {
        return $this->hasMany(PastQuestion::class);
    }
}