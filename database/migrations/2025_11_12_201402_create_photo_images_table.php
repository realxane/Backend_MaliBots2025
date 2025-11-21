<?php

// database/migrations/xxxx_xx_xx_create_photo_images_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photo_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('photoId')
                  ->constrained('photos')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete(); // si publication supprimée => images supprimées

            $table->string('filename', 255);        // nom sur le disque
            $table->string('url', 2048);            // url publique (storage)
            $table->string('mime', 100)->nullable();
            $table->unsignedInteger('size')->nullable(); // en octets
            $table->integer('order')->default(0);  // si besoin d'ordre d'affichage

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photo_images');
    }
};

