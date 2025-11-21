<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /*
      Dashboard admin : simple message de test
     */
    public function index()
    {
        try {
        return response()->json([
            'message' => 'Bienvenue sur le Dashboard Admin',
            'status' => 'success'
        ], 200);
        } catch (\Throwable $e) {
            // Retourne une erreur claire si quelque chose échoue
            return response()->json([
                'message' => 'Erreur ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lister tous les utilisateurs
     */
    public function getAllUsers()
    {
        $users = User::all();

        return response()->json([
            'total' => $users->count(),
            'users' => $users
        ], 200);
    }

    /**
     * Activer ou désactiver un utilisateur
     */
    public function toggleUserStatus($id)
    {
        try {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur introuvable'
            ], 404);
        }

        // Inverser l'état
        $user->isActive = !$user->isActive;
        $user->save();

        return response()->json([
            'message' => 'Statut mis à jour',
            'user' => $user
        ], 200);
        } catch (\Throwable $e) {
            // Retourne une erreur claire si quelque chose échoue
            return response()->json([
                'message' => 'Erreur ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modifier le rôle d’un utilisateur
     * Exemple : Vendeur → Admin
     */
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:Admin,Vendeur,Acheteur'
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur introuvable'
            ], 404);
        }

        $user->role = $request->role;
        $user->save();

        return response()->json([
            'message' => 'Rôle mis à jour',
            'user' => $user
        ], 200);
    }
    public function getUsersByRole($role)
{
    try {
        // Vérification si le rôle existe
        $validRoles = ['Admin', 'Vendeur', 'Acheteur'];

        if (!in_array($role, $validRoles)) {
            return response()->json([
                'message' => 'Rôle invalide',
                'roles_acceptes' => $validRoles
            ], 400);
        }

        // Recherche des utilisateurs du rôle demandé
        $users = User::where('role', $role)->get();

        return response()->json([
            'role' => $role,
            'total' => $users->count(),
            'users' => $users
        ], 200);
    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Erreur lors de la récupération',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
