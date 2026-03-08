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
            $table->unsignedInteger('number')->primary();
            $table->unsignedInteger('sequence');
            $table->unsignedInteger('number_of_verses');
            $table->string('name_short');
            $table->text('name_long');
            $table->text('name_en');
            $table->string('name_id');
            $table->string('translation_en');
            $table->string('translation_id');
            $table->string('revelation');
            $table->string('revelation_en');
            $table->string('revelation_id');
            $table->text('tafsir');
            $table->string('audio_url');

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
