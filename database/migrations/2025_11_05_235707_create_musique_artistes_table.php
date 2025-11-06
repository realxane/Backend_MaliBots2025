<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('musique_artistes', function (Blueprint $table) {
            $table->uuid('musiqueId');
            $table->uuid('artisteId');

            $table->primary(['musiqueId', 'artisteId']);

            $table->foreign('musiqueId')
                ->references('id')->on('musiques')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('artisteId')
                ->references('id')->on('artistes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('musique_artistes');
    }
};