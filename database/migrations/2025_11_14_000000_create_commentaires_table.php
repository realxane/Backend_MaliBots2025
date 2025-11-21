<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commentaires', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('produitId')
                ->constrained('produits')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

           $table->foreignUuid('acheteurId')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->text('contenu'); 

            $table->timestamps();
            $table->softDeletes();

            // Index utiles pour lister rapidement les commentaires d'un produit
            $table->index(['produitId', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commentaires');
    }
};