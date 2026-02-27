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
        Schema::create('position_disc_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('d_target')->default(25);
            $table->unsignedTinyInteger('i_target')->default(25);
            $table->unsignedTinyInteger('s_target')->default(25);
            $table->unsignedTinyInteger('c_target')->default(25);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('position_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_disc_profiles');
    }
};
