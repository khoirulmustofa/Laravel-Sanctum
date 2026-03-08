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

            $table->unsignedInteger('surah');
            $table->unsignedInteger('ayah');

            $table->text('arab');
            $table->text('latin');

            $table->unsignedInteger('page');
            $table->unsignedInteger('juz');

            $table->decimal('hizb', 4, 1)->nullable();

            $table->unsignedInteger('asbab')->default(0);

            $table->string('audio')->nullable();
            $table->string('theme')->nullable();

            $table->text('text')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('surah')
                ->references('number')
                ->on('quran_surah')
                ->cascadeOnDelete();
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
