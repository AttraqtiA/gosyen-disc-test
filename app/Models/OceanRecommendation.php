<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OceanRecommendation extends Model
{
    protected $fillable = [
        'ocean_test_id',
        'position_id',
        'match_score',
        'rank',
        'test_type',
        'source',
    ];

    public function test()
    {
        return $this->belongsTo(OceanTest::class, 'ocean_test_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
