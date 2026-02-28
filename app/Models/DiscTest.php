<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscTest extends Model
{
    protected $fillable = [
        'client_id',
        'test_session_id',
        'nama',
        'email',
        'nomor_hp',
        'institusi_perusahaan',
        'departemen_divisi',
        'jabatan_saat_ini',
        'usia',
        'jenis_kelamin',
        'pendidikan_terakhir',
        'lama_pengalaman_kerja',
        'lokasi_kota',
        'tujuan_tes',
        'tanggal_tes',
        'started_at',
        'submitted_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'tanggal_tes' => 'date',
    ];

    public function answers()
    {
        return $this->hasMany(DiscAnswer::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function session()
    {
        return $this->belongsTo(TestSession::class, 'test_session_id');
    }

    public function result()
    {
        return $this->hasOne(DiscResult::class);
    }

    public function recommendations()
    {
        return $this->hasMany(DiscRecommendation::class);
    }
}
