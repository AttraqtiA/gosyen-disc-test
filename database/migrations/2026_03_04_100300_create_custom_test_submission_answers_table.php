<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_test_submission_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_test_submission_id')->constrained('custom_test_submissions')->cascadeOnDelete();
            $table->foreignId('custom_test_question_id')->constrained('custom_test_questions')->cascadeOnDelete();
            $table->foreignId('custom_test_option_id')->nullable()->constrained('custom_test_options')->nullOnDelete();

            $table->text('answer_text')->nullable();
            $table->json('auto_scores_json')->nullable();

            $table->integer('reviewer_score')->nullable();
            $table->text('reviewer_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('review_status', 30)->default('pending_review');
            $table->timestamps();

            $table->unique(['custom_test_submission_id', 'custom_test_question_id'], 'custom_submission_question_unique');
            $table->index(['review_status', 'reviewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_test_submission_answers');
    }
};
