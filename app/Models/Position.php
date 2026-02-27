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
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function profile()
    {
        return $this->hasOne(PositionDiscProfile::class);
    }

    public function recommendations()
    {
        return $this->hasMany(DiscRecommendation::class);
    }
}
