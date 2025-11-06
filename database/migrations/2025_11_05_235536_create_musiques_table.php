<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('musiques', function (Blueprint $table) {
            // PK UUID (HasUuids + $incrementing=false + $keyType='string')
            $table->uuid('id')->primary();

            // Champs du modèle
            $table->string('titre', 255);
            // URLs: string long pour éviter les coupes (pas d'index ici)
            $table->string('fichierUrl', 2048)->nullable();
            $table->string('couvertureUrl', 2048)->nullable();

            // Duree en secondes (cast 'integer')
            $table->unsignedInteger('duree')->nullable();

            // dateSortie (cast 'datetime')
            $table->dateTime('dateSortie')->nullable()->index();

            // Pas de timestamps (conforme au modèle)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('musiques');
    }
};