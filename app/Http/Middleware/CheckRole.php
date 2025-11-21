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

        $current = $user->role instanceof \BackedEnum ? $user->role->value : (string) $user->role;

        $ok = collect($roles)->contains(function ($r) use ($current) {
            return mb_strtolower($r) === mb_strtolower((string) $current);
        });

        if (! $ok) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        return $next($request);
    }
}