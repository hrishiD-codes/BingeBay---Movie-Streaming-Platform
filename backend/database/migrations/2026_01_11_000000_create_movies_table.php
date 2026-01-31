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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path'); // Absolute path to video file
            $table->string('thumbnail_path')->nullable(); // Path to poster/thumbnail
            $table->integer('duration')->nullable(); // Duration in seconds
            $table->string('genre')->nullable();
            $table->year('release_year')->nullable();
            $table->decimal('rating', 3, 1)->nullable(); // e.g., 8.5
            $table->bigInteger('file_size')->nullable(); // File size in bytes
            $table->string('video_codec')->nullable();
            $table->string('resolution')->nullable(); // e.g., 1920x1080
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
