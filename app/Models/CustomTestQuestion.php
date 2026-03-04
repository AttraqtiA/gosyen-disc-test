<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomTestQuestion extends Model
{
    protected $fillable = [
        'custom_test_id',
        'question_text',
        'question_type',
        'sort_order',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function test()
    {
        return $this->belongsTo(CustomTest::class, 'custom_test_id');
    }

    public function options()
    {
        return $this->hasMany(CustomTestOption::class, 'custom_test_question_id')->orderBy('sort_order');
    }
}
