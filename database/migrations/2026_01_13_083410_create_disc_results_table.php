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
        Schema::create('disc_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disc_test_id')->constrained()->cascadeOnDelete();
            $table->integer('d_score')->default(0);
            $table->integer('i_score')->default(0);
            $table->integer('s_score')->default(0);
            $table->integer('c_score')->default(0);
            $table->enum('dominant_type', ['D', 'I', 'S', 'C'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disc_results');
    }
};
