<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ocean_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('question_number')->unique();
            $table->text('statement');
            $table->enum('trait', ['O', 'C', 'E', 'A', 'N']);
            $table->boolean('reverse_scored')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ocean_questions');
    }
};
