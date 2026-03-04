<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MbtiResult extends Model
{
    protected $fillable = [
        'mbti_test_id',
        'e_score',
        'i_score',
        's_score',
        'n_score',
        't_score',
        'f_score',
        'j_score',
        'p_score',
        'type_code',
    ];

    public function test()
    {
        return $this->belongsTo(MbtiTest::class, 'mbti_test_id');
    }
}
