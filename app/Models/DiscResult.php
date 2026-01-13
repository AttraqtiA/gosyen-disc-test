<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscResult extends Model
{
    protected $fillable = [
        'disc_test_id',
        'd_score',
        'i_score',
        's_score',
        'c_score',
        'dominant_type'
    ];

    public function test()
    {
        return $this->belongsTo(DiscTest::class);
    }
}
