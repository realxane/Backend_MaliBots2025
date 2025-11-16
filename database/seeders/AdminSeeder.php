<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Region;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Créer / récupérer la région d'abord
        $region = Region::firstOrCreate(
            ['nom' => 'koulikoro'] // si tu veux une région par défaut
            // si ton modèle Region génère l'UUID automatiquement, pas besoin d'indiquer 'id'
        );

        // 2) Créer l'admin si il n'existe pas (on cherche par email)
        $user = User::firstOrCreate(
            ['email' => 'youss210.com@gmail.com'], // critère de recherche
            [
                'id' => (string) Str::uuid(),
                'nom' => 'Super Admin Youssouf',
                'motDePasse' => 'youss223.com', // sera hashé si tu as 'hashed' cast
                'role' => 'Admin',
                'telephone' => '93153124',
                'telephone_pro' => '93153124',
                'descrit_ton_savoir_faire' => 'Super Admin',
                'regionId' => $region->id,
                'isActive' => true,
            ]
        );

        // Optionnel : afficher un message en console
        $this->command->info("Admin seed terminé. Admin email: {$user->email}");
    }
}
