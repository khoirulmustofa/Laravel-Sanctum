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
        Schema::create('siswa_kelas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('siswa_id')->constrained('siswa')->cascadeOnDelete()->nullable();
            $table->foreignUuid('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete()->nullable();
            $table->foreignUuid('semester_id')->constrained('semester')->cascadeOnDelete()->nullable();
            $table->foreignUuid('kelas_id')->constrained('kelas')->cascadeOnDelete()->nullable();
            $table->boolean('aktif')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_kelas');
    }
};
