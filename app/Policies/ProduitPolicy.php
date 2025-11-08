<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProduitPolicy
{
    use HandlesAuthorization;

    protected function isOwner(User $user, Produit $produit): bool
    {
        return $produit->vendeurId === $user->id;
    }

    public function viewAny(User $user): bool
    {
        return $user->role === Role::Vendeur;
    }

    public function view(User $user, Produit $produit): bool
    {
        return $this->isOwner($user, $produit);
    }

    public function create(User $user): bool
    {   
        \Log::debug('ProduitPolicy@create', [
            'user_id' => $user->id,
            'role_raw' => $user->role,
            'role_type' => gettype($user->role),
            'is_enum' => $user->role instanceof \BackedEnum,
        ]);
        return $user->role === Role::Vendeur;
    }

    public function update(User $user, Produit $produit): bool
    {
        return $this->isOwner($user, $produit);
    }

    public function delete(User $user, Produit $produit): bool
    {
        return $this->isOwner($user, $produit);
    }

    public function restore(User $user, Produit $produit): bool
    {
        return $this->isOwner($user, $produit);
    }
}