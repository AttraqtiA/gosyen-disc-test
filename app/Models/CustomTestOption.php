<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomTestOption extends Model
{
    protected $fillable = [
        'custom_test_question_id',
        'option_text',
        'scores_json',
        'sort_order',
    ];

    protected $casts = [
        'scores_json' => 'array',
    ];

    public function question()
    {
        return $this->belongsTo(CustomTestQuestion::class, 'custom_test_question_id');
    }
}
