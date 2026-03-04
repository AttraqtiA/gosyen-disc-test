<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ocean_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ocean_test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ocean_question_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score')->comment('Likert 1-5');
            $table->timestamps();

            $table->unique(['ocean_test_id', 'ocean_question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ocean_answers');
    }
};
