<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\Role;
use App\Models\User;
use App\Models\Region;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
{
    $role = Role::from($request->input('role')); // convertit la string en enum

    // Validation de base
    $rules = [
        'nom' => 'required|string|max:120',
        'email' => 'required|email|unique:users,email',
        'motDePasse' => 'required|string|min:6',
        'region' => 'required|string|max:150',
    ];

    // Validation dynamique selon le rôle
    if ($role === Role::Vendeur || $role === Role::Admin) {
        $rules['telephone_pro'] = 'nullable|string|max:30';
        $rules['descrit_ton_savoir_faire'] = 'nullable|string|max:255';
    }

    $validated = $request->validate($rules);

    // Gestion de la région
    $region = Region::firstOrCreate(['nom' => $validated['region']]);

    // Création de l'utilisateur
    $user = User::create([
        'nom' => $validated['nom'],
        'email' => $validated['email'],
        'motDePasse' => $validated['motDePasse'],
        'role' => $role,
        'regionId' => $region->id,
        'telephone_pro' => $request->input('telephone_pro'),
        'descrit_ton_savoir_faire' => $request->input('descrit_ton_savoir_faire'),
    ]);

    return response()->json(['message' => 'Utilisateur créé', 'user' => $user], 201);
}
  
}
