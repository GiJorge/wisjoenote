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
        Schema::create('notes', function (Blueprint $table) {
          $table->id(); // Auto-incrementing primary key
            $table->string('title');
            $table->text('content')->nullable();
            $table->text('amharic_content')->nullable();
            $table->string('category')->nullable();
            $table->timestamps(); // Generates created_at and updated_at datetime tracks
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
