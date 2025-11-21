<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user(); 

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Si c'est une BackedEnum, on prend ->value, sinon on garde tel quel
        $currentRole = $user->role instanceof \BackedEnum ? $user->role->value : $user->role;

        if (! in_array($currentRole, $roles, true)) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        return $next($request);
    } 
}