<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_contents', function (Blueprint $table) {
            $table->id();
            $table->text('history_content')->nullable();
            $table->text('vision_content')->nullable();
            $table->text('mission_content')->nullable();
            $table->text('about_excerpt')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_contents');
    }
};