<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MbtiQuestion extends Model
{
    protected $fillable = [
        'question_number',
        'text_a',
        'text_b',
        'trait_a',
        'trait_b',
    ];

    public function answers()
    {
        return $this->hasMany(MbtiAnswer::class);
    }
}
