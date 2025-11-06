<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppressions_produits', function (Blueprint $table) {
            // Clé primaire UUID
            $table->uuid('id')->primary();

            // Relations
            $table->uuid('produitId');
            $table->uuid('vendeurId');

            // created_at / updated_at (ton modèle n’a pas $timestamps = false)
            $table->timestamps();

            // FKs (adapte les noms de tables si différent)
            $table->foreign('produitId')
                ->references('id')->on('produits')
                ->cascadeOnDelete();

            $table->foreign('vendeurId')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            // Index utiles
            $table->index(['produitId', 'vendeurId']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppressions_produits');
    }
};