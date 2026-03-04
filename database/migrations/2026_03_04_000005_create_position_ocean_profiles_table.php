<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('position_ocean_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->string('test_type')->default('OCEAN');
            $table->unsignedTinyInteger('o_target')->default(50);
            $table->unsignedTinyInteger('c_target')->default(50);
            $table->unsignedTinyInteger('e_target')->default(50);
            $table->unsignedTinyInteger('a_target')->default(50);
            $table->unsignedTinyInteger('n_target')->default(50);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['position_id', 'test_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('position_ocean_profiles');
    }
};
