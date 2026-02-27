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
        Schema::create('disc_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nama');
            $table->string('email')->nullable();
            $table->string('nomor_hp')->nullable();
            $table->string('institusi_perusahaan')->nullable();
            $table->string('departemen_divisi')->nullable();
            $table->string('jabatan_saat_ini')->nullable();
            $table->integer('usia');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('pendidikan_terakhir')->nullable();
            $table->unsignedTinyInteger('lama_pengalaman_kerja')->nullable();
            $table->string('lokasi_kota')->nullable();
            $table->string('tujuan_tes')->nullable();
            $table->date('tanggal_tes');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disc_tests');
    }
};
