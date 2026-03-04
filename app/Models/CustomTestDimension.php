<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomTestDimension extends Model
{
    protected $fillable = [
        'custom_test_id',
        'code',
        'name',
        'weight',
        'sort_order',
    ];

    public function test()
    {
        return $this->belongsTo(CustomTest::class, 'custom_test_id');
    }
}
