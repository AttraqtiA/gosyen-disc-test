<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionMbtiProfile extends Model
{
    protected $fillable = [
        'position_id',
        'test_type',
        'e_target',
        'i_target',
        's_target',
        'n_target',
        't_target',
        'f_target',
        'j_target',
        'p_target',
        'notes',
        'is_active',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
