<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OceanAnswer extends Model
{
    protected $fillable = [
        'ocean_test_id',
        'ocean_question_id',
        'score',
    ];

    public function test()
    {
        return $this->belongsTo(OceanTest::class, 'ocean_test_id');
    }

    public function question()
    {
        return $this->belongsTo(OceanQuestion::class, 'ocean_question_id');
    }
}
