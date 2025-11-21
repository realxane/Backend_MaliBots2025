<?php

namespace App\Providers;

use App\Models\Produit;
use App\Models\Panier;
use App\Models\Favori;
use App\Policies\ProduitPolicy;
use App\Policies\PanierPolicy;
use App\Policies\FavoriPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Produit::class => ProduitPolicy::class,
        Panier::class => PanierPolicy::class,
        Favori::class  => FavoriPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}