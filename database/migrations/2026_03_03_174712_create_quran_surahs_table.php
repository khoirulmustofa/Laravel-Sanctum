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
        Schema::create('quran_surah', function (Blueprint $table) {
            $table->integer('nomor')->primary();
            $table->string('nama');
            $table->string('nama_latin');
            $table->integer('jumlah_ayat');
            $table->string('tempat_turun');
            $table->string('arti');
            $table->text('deskripsi');
            $table->string('audio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_surah');
    }
};
