<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        User::updateOrCreate(
            ['email' => 'lelenta21@gmail.com'], // Vérifie si un utilisateur avec cet email existe déjà.
            [
                'nom' => 'Lelenta',
                'prenom' => 'Cheick',
                'email' => 'lelenta21@gmail.com',
                'password' => Hash::make('lelenta21'),
                'cin' => 'AB123456',
                'code' => 'STAG20250611',
                'telephone' => '773120198',
                'adresse' => '123 Rue de Paris, 75001, Paris',
                'universite' => 'Université de Paris',
                'faculte' => 'Faculté des Sciences',
                'titre_formation' => 'Licence Informatique',
                'pays_id' => 134,
                'ville_id' => 66709,
                'groupe_id' => 1,
                'role_id' => 3,
                'statut_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        echo "Utilisateur créé ou mis à jour avec succès.";
    }
}
