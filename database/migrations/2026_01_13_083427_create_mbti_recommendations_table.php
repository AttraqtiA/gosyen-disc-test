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
        Schema::create('mbti_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mbti_test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->decimal('match_score', 5, 2);
            $table->unsignedTinyInteger('rank');
            $table->string('test_type')->default('MBTI');
            $table->enum('source', ['deterministic', 'gpt'])->default('deterministic');
            $table->timestamps();

            $table->unique(['mbti_test_id', 'position_id']);
            $table->index(['mbti_test_id', 'rank']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mbti_recommendations');
    }
};
