<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mbti_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mbti_test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mbti_question_id')->constrained()->cascadeOnDelete();
            $table->enum('selected_trait', ['E', 'I', 'S', 'N', 'T', 'F', 'J', 'P']);
            $table->timestamps();

            $table->unique(['mbti_test_id', 'mbti_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mbti_answers');
    }
};
