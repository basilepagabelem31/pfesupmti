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

class UserSeeder extends Seeder
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
        $adminRole = Role::where('nom', 'Administrateur')->first();
        // $superviseurRole = Role::where('nom', 'Superviseur')->first();
        // $stagiaireRole = Role::where('nom', 'Stagiaire')->first();
        $now = now();
        // Utilisateur Administrateur
        User::create([
            'nom' => 'Admin',
            'prenom' => 'Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin'),
            'telephone' => '0100000001',
            'cin' => 'CINADMIN1',
            'adresse' => 'Adresse Admin',
            'role_id' => $adminRole->id ?? 1,
            'pays_id' => $pays->id,
            'ville_id' => $ville->id,
            'statut_id' => $statut->id,
            'universite' => null,
            'faculte' => null,
            'titre_formation' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Utilisateur Superviseur
        // User::create([
        //     'nom' => 'Superviseur',
        //     'prenom' => 'Test',
        //     'email' => 'superviseur@test.com',
        //     'password' => Hash::make('superviseur'),
        //     'telephone' => '0100000002',
        //     'cin' => 'CINSUP1',
        //     'adresse' => 'Adresse Superviseur',
        //     'role_id' => $superviseurRole->id ?? 2,
        //     'pays_id' => $pays->id,
        //     'ville_id' => $ville->id,
        //     'statut_id' => $statut->id,
        //     'universite' => null,
        //     'faculte' => null,
        //     'titre_formation' => null,
        //     'created_at' => $now,
        //     'updated_at' => $now,
        // ]);

        // Utilisateur Stagiaire
        // User::create([
        //     'nom' => 'Stagiaire',
        //     'prenom' => 'Test',
        //     'email' => 'stagiaire@test.com',
        //     'password' => Hash::make('stagiaire'),
        //     'telephone' => '0100000003',
        //     'cin' => 'CINSTAG1',
        //     'adresse' => 'Adresse Stagiaire',
        //     'role_id' => $stagiaireRole->id ?? 3,
        //    'pays_id' => $pays->id,
        //     'ville_id' => $ville->id,
        //     'statut_id' => $statut->id,
        //     'universite' => 'Université Test',
        //     'faculte' => 'Faculté Test',
        //     'titre_formation' => 'Formation Test',
        //     'created_at' => $now,
        //     'updated_at' => $now,
        // ]);
    }
}