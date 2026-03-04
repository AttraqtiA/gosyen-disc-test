<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomTestSubmission extends Model
{
    protected $fillable = [
        'custom_test_id',
        'test_session_id',
        'client_id',
        'nama',
        'email',
        'nomor_hp',
        'institusi_perusahaan',
        'departemen_divisi',
        'jabatan_saat_ini',
        'started_at',
        'submitted_at',
        'review_status',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function customTest()
    {
        return $this->belongsTo(CustomTest::class, 'custom_test_id');
    }

    public function session()
    {
        return $this->belongsTo(TestSession::class, 'test_session_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function answers()
    {
        return $this->hasMany(CustomTestSubmissionAnswer::class, 'custom_test_submission_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
