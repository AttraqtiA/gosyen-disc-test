<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionOceanProfile extends Model
{
    protected $fillable = [
        'position_id',
        'test_type',
        'o_target',
        'c_target',
        'e_target',
        'a_target',
        'n_target',
        'notes',
        'is_active',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
