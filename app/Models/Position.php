<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = [
        'client_id',
        'title',
        'description',
        'is_active',
        'is_global',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_position')->withTimestamps();
    }

    public function profile()
    {
        return $this->hasOne(PositionDiscProfile::class);
    }

    public function mbtiProfiles()
    {
        return $this->hasMany(PositionMbtiProfile::class);
    }

    public function recommendations()
    {
        return $this->hasMany(DiscRecommendation::class);
    }

    public function mbtiRecommendations()
    {
        return $this->hasMany(MbtiRecommendation::class);
    }

    public function customTestProfiles()
    {
        return $this->hasMany(PositionCustomTestProfile::class);
    }
}
