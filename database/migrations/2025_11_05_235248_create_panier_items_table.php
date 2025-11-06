<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('panier_items', function (Blueprint $table) {
            // Clé primaire UUID (HasUuids + incrementing=false + keyType=string)
            $table->uuid('id')->primary();

            // FKs
            $table->uuid('panierId');

            // Si produits.id est un UUID (recommandé si Produit utilise HasUuids)
            $table->uuid('produitId');

            // Données
            $table->unsignedInteger('quantite')->default(1);
            $table->decimal('prixUnitaire', 10, 2);

            // Index + intégrité référentielle
            $table->index(['panierId', 'produitId']);

            // Panier: si on supprime un panier, supprimer ses items
            $table->foreign('panierId')
                  ->references('id')->on('paniers')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            // Produit: empêcher la suppression d’un produit présent dans des paniers
            $table->foreign('produitId')
                  ->references('id')->on('produits')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            // Optionnel: empêcher les doublons (un même produit une seule ligne par panier)
            $table->unique(['panierId', 'produitId']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panier_items');
    }
};