<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                "nom" => "Administrateur",
                'description' => " Dispose de toutes les autorisations et peut réaliser l’ensemble des opérations",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                "nom" => "Superviseur",
                'description' => "Partage la plupart des droits du super administrateur, avec certaines restrictions ",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                "nom" => "Stagiaire",
                'description' => "Une personne qui est en stage dans l'entreprise ",
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}