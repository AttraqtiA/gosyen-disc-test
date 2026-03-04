<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    public function positions()
    {
        return $this->belongsToMany(Position::class, 'client_position')->withTimestamps();
    }

    public function ownedPositions()
    {
        return $this->hasMany(Position::class);
    }

    public function tests()
    {
        return $this->hasMany(DiscTest::class);
    }

    public function mbtiTests()
    {
        return $this->hasMany(MbtiTest::class);
    }

    public function oceanTests()
    {
        return $this->hasMany(OceanTest::class);
    }

    public function sessions()
    {
        return $this->hasMany(TestSession::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
