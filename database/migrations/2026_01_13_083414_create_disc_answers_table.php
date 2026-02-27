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
        Schema::create('disc_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disc_test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('disc_question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('p_statement_id')->constrained('disc_statements');
            $table->foreignId('k_statement_id')->constrained('disc_statements');
            $table->timestamps();

            $table->unique(['disc_test_id', 'disc_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disc_answers');
    }
};
