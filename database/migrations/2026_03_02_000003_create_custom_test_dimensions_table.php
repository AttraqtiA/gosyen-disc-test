<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_test_dimensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_test_id')->constrained('custom_tests')->cascadeOnDelete();
            $table->string('code', 20);
            $table->string('name');
            $table->unsignedTinyInteger('weight')->default(1);
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->timestamps();

            $table->unique(['custom_test_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_test_dimensions');
    }
};
