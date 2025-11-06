<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table) {
            // PK UUID
            $table->uuid('id')->primary();

            // Champs métier
            $table->string('titre');
            $table->string('url', 2048);
            $table->text('description')->nullable();

            // Relations (UNE SEULE déclaration, via foreignUuid)
            $table->foreignUuid('regionId')
                  ->constrained('regions')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->foreignUuid('publieParAdminId')
                  ->constrained('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            // Timestamps
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};