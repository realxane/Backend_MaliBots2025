<?php

use Illuminate\Support\Facades\Route;

// Vendeur
use App\Http\Controllers\Api\Vendeur\ProduitController;

// Acheteur
use App\Http\Controllers\Api\Acheteur\PanierController;
use App\Http\Controllers\Api\Acheteur\PanierItemController;

Route::middleware('auth:sanctum')->group(function () {

    // Vendeur
    Route::prefix('vendeur')->as('vendeur.')->middleware('checkrole:vendeur')->group(function () {
        Route::apiResource('produits', ProduitController::class);
        // Restauration d’un produit soft-deleted
        Route::patch('produits/{id}/restore', [ProduitController::class, 'restore'])
            ->name('produits.restore');
    });

    // Acheteur
    Route::prefix('acheteur')->as('acheteur.')->middleware('checkrole:acheteur')->group(function () {
        Route::get('panier', [PanierController::class, 'show'])->name('panier.show');
        Route::delete('panier', [PanierController::class, 'clear'])->name('panier.clear');

        Route::post('panier/items', [PanierItemController::class, 'store'])->name('panier.items.store');
        Route::patch('panier/items/{item}', [PanierItemController::class, 'update'])->name('panier.items.update');
        Route::delete('panier/items/{item}', [PanierItemController::class, 'destroy'])->name('panier.items.destroy');
    });
});