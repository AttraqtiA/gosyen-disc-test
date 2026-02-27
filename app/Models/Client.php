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
        return $this->hasMany(Position::class);
    }

    public function tests()
    {
        return $this->hasMany(DiscTest::class);
    }
}
