<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscStatement extends Model
{
    protected $fillable = ['disc_question_id', 'text', 'disc_type'];

    public function question()
    {
        return $this->belongsTo(DiscQuestion::class);
    }
}

