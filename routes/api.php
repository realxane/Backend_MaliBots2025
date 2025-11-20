<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;

Route::get('/', function () {
    return response()->json(["message" => "Bienvenue sur l’API MaliBots!"]);
});


// ---------------------------------------
// ROUTES PUBLIQUES
// ---------------------------------------
Route::post('/login', [AuthController::class, 'login']); 
Route::post('/users/create/{role}', [UserController::class, 'storeavecrole']);


// ---------------------------------------
// ROUTES PROTÉGÉES (utilisateur connecté)
// ---------------------------------------
Route::middleware(['auth:sanctum'])->group(function () {

    // Profil utilisateur
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/user/update_profile', [UserController::class, 'updateProfile']);

    // Ressources sécurisées
    Route::apiResource('regions', RegionController::class);
    Route::apiResource('users', UserController::class);

    // -----------------------
    // ROUTES ADMIN
    // -----------------------
    Route::middleware(['role:Admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index']);
        Route::get('/admin/users', [AdminController::class, 'getAllUsers']);
        Route::patch('/admin/user/toggle/{id}', [AdminController::class, 'toggleUserStatus']);
        Route::patch('/admin/user/role/{id}', [AdminController::class, 'updateRole']);
    });

});
