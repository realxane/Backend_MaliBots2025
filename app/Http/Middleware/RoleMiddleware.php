<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Vérifie si l'utilisateur connecté a le rôle requis.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        // Assurer que le rôle est une string pour comparaison
        $userRole = is_object($user->role) && property_exists($user->role, 'value') 
                    ? $user->role->value 
                    : $user->role;

        if ($userRole !== $role) {
            return response()->json(['message' => 'Accès interdit pour ce rôle'], 403);
        }

        return $next($request);
    }
}
