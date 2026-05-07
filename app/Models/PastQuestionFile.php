<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PastQuestionFile extends Model
{
    protected $fillable = [
        'past_question_id',
        'file_path',
        'file_type',
        'caption',
    ];

    public function question()
    {
        return $this->belongsTo(PastQuestion::class, 'past_question_id');
    }
}