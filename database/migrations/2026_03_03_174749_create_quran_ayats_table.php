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
        Schema::create('quran_ayat', function (Blueprint $table) {
            $table->id();
            $table->integer('surah');
            $table->integer('nomor')->nullable()->index();
            $table->text('ar');
            $table->text('tr');
            $table->text('idn');
            $table->timestamps();
            $table->foreign('surah')->references('nomor')->on('quran_surah')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_ayat');
    }
};
