<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contes', function (Blueprint $table) {
            // PK en UUID
            $table->uuid('id')->primary();

            // Colonnes métier
            $table->string('titre', 255);
            $table->longText('histoire');
            $table->string('langue', 10)->index();

            // Foreign keys en UUID
            $table->foreignUuid('regionId')
                  ->constrained('regions')      // référence regions.id (uuid)
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->foreignUuid('publieParAdminId')
                  ->constrained('users')        // référence users.id (uuid)
                  ->cascadeOnUpdate();

            $table->timestamps();

            // Index utiles
            $table->index('titre');
            // NB: pas besoin de ré-indexer regionId/publieParAdminId, la FK crée un index implicite.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contes');
    }
};