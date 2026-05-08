<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomTestSessionItem extends Model
{
    protected $fillable = [
        'test_session_id',
        'custom_test_id',
        'sort_order',
    ];

    public function session()
    {
        return $this->belongsTo(TestSession::class, 'test_session_id');
    }

    public function customTest()
    {
        return $this->belongsTo(CustomTest::class, 'custom_test_id');
    }
}
