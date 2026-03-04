<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('position_custom_test_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->foreignId('custom_test_id')->constrained('custom_tests')->cascadeOnDelete();
            $table->json('target_scores_json');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['position_id', 'custom_test_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('position_custom_test_profiles');
    }
};
