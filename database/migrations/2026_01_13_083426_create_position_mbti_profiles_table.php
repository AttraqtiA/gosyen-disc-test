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
        Schema::create('position_mbti_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->string('test_type')->default('MBTI');
            $table->unsignedTinyInteger('e_target')->default(50);
            $table->unsignedTinyInteger('i_target')->default(50);
            $table->unsignedTinyInteger('s_target')->default(50);
            $table->unsignedTinyInteger('n_target')->default(50);
            $table->unsignedTinyInteger('t_target')->default(50);
            $table->unsignedTinyInteger('f_target')->default(50);
            $table->unsignedTinyInteger('j_target')->default(50);
            $table->unsignedTinyInteger('p_target')->default(50);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['position_id', 'test_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_mbti_profiles');
    }
};
