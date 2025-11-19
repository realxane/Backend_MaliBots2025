<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\Role;
use App\Models\User;
use App\Models\Region;
use Illuminate\Http\Request;

class UserController extends Controller
{
     // 1. Lister tous les utilisateurs
    public function index()

    { try{
        $users = User::with('region')->get(); // inclut la région
        return response()->json($users);
        }catch (\Throwable $e) {
        // Retourne une erreur claire si quelque chose échoue
        return response()->json([
            'message' => 'Erreur de chargement de l’utilisateur',
            'error' => $e->getMessage()
        ], 500);
    }
    }
    //  2. Afficher un utilisateur par UUID
    public function show($id)
    {
        $user = User::with('region')->find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }

        return response()->json($user);
    }

    

   public function store(Request $request)
{
    try {
        // Récupération du rôle et conversion en enum
        $roleInput = ucfirst(strtolower($request->input('role')));
        $role = Role::tryFrom($roleInput);
        if (!$role) {
            return response()->json([
                'message' => 'Rôle invalide'
            ], 400);
        }

        // Validation
        $rules = [
            'nom' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'motDePasse' => 'required|string|min:6',
            'region' => 'required|string|max:150',
        ];

        if ($role === Role::Vendeur || $role === Role::Admin) {
            $rules['telephone_pro'] = 'nullable|string|max:30';
            $rules['descrit_ton_savoir_faire'] = 'nullable|string|max:255';
        }

        $validated = $request->validate($rules);

        // Création ou récupération de la région
        $region = Region::firstOrCreate(['nom' => $validated['region']]);

        // Création de l'utilisateur
        $user = User::create([
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'motDePasse' => $validated['motDePasse'], // hash automatique grâce à $casts
            'role' => $role,
            'regionId' => $region->id,
            'telephone' => $request->input('telephone'),
            'telephone_pro' => $request->input('telephone_pro'),
            'descrit_ton_savoir_faire' => $request->input('descrit_ton_savoir_faire'),
            'isActive' => true,
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès !',
            'user' => $user
        ], 201);

    } catch (\Throwable $e) {
        // Retourne une erreur claire si quelque chose échoue
        return response()->json([
            'message' => 'Erreur lors de la création de l’utilisateur',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function storeavecrole(Request $request, $role)
{
    try {
        
    $role = ucfirst(strtolower($role)); // transforme "vendeur" → "Vendeur"

    $validated = $request->validate([
        'nom' => 'required|string|max:120',
        'email' => 'required|email|unique:users,email',
        'motDePasse' => 'required|string|min:6',
        'region' => 'required|string|max:150',
        'telephone' => 'nullable|string|max:30',
    ]);

    $region = Region::firstOrCreate(['nom' => $validated['region']]);

    $user = User::create([
        'nom' => $validated['nom'],
        'email' => $validated['email'],
        'motDePasse' => $validated['motDePasse'],
        'role' => $role, 
        'regionId' => $region->id,
        'telephone' => $validated['telephone'] ?? null,
        'telephone_pro' => $request->telephone_pro,
        'descrit_ton_savoir_faire' => $request->descrit_ton_savoir_faire,
    ]);

    return response()->json(['user' => $user], 201);
        }
        catch (\Throwable $e) {
        // Retourne une erreur claire si quelque chose échoue
        return response()->json([
            'message' => 'Erreur lors de la création de l’utilisateur',
            'error' => $e->getMessage()
        ], 500);
    }
}

 // 4. Mettre à jour un utilisateur
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }

        $rules = [
            'nom' => 'sometimes|string|max:120',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'motDePasse' => 'sometimes|string|min:6',
            'region' => 'sometimes|string|max:150',
            'telephone' => 'sometimes|string|max:30',
            'telephone_pro' => 'sometimes|string|max:30',
            'descrit_ton_savoir_faire' => 'sometimes|string|max:255',
        ];

        $validated = $request->validate($rules);

        if (isset($validated['region'])) {
            $region = Region::firstOrCreate(['nom' => $validated['region']]);
            $validated['regionId'] = $region->id;
        }

        if (isset($validated['motDePasse'])) {
            $validated['motDePasse'] = $validated['motDePasse']; // hash automatique via $casts
        }

        $user->update($validated);

        return response()->json(['message' => 'Utilisateur mis à jour', 'user' => $user]);
    }
     public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }

  
}
