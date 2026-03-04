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
        Schema::create('mbti_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mbti_test_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('e_score')->default(0);
            $table->unsignedTinyInteger('i_score')->default(0);
            $table->unsignedTinyInteger('s_score')->default(0);
            $table->unsignedTinyInteger('n_score')->default(0);
            $table->unsignedTinyInteger('t_score')->default(0);
            $table->unsignedTinyInteger('f_score')->default(0);
            $table->unsignedTinyInteger('j_score')->default(0);
            $table->unsignedTinyInteger('p_score')->default(0);
            $table->char('type_code', 4)->nullable();
            $table->timestamps();

            $table->unique('mbti_test_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mbti_results');
    }
};
