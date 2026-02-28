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
        Schema::table('positions', function (Blueprint $table) {
            $table->boolean('is_global')->default(false)->after('is_active');
        });

        Schema::table('position_disc_profiles', function (Blueprint $table) {
            $table->string('test_type')->default('DISC')->after('position_id');
            $table->index('test_type');
        });

        Schema::table('disc_recommendations', function (Blueprint $table) {
            $table->string('test_type')->default('DISC')->after('rank');
            $table->index('test_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disc_recommendations', function (Blueprint $table) {
            $table->dropIndex(['test_type']);
            $table->dropColumn('test_type');
        });

        Schema::table('position_disc_profiles', function (Blueprint $table) {
            $table->dropIndex(['test_type']);
            $table->dropColumn('test_type');
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('is_global');
        });
    }
};
