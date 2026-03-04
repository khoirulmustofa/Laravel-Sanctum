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
        Schema::create('setoran_hafalan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('tanggal');
            $table->foreignUuid('halaqoh_id')->nullable()->constrained('halaqoh')->cascadeOnDelete();
            $table->foreignUuid('siswa_id')->nullable()->constrained('siswa')->cascadeOnDelete();
            $table->integer('surah')->nullable();
            $table->integer('ayat_awal')->nullable();
            $table->integer('ayat_akhir')->nullable();
            $table->integer('tajwid')->nullable();
            $table->integer('makhraj')->nullable();
            $table->integer('kelancaran')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('surah')->references('nomor')->on('quran_surah')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setoran_hafalan');
    }
};
