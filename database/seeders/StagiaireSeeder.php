<?php

namespace Database\Seeders;

use App\Models\Pays;
use App\Models\Role;
use App\Models\Statut;
use App\Models\User;
use App\Models\Ville;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StagiaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pays = Pays::firstOrCreate(['id' => 134], ['nom' => 'TonPays']);
        $ville = Ville::firstOrCreate(['id' => 66709], ['nom' => 'TaVille', 'pays_id' => $pays->id]);
        $statut = Statut::firstOrCreate(['id' => 1], ['nom' => 'Actif']);
        // Récupère les id des rôles par nom
       
        $stagiaireRole = Role::where('nom', 'Stagiaire')->first();
        $now = now();
       

        // Utilisateur Stagiaire
        User::create([
            'nom' => 'Stagiaire_2',
            'prenom' => 'Test_2',
            'email' => 'stagiaire_2@test.com',
            'password' => Hash::make('stagiaire_2'),
            'telephone' => '0100004443',
            'cin' => 'AEEEE',
            'adresse' => 'Adresse stagiaire_2',
            'role_id' => $stagiaireRole->id ?? 3,
           'pays_id' => $pays->id,
            'ville_id' => $ville->id,
            'statut_id' => $statut->id,
            'universite' => 'Université Test_2',
            'faculte' => 'Faculté Test_2',
            'titre_formation' => 'Formation Test_2',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Utilisateur Stagiaire
        User::create([
            'nom' => 'Stagiaire_3',
            'prenom' => 'Test_3',
            'email' => 'stagiaire_3@test.com',
            'password' => Hash::make('stagiaire_3'),
            'telephone' => '45700004443',
            'cin' => 'AEIIE',
            'adresse' => 'Adresse stagiaire_3',
            'role_id' => $stagiaireRole->id ?? 3,
           'pays_id' => $pays->id,
            'ville_id' => $ville->id,
            'statut_id' => $statut->id,
            'universite' => 'Université Test_3',
            'faculte' => 'Faculté Test_3',
            'titre_formation' => 'Formation Test_3',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

          // Utilisateur Stagiaire
        User::create([
            'nom' => 'Stagiaire_4',
            'prenom' => 'Test_4',
            'email' => 'stagiaire_4@test.com',
            'password' => Hash::make('stagiaire_4'),
            'telephone' => '47485489554',
            'cin' => 'TYUGGT',
            'adresse' => 'Adresse stagiaire_4',
            'role_id' => $stagiaireRole->id ?? 3,
           'pays_id' => $pays->id,
            'ville_id' => $ville->id,
            'statut_id' => $statut->id,
            'universite' => 'Université Test_4',
            'faculte' => 'Faculté Test_4',
            'titre_formation' => 'Formation Test_4',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}