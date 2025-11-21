<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Favori;

class FavoriPolicy
{
    public function delete(User $user, Favori $favori): bool
    {
        return $favori->utilisateurId === $user->id;
    }
}