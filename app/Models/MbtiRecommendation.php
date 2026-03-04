<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MbtiRecommendation extends Model
{
    protected $fillable = [
        'mbti_test_id',
        'position_id',
        'match_score',
        'rank',
        'test_type',
        'source',
    ];

    public function test()
    {
        return $this->belongsTo(MbtiTest::class, 'mbti_test_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
