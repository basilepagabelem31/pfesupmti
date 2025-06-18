<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            PaysVilleSeeder::class,
            RoleSeeder::class,
            StatutSeeder::class,
            UserSeeder::class ,
            // AdminUserSeeder::class,
            // GroupeSeeder::class,
            // StagiaireSeeder::class,
            // TestSeeder::class,
        ]);
    }
}
