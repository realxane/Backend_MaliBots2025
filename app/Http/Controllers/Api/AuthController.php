<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Enums\Role;

class AuthController extends Controller
{
    /**
     * Connexion utilisateur / admin
     */
    public function login(Request $request)
    {
        try{
        // Validation des champs
        $request->validate([
            'email' => 'required|email',
            'motDePasse' => 'required|string',
        ]);

        // Récupérer l'utilisateur par email
        $user = User::where('email', $request->email)->first();

        if (!$user || !password_verify($request->motDePasse, $user->motDePasse)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        // Créer un token pour l'utilisateur
        $token = $user->createToken('auth-token')->plainTextToken;

        // Déterminer la page d'accueil selon le rôle
        $homePage = match ($user->role->value) {
            Role::Admin->value => 'admin_dashboard',
            Role::Vendeur->value, Role::Acheteur->value => 'home_user',
            default => 'home_user',
        };

        return response()->json([
            'message' => 'Connexion réussie',
            'user' => $user,
            'token' => $token,
            'home_page' => $homePage
        ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        // Supprimer tous les tokens (ou juste celui actuel)
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }

    /**
     * Récupérer les informations de l'utilisateur connecté
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
