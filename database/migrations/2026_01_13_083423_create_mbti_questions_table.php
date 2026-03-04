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
        Schema::create('mbti_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('question_number')->unique();
            $table->text('text_a');
            $table->text('text_b');
            $table->enum('trait_a', ['E', 'I', 'S', 'N', 'T', 'F', 'J', 'P']);
            $table->enum('trait_b', ['E', 'I', 'S', 'N', 'T', 'F', 'J', 'P']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mbti_questions');
    }
};
