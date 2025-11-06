<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validations_produits', function (Blueprint $table) {
            // PK UUID (HasUuids + $incrementing=false + $keyType='string' côté modèle)
            $table->uuid('id')->primary();

            // Clés de relation (adaptables selon le type d'ID des tables cibles)
            $table->uuid('produitId');
            $table->uuid('adminId');

            // Décision (stockée comme string par défaut pour ton enum PHP)
            $table->string('decision', 50);

            // Timestamps (présents car $timestamps n'est plus à false dans le modèle)
            $table->timestamps();

            // FKs
            $table->foreign('produitId')
                ->references('id')->on('produits')
                ->cascadeOnDelete();

            $table->foreign('adminId')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            // Index utiles
            $table->index(['produitId', 'adminId']);
            $table->index('decision');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validations_produits');
    }
};