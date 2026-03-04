<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OceanQuestion extends Model
{
    protected $fillable = [
        'question_number',
        'statement',
        'trait',
        'reverse_scored',
    ];

    protected $casts = [
        'reverse_scored' => 'boolean',
    ];

    public function answers()
    {
        return $this->hasMany(OceanAnswer::class);
    }
}
