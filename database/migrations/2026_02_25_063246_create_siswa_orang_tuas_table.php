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
        Schema::create('siswa_orang_tua', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('siswa_id')
                ->constrained('siswa')
                ->cascadeOnDelete()->nullable();

            // Ayah
            $table->string('nama_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('pendidikan_ayah')->nullable();
            $table->decimal('penghasilan_ayah', 15, 2)->nullable();

            // Ibu
            $table->string('nama_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('pendidikan_ibu')->nullable();
            $table->decimal('penghasilan_ibu', 15, 2)->nullable();

            // Wali
            $table->string('nama_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->string('pendidikan_wali')->nullable();
            $table->decimal('penghasilan_wali', 15, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_orang_tua');
    }
};
