<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signalements', function (Blueprint $table) {
            // PK UUID (HasUuids + incrementing=false + keyType=string)
            $table->uuid('id')->primary();

            // Qui a créé le signalement
            $table->uuid('utilisateurId');
            $table->foreign('utilisateurId')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->string('cibleType', 191);
            $table->uuid('cibleId');

            // Statut (casté vers App\Enums\StatutSignalement)
            // Par défaut en string; si ton enum est int-backed, voir notes ci-dessous
            $table->string('statut', 50);

            // Admin qui a traité (optionnel)
            $table->uuid('traiteParAdminId')->nullable();
            $table->foreign('traiteParAdminId')
                ->references('id')->on('users')
                ->nullOnDelete();

            // Date de traitement (optionnelle)
            $table->timestamp('dateTraitement')->nullable();

            // Le modèle n’a pas $timestamps = false; donc on garde created_at / updated_at
            $table->timestamps();

            // Index utiles
            $table->index(['cibleType', 'cibleId']);
            $table->index('statut');
            $table->index('dateTraitement');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signalements');
    }
};