<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('musiques', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('titre', 255);
            $table->string('artist', 255)->nullable();
            $table->string('genre', 100)->nullable();
            $table->string('urlImage', 2048)->nullable();
            $table->string('urlAudio', 2048)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('musiques');
    }
};
