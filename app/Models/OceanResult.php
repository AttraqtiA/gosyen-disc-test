<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OceanResult extends Model
{
    protected $fillable = [
        'ocean_test_id',
        'o_score',
        'c_score',
        'e_score',
        'a_score',
        'n_score',
        'dominant_trait',
    ];

    public function test()
    {
        return $this->belongsTo(OceanTest::class, 'ocean_test_id');
    }
}
