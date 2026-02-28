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
        Schema::table('disc_tests', function (Blueprint $table) {
            $table->foreignId('test_session_id')->nullable()->after('client_id')->constrained('test_sessions')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disc_tests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('test_session_id');
        });
    }
};
