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
        Schema::create('siswa', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('sekolah_id')->constrained('sekolah')->cascadeOnDelete()->nullable();

            // Identitas peserta didik
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('nisn')->nullable()->unique();
            $table->string('nik')->nullable();

            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama')->nullable();

            // Data tambahan
            $table->integer('tinggi_badan')->nullable();
            $table->string('berkebutuhan_khusus')->nullable();

            $table->string('no_telepon_rumah')->nullable();
            $table->integer('jarak_ke_sekolah')->nullable(); // km
            $table->string('alat_transportasi')->nullable();
            $table->string('email_pribadi')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
