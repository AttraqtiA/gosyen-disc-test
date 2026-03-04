<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ocean_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ocean_test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->decimal('match_score', 5, 2);
            $table->unsignedTinyInteger('rank');
            $table->string('test_type')->default('OCEAN');
            $table->enum('source', ['deterministic', 'gpt'])->default('deterministic');
            $table->timestamps();

            $table->unique(['ocean_test_id', 'position_id']);
            $table->index(['ocean_test_id', 'rank']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ocean_recommendations');
    }
};
