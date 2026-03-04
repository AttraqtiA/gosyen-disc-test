<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MbtiAnswer extends Model
{
    protected $fillable = [
        'mbti_test_id',
        'mbti_question_id',
        'selected_trait',
    ];

    public function test()
    {
        return $this->belongsTo(MbtiTest::class, 'mbti_test_id');
    }

    public function question()
    {
        return $this->belongsTo(MbtiQuestion::class, 'mbti_question_id');
    }
}
