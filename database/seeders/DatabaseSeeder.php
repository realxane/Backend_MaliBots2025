<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
   public function run()
{
    $this->call([
        // RegionSeeder::class, // si tu en as un
        AdminSeeder::class,
    ]);
}

}
