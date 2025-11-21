<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des images
        Schema::create('produit_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('produitId')
                  ->constrained('produits')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->string('url', 2048);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['produitId', 'position']);
        });

        // Colonnes de rating sur produits
        Schema::table('produits', function (Blueprint $table) {
            $table->decimal('rating_avg', 3, 2)->default(0)->after('stock');
            $table->unsignedInteger('rating_count')->default(0)->after('rating_avg');
        });

        // Optionnel: si tu veux déprécier l’ancienne colonne mais la garder nullable
        try {
            Schema::table('produits', function (Blueprint $table) {
                // nécessite doctrine/dbal si ta DB ne supporte pas nativement change()
                $table->string('imageUrl', 2048)->nullable()->change();
            });
        } catch (\Throwable $e) {
            // Si change() indisponible, on ignore: la colonne restera non-nullable
        }

        // Backfill: migrer l’ancienne imageUrl vers produit_images si elle existe
        DB::table('produits')->select('id', 'imageUrl')->orderBy('id')->chunkById(500, function ($rows) {
            $now = now();
            $toInsert = [];
            foreach ($rows as $row) {
                if (!empty($row->imageUrl)) {
                    $toInsert[] = [
                        'id'         => (string) \Illuminate\Support\Str::uuid(),
                        'produitId'  => $row->id,
                        'url'        => $row->imageUrl,
                        'position'   => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            if ($toInsert) {
                DB::table('produit_images')->insert($toInsert);
            }
        });
    }

    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->dropColumn(['rating_avg', 'rating_count']);
        });

        Schema::dropIfExists('produit_images');
    }
};