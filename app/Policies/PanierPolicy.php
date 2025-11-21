<?php

namespace App\Policies;

use App\Models\Panier;
use App\Models\User;

class PanierPolicy
{
    public function view(User $user, Panier $panier): bool
    {
        return $panier->acheteurId === $user->id;
    }

    public function update(User $user, Panier $panier): bool
    {
        return $panier->acheteurId === $user->id;
    }

    public function delete(User $user, Panier $panier): bool
    {
        return $panier->acheteurId === $user->id;
    }

    public function clear(User $user, Panier $panier): bool
    {
        return $panier->acheteurId === $user->id;
    }
}