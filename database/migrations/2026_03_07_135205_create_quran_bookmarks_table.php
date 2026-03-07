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
        Schema::create('quran_bookmarks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('user_id')->nullable();
            $table->integer('surah_nomor')->nullable();
            $table->integer('ayat_id')->nullable();
            $table->timestamps();

            // relation
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('surah_nomor')->references('nomor')->on('quran_surah')->onDelete('cascade');
            $table->foreign('ayat_id')->references('id')->on('quran_ayat')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_bookmarks');
    }
};
