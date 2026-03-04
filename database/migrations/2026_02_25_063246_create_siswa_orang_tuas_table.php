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
            $table->foreignUuid('siswa_id')->nullable()->constrained('siswa')->cascadeOnDelete();
            $table->foreignUuid('orang_tua_id')->nullable()->constrained('orang_tua')->cascadeOnDelete();

            // Informasi relasi spesifik
            $table->string('hubungan'); // Contoh: Ayah, Ibu, Wali, Kakak, Tante
            $table->boolean('kontak_utama')->default(false); // Untuk menentukan siapa yang dihubungi jika darurat

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
