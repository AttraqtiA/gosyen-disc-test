<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionDiscProfile extends Model
{
    protected $fillable = [
        'position_id',
        'test_type',
        'd_target',
        'i_target',
        's_target',
        'c_target',
        'notes',
        'is_active',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
