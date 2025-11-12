<?php

use App\Http\Controllers\Api\Vendeur\ProduitController;
use App\Http\Controllers\Api\Musique\MusiqueController;
use App\Http\Controllers\Api\galerie\PhotoController;

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'checkrole:vendeur'])->prefix('vendeur')->group(function () {
    Route::apiResource('produits', ProduitController::class);
    // Restauration d’un produit soft-deleted
    Route::patch('produits/{id}/restore', [ProduitController::class, 'restore'])
        ->name('produits.restore');
});



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