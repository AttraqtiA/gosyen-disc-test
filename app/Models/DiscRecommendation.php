<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscRecommendation extends Model
{
    protected $fillable = [
        'disc_test_id',
        'position_id',
        'match_score',
        'rank',
        'test_type',
        'source',
    ];

    public function test()
    {
        return $this->belongsTo(DiscTest::class, 'disc_test_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
