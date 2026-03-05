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
        Schema::create('orang_tua', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('pekerjaan')->nullable();
            $table->string('pendidikan')->nullable();
            $table->decimal('penghasilan', 15, 2)->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('email')->unique()->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orang_tua');
    }
};
