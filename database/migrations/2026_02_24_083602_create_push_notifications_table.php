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
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            // Relasi ke user
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Konten Notifikasi
            $table->string('title');
            $table->text('body');
            $table->string('image')->nullable();

            // Metadata (Sangat penting untuk navigasi di Ionic)
            $table->string('type')->nullable(); // misal: 'announcement', 'payment', 'assignment'
            $table->string('related_id')->nullable(); // ID pengumuman/tugas yang terkait

            // Status Pelacakan
            $table->boolean('is_sent')->default(false);
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_notifications');
    }
};
