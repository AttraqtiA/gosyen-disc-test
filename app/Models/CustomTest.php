<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomTest extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'code',
        'description',
        'time_limit_minutes',
        'instructions',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function dimensions()
    {
        return $this->hasMany(CustomTestDimension::class)->orderBy('sort_order');
    }

    public function questions()
    {
        return $this->hasMany(CustomTestQuestion::class)->orderBy('sort_order');
    }

    public function positionProfiles()
    {
        return $this->hasMany(PositionCustomTestProfile::class);
    }

    public function submissions()
    {
        return $this->hasMany(CustomTestSubmission::class);
    }
}
