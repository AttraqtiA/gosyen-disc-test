<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_test_questions', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('question_text');
        });

        Schema::table('custom_test_options', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('option_text');
        });

        Schema::table('custom_test_submissions', function (Blueprint $table) {
            $table->uuid('packet_attempt_uuid')->nullable()->after('test_session_id');
            $table->unsignedSmallInteger('packet_index')->default(1)->after('packet_attempt_uuid');
            $table->unsignedSmallInteger('packet_size')->default(1)->after('packet_index');
            $table->index(['test_session_id', 'packet_attempt_uuid'], 'custom_submissions_session_packet_idx');
        });

        Schema::create('custom_test_session_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_session_id')->constrained('test_sessions')->cascadeOnDelete();
            $table->foreignId('custom_test_id')->constrained('custom_tests')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->timestamps();

            $table->unique(['test_session_id', 'custom_test_id']);
            $table->unique(['test_session_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_test_session_items');

        Schema::table('custom_test_submissions', function (Blueprint $table) {
            $table->dropIndex('custom_submissions_session_packet_idx');
            $table->dropColumn(['packet_attempt_uuid', 'packet_index', 'packet_size']);
        });

        Schema::table('custom_test_options', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        Schema::table('custom_test_questions', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
