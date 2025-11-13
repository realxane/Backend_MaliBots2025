<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\UserController;

Route::get('/', function () {
    return response()->json(['message' => 'Bienvenue sur l’API MaliBots!']);
});

Route::apiResource('regions', RegionController::class);
Route::apiResource('users', UserController::class);