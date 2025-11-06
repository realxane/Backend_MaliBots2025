<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            // PK UUID (HasUuids + incrementing=false + keyType=string)
            $table->uuid('id')->primary();

            // FK vers commandes (colonne personnalisée 'commandeId')
            $table->uuid('commandeId');

            // Montant en devise, non négatif
            $table->decimal('montant', 10, 2);

            // Enums applicatives (backed enums PHP) :
            // si tes enums sont "string-backed" => string
            // si elles sont "int-backed", voir l’alternative plus bas.
            $table->string('methode', 50);
            $table->string('statut', 30)->index();

            // Référence du PSP/fournisseur (souvent unique)
            $table->string('referenceFournisseur', 191)->nullable()->unique();

            // Timestamps (le modèle n’a pas $timestamps = false)
            $table->timestamps();

            // Index + contrainte d’intégrité
            $table->index('commandeId');
            $table->foreign('commandeId')
                ->references('id')->on('commandes')
                ->cascadeOnUpdate()
                ->restrictOnDelete(); // protège les paiements si on tente de supprimer une commande
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};