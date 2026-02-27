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
        Schema::create('disc_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disc_test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->decimal('match_score', 5, 2);
            $table->unsignedTinyInteger('rank');
            $table->enum('source', ['deterministic', 'gpt'])->default('deterministic');
            $table->timestamps();

            $table->unique(['disc_test_id', 'position_id']);
            $table->index(['disc_test_id', 'rank']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disc_recommendations');
    }
};
