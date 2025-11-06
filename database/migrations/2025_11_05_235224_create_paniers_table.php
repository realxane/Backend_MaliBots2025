<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paniers', function (Blueprint $table) {
            // PK UUID (HasUuids + incrementing=false + keyType=string)
            $table->uuid('id')->primary();

            // Référence vers l'utilisateur (acheteur)
            // Variante 1: si users.id est un UUID (recommandé si tu utilises HasUuids sur User)
            $table->uuid('acheteurId');

            // Timestamps (le modèle n’a pas $timestamps = false)
            $table->timestamps();

            // Index + contrainte d’intégrité
            $table->index('acheteurId');
            $table->foreign('acheteurId')
                ->references('id')->on('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete(); // empêche la suppression d'un user avec panier associé

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paniers');
    }
};