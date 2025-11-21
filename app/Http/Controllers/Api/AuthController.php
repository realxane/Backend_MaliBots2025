<?php

// App/Http/Controllers/Api/AuthController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Enums\Role;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'motDePasse' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            // Si ta colonne est "password"
            // if (! $user || ! Hash::check($request->motDePasse, $user->password)) { ... }

            // Si ta colonne est "motDePasse"
            if (! $user || ! Hash::check($request->motDePasse, $user->motDePasse)) {
                return response()->json(['message' => 'Identifiants invalides'], 401);
            }

            $token = $user->createToken('auth-token')->plainTextToken;

            $homePage = match ($user->role instanceof \BackedEnum ? $user->role->value : (string) $user->role) {
                'Admin' => 'admin_dashboard',
                'Vendeur', 'Acheteur' => 'home_user',
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

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Déconnexion réussie']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}