<?php

namespace App\Providers;

use App\Models\Produit;
use App\Policies\ProduitPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Produit::class => ProduitPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}