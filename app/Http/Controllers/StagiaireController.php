<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Note;
use App\Models\Fichier;
use App\Models\Pays;
use App\Models\Ville;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\helper\LogHelper; 

class StagiaireController extends Controller
{
    //
    public function index ()
    {
        return view('stagiaires.dashboard');
    }

    public function profiles()
    {
        $user = Auth::user()->load('notes', 'fichiers');
        
        return view('stagiaires.profiles', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'telephone' => 'nullable|string|max:20',
            'cin' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'current_password' => ['nullable', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'universite' => 'nullable|string|max:255',
            'faculte' => 'nullable|string|max:255',
            'titre_formation' => 'nullable|string|max:255',
        ]);

        $validatedData = $request->only([
            'nom', 'prenom', 'email', 'telephone', 'cin', 'adresse',
            'universite', 'faculte','titre_formation', 
        ]);

        $user = User::findOrFail($id);
        $oldUserAttributes = $user->getOriginal(); // Capture les attributs originaux de l'utilisateur

        // Si mot de passe renseigné, on le prépare ici
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        // Met à jour tous les autres champs
        $user->fill($validatedData);

        // Sauvegarde tout en une seule fois
        $user->save();

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA MISE A JOUR
        $loggedInUser = Auth::user();
        $changes = [];

        // Itérer sur les champs qui peuvent être modifiés et comparer
        $fieldsToCheck = array_keys($validatedData); // Champs du formulaire validés
        if ($request->filled('new_password')) {
            $fieldsToCheck[] = 'password'; // Ajouter le mot de passe pour le log si changé
        }

        foreach ($fieldsToCheck as $field) {
            $oldValue = $oldUserAttributes[$field] ?? null;
            $newValue = $user->{$field};

            // Gérer spécifiquement le changement de mot de passe
            if ($field === 'password') {
                if ($request->filled('new_password')) {
                    $changes[] = "Mot de passe: [changé]";
                }
            } else if ($oldValue != $newValue) {
                // Pour les autres champs, enregistrer l'ancienne et la nouvelle valeur
                $changes[] = ucfirst(str_replace('_', ' ', $field)) . ": '" . ($oldValue ?? 'vide') . "' -> '" . ($newValue ?? 'vide') . "'";
            }
        }

        $message = 'Le ' . $loggedInUser->role->nom . ' ' . $loggedInUser->prenom . ' ' . $loggedInUser->nom . ' (ID: ' . $loggedInUser->id . ') a mis à jour le profil du stagiaire ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . '). ';
        if (!empty($changes)) {
            $message .= 'Changements: ' . implode(', ', $changes) . '.';
        } else {
            $message .= 'Aucun changement significatif détecté.';
        }

        LogHelper::logAction(
            'Mise à jour profil stagiaire',
            $message,
            $loggedInUser->id
        );
        
        return redirect()->back()->with('success', 'Profil mis à jour avec succès !');
    }

    
}