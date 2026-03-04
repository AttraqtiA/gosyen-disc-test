<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestSession extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'code',
        'test_type',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function discTests()
    {
        return $this->hasMany(DiscTest::class, 'test_session_id');
    }

    public function mbtiTests()
    {
        return $this->hasMany(MbtiTest::class, 'test_session_id');
    }

    public function oceanTests()
    {
        return $this->hasMany(OceanTest::class, 'test_session_id');
    }

    public function customTestSubmissions()
    {
        return $this->hasMany(CustomTestSubmission::class, 'test_session_id');
    }
}
