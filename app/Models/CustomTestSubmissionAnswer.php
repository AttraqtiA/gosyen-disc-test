<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomTestSubmissionAnswer extends Model
{
    protected $fillable = [
        'custom_test_submission_id',
        'custom_test_question_id',
        'custom_test_option_id',
        'answer_text',
        'auto_scores_json',
        'reviewer_score',
        'reviewer_notes',
        'reviewed_by',
        'reviewed_at',
        'review_status',
    ];

    protected $casts = [
        'auto_scores_json' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(CustomTestSubmission::class, 'custom_test_submission_id');
    }

    public function question()
    {
        return $this->belongsTo(CustomTestQuestion::class, 'custom_test_question_id');
    }

    public function option()
    {
        return $this->belongsTo(CustomTestOption::class, 'custom_test_option_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
