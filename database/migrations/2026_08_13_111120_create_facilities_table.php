<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            
            // Diubah ke nullOnDelete() agar tidak error saat media lama dihapus controller
            $table->foreignId('photo_media_id')
                  ->nullable()
                  ->constrained('media')
                  ->nullOnDelete();

            $table->unsignedInteger('sort_order')->default(0);
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->softDeletes();
            $table->timestamps();

            // Indexing untuk optimasi query orderBy & pencarian
            $table->index(['sort_order', 'name']);
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};