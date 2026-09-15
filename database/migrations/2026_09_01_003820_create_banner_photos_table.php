<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banner_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banner_id')->constrained('banners')->cascadeOnDelete();
            $table->string('file_path');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banner_photos');
    }
};