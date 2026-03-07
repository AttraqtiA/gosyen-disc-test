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
        Schema::create('release_notes', function (Blueprint $table) {
            $table->id();
            $table->string('version', 120);
            $table->string('git_commit', 64)->nullable();
            $table->string('operator', 120)->nullable();
            $table->string('environment', 50)->default('production');
            $table->string('status', 20)->default('success');
            $table->boolean('migrated')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('released_at')->useCurrent();
            $table->timestamps();

            $table->index(['version', 'released_at']);
            $table->index(['status', 'released_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('release_notes');
    }
};

