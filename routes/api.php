<?php

use Illuminate\Support\Facades\Route;

// Vendeur
use App\Http\Controllers\Api\Vendeur\ProduitController;

// Acheteur
use App\Http\Controllers\Api\Acheteur\PanierController;
use App\Http\Controllers\Api\Acheteur\PanierItemController;
//Favoris
use App\Http\Controllers\Api\FavoriController;
use App\Http\Controllers\Api\Musique\MusiqueController;
use App\Http\Controllers\Api\galerie\PhotoController;

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

    //Favoris
    Route::get('favoris', [FavoriController::class, 'index']);
    Route::post('favoris', [FavoriController::class, 'store']);
    Route::post('favoris/toggle', [FavoriController::class, 'toggle']);
    Route::delete('favoris/{favori}', [FavoriController::class, 'destroy']);

});

//admins
Route::prefix('musiques')->group(function () {
    Route::get('/', [MusiqueController::class, 'index']);  // Public : lister toutes les musiques
    Route::post('/admin', [MusiqueController::class, 'store']);// Admin : publier une musique
    Route::delete('/admin/{id}', [MusiqueController::class, 'destroy']);// Admin : supprimer une musique
});

//NB:La bonne manière mais comme j'a pas un token pour admin je vais laisser apres si je récupere pour GD
// Route::prefix('musiques')->group(function () {
//     Route::get('/', [MusiqueController::class, 'index']);           // liste publique
//     Route::middleware('auth:sanctum')->group(function () {
//         Route::post('/admin', [MusiqueController::class, 'store']);   // publier
//         Route::delete('/admin/{id}', [MusiqueController::class, 'destroy']); // supprimer
//     });
// });

//photos
Route::prefix('photos')->group(function () {
    Route::get('/', [PhotoController::class, 'index']);         // lister
    Route::get('/{id}', [PhotoController::class, 'show']);      // voir une publication

    // admin (on peux ajouter middleware auth plus tard)
    Route::post('/admin', [PhotoController::class, 'store']);   // publier (multipart form-data)
    Route::post('/admin/{id}', [PhotoController::class, 'update']); 
    Route::delete('/admin/{id}', [PhotoController::class, 'destroy']);
    Route::delete('/admin/image/{imageId}', [PhotoController::class, 'deleteImage']); // supprimer image
});

// endpoint regions
Route::get('/regions', function() {
    return \App\Models\Region::all();
});