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
        Schema::create('siswa_alamat', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('siswa_id')
                ->constrained('siswa')
                ->cascadeOnDelete()->nullable();

            $table->string('jenis_tinggal')->nullable();
            $table->text('alamat_tempat_tinggal')->nullable();

            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten_kota')->nullable();
            $table->string('provinsi')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_alamat');
    }
};
