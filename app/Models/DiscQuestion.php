<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscQuestion extends Model
{
    protected $fillable = ['question_number'];

    public function statements()
    {
        return $this->hasMany(DiscStatement::class);
    }
}

