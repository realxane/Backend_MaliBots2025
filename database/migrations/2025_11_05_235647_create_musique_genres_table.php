<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('musique_genres', function (Blueprint $table) {
            $table->uuid('musiqueId');
            $table->uuid('genreId');

            // Empêche les doublons
            $table->primary(['musiqueId', 'genreId']);

            // FKs + nettoyage en cascade
            $table->foreign('musiqueId')
                ->references('id')->on('musiques')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('genreId')
                ->references('id')->on('genres')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('musique_genres');
    }
};