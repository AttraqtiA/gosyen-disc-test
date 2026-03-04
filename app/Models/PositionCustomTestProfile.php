<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionCustomTestProfile extends Model
{
    protected $fillable = [
        'position_id',
        'custom_test_id',
        'target_scores_json',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'target_scores_json' => 'array',
        'is_active' => 'boolean',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function test()
    {
        return $this->belongsTo(CustomTest::class, 'custom_test_id');
    }
}
