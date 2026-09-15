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
        Schema::create('notifications', function (Blueprint $table) {
            // ID Notifikasi (UUID)
            $table->uuid('id')->primary();
            // Tipe Notifikasi (misal: 'App\Notifications\AdminActivityNotification')
            $table->string('type');
            // Polimorfisme: User mana yang menerima notifikasi (Admin/Guru)
            $table->morphs('notifiable');
            // Data notifikasi dalam format JSON
            $table->text('data');
            // Timestamp kapan notifikasi dibaca
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};