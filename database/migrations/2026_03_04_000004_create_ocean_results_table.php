<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ocean_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ocean_test_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('o_score')->default(0);
            $table->unsignedTinyInteger('c_score')->default(0);
            $table->unsignedTinyInteger('e_score')->default(0);
            $table->unsignedTinyInteger('a_score')->default(0);
            $table->unsignedTinyInteger('n_score')->default(0);
            $table->char('dominant_trait', 1)->nullable();
            $table->timestamps();

            $table->unique('ocean_test_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ocean_results');
    }
};
