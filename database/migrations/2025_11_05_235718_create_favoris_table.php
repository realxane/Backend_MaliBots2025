<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favoris', function (Blueprint $table) {
            // PK UUID (HasUuids + $incrementing=false + $keyType='string')
            $table->uuid('id')->primary();

            // FK vers users (BIGINT par défaut dans Laravel)
           $table->foreignUuid('utilisateurId')
                    ->constrained('users', 'id')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();

            // Polymorphisme "maison" (types stockés en string; valeurs mappées par l'Enum TypeFavori)
            $table->string('cibleType', 100);

            // Si les cibles utilisent des UUID (conseillé si vos contenus sont en UUID)
            $table->uuid('cibleId');

            $table->timestamps();

            // Empêche qu'un même utilisateur "re-favorise" la même cible
            $table->unique(['utilisateurId', 'cibleType', 'cibleId']);

            // Index pour retrouver rapidement les favoris d'une cible
            $table->index(['cibleType', 'cibleId']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favoris');
    }
};