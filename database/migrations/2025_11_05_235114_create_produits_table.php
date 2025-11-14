<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produits', function (Blueprint $table) {
            // PK UUID (HasUuids + incrementing=false + keyType=string)
            $table->uuid('id')->primary();

            // Champs métier
            $table->string('nom');
            $table->text('description')->nullable();
            $table->decimal('prix', 12, 2);
            $table->unsignedInteger('stock')->default(0); // idéalement après 'prix'

            // Enums: on stocke les valeurs des enums PHP (backed enums)
            $table->string('categorie', 50);
            $table->string('statut', 50);

            // URL d'image potentiellement longue
            $table->string('imageUrl', 2048);

            // Relations
            $table->foreignUuid('regionId')
                  ->constrained('regions')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->foreignUuid('vendeurId')
                  ->constrained('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            // Timestamps + Soft deletes (car le modèle use SoftDeletes)
            $table->timestamps();
            $table->softDeletes();

            // Index utiles
            $table->index(['categorie', 'statut']);
            $table->index('regionId');
            $table->index('vendeurId');
        });
    } 

    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};