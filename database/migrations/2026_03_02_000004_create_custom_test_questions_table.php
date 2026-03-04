<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_test_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_test_id')->constrained('custom_tests')->cascadeOnDelete();
            $table->text('question_text');
            $table->string('question_type')->default('single_choice');
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_test_questions');
    }
};
