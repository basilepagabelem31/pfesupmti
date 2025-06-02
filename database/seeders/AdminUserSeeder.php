<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Statut;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Assurez-vous que le rôle 'Administrateur' existe
        // MODIFICATION ICI : Nous cherchons 'Administrateur' comme nom de rôle
        $adminRole = Role::firstOrCreate(['nom' => 'Administrateur'], ['description' => 'Administrateur du système']);

        // 2. Assurez-vous que le statut 'Actif' existe
        // Utilisez 'Active' si c'est ce qui est dans votre BD (selon les captures précédentes)
        $activeStatut = Statut::firstOrCreate(['nom' => 'Active'], ['description' => 'Compte actif']);


        // Vérifiez si les rôles ou statuts n'existent pas
        if (!$adminRole) {
            $this->command->error('Le rôle "Administrateur" n\'existe pas. Veuillez exécuter votre RoleSeeder.');
            return;
        }
        if (!$activeStatut) {
            $this->command->error('Le statut "Active" n\'existe pas. Veuillez exécuter votre StatutSeeder.');
            return;
        }

        // Supprimez l'utilisateur admin existant (par email) pour une recréation propre
        User::where('email', 'admin@example.com')->delete();

        // Créez l'utilisateur administrateur avec le nom et prénom spécifiés
        User::create(
            [
                'email' => 'bpagabelem78@gmail.com', // L'email de connexion reste le même
                'nom' => 'Pagabelem',        // MODIFICATION ICI : Nom
                'prenom' => 'Basile',        // MODIFICATION ICI : Prénom
                'cin' => 'AD123456u',         // CIN unique
                'telephone' => '0600000000',
                'password' => Hash::make('BASILO.PAG'), // Mot de passe par défaut
                'adresse' => '123 Rue de l\'Admin',
                'universite' => 'Admin University',
                'faculte' => 'Admin Faculty',
                'titre_formation' => 'Master Admin',
                'role_id' => $adminRole->id,
                'statut_id' => $activeStatut->id,
            ]
        );

        $this->command->info('Compte administrateur (Pagabelem Basile) réinitialisé et recréé.');
    }
}