<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proverbes', function (Blueprint $table) {
            // PK UUID (HasUuids + incrementing=false + keyType=string)
            $table->uuid('id')->primary();

            // Contenu
            $table->text('texte');           // le proverbe lui-même
            $table->string('langue', 20);    // ex: 'fr', 'en', 'baoulé', etc.

            // Relations
            $table->foreignUuid('regionId')
                  ->constrained('regions')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            // publié par un admin (user avec rôle admin côté applicatif)
            $table->foreignUuid('publieParAdminId')
                  ->constrained('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            // Métadonnées
            $table->timestamps();

            // Index utiles
            $table->index('langue');
            $table->index('regionId');
            $table->index('publieParAdminId');

            // (Optionnel) empêcher les doublons exacts d’un même proverbe dans une même région/langue
            // $table->unique(['texte', 'langue', 'regionId']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proverbes');
    }
};