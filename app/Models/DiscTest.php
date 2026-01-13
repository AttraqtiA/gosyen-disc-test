<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscTest extends Model
{
    protected $fillable = ['nama', 'usia', 'jenis_kelamin', 'tanggal_tes'];

    public function answers()
    {
        return $this->hasMany(DiscAnswer::class);
    }

    public function result()
    {
        return $this->hasOne(DiscResult::class);
    }
}

