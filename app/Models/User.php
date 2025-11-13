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
        // On récupère le rôle envoyé et on le convertit en enum
        $role = Role::from($request->input('role'));

        // Validation de base
        $rules = [
            'nom' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'motDePasse' => 'required|string|min:6',
            'region' => 'required|string|max:150',
        ];

        // Validation dynamique pour vendeur ou admin
        if ($role === Role::Vendeur || $role === Role::Admin) {
            $rules['telephone_pro'] = 'nullable|string|max:30';
            $rules['descrit_ton_savoir_faire'] = 'nullable|string|max:255';
        }

        // Validation
        $validated = $request->validate($rules);

        // Gestion de la région (création si elle n'existe pas)
        $region = Region::firstOrCreate(['nom' => $validated['region']]);

        // Création de l'utilisateur
        $user = User::create([
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'motDePasse' => $validated['motDePasse'], // sera hashé automatiquement grâce à $casts
            'role' => $role,
            'regionId' => $region->id,
            'telephone' => $request->input('telephone'), // si tu veux récupérer le téléphone perso
            'telephone_pro' => $request->input('telephone_pro'),
            'descrit_ton_savoir_faire' => $request->input('descrit_ton_savoir_faire'),
            'isActive' => true,
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user
        ], 201);
    }
}
