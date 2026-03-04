<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_test_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_test_id')->constrained('custom_tests')->cascadeOnDelete();
            $table->foreignId('test_session_id')->nullable()->constrained('test_sessions')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();

            $table->string('nama');
            $table->string('email')->nullable();
            $table->string('nomor_hp')->nullable();
            $table->string('institusi_perusahaan')->nullable();
            $table->string('departemen_divisi')->nullable();
            $table->string('jabatan_saat_ini')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('review_status', 30)->default('pending_review');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['custom_test_id', 'review_status']);
            $table->index(['client_id', 'review_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_test_submissions');
    }
};
