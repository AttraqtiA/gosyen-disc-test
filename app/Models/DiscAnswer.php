<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscAnswer extends Model
{
    protected $fillable = [
        'disc_test_id',
        'disc_question_id',
        'p_statement_id',
        'k_statement_id'
    ];

    public function test()
    {
        return $this->belongsTo(DiscTest::class);
    }

    public function question()
    {
        return $this->belongsTo(DiscQuestion::class);
    }

    public function pStatement()
    {
        return $this->belongsTo(DiscStatement::class, 'p_statement_id');
    }

    public function kStatement()
    {
        return $this->belongsTo(DiscStatement::class, 'k_statement_id');
    }
}

