<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_test_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_test_question_id')->constrained('custom_test_questions')->cascadeOnDelete();
            $table->text('option_text');
            $table->json('scores_json');
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_test_options');
    }
};
