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
        Schema::create('halaqoh_siswa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('halaqoh_id')->nullable()->constrained('halaqoh')->cascadeOnDelete();
            $table->foreignUuid('siswa_id')->nullable()->constrained('siswa')->cascadeOnDelete();
            $table->string('tahun_ajaran')->nullable();
            $table->integer('semester')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('halaqoh_siswa');
    }
};
