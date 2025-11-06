<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commande_items', function (Blueprint $table) {
            // PK UUID (HasUuids dans le modèle)
            $table->uuid('id')->primary();

            // FK -> commandes.id (UUID)
            $table->foreignUuid('commandeId')
                  ->constrained('commandes', 'id')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete(); // si une commande est supprimée, on supprime ses lignes

            // FK -> produits.id
            // Si produits.id est un UUID, gardez foreignUuid :
            $table->foreignUuid('produitId')
                  ->constrained('produits', 'id')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete(); // empêche la suppression d’un produit encore référencé

            // Données de ligne
            $table->unsignedInteger('quantite');                 // cast integer
            $table->decimal('prixUnitaire', 12, 2);      // cast decimal:2

            // Index utiles
            $table->index('commandeId');
            $table->index('produitId');

            // Optionnel : empêcher les doublons de produit dans une même commande
            // Supprimez cette contrainte si vous voulez autoriser plusieurs lignes pour le même produit.
            $table->unique(['commandeId', 'produitId'], 'uniq_commandeitem_commande_produit');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commande_items');
    }
};