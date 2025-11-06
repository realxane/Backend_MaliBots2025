<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            // PK UUID (HasUuids dans le modèle)
            $table->uuid('id')->primary();

            // Acheteur (FK -> users.id)
            // Suppose que users.id est un UUID. Voir note ci-dessous si ce n'est pas le cas.
            $table->foreignUuid('acheteurId')
                  ->constrained('users', 'id')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            // Montant total
            $table->decimal('montantTotal', 12, 2);

            // Enums PHP castés côté modèle -> on stocke en string (ou int selon vos enums)
            $table->string('methodePaiement', 50);
            $table->string('statut', 50);

            // Timestamps (le modèle n'a pas $timestamps = false)
            $table->timestamps();

            // Index utiles
            $table->index('acheteurId');
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};