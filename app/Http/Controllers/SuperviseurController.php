<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Pays;
use App\Models\Ville;
use App\Models\Statut;
use Illuminate\Support\Facades\Hash;
use App\helper\LogHelper; 

class SuperviseurController extends Controller
{
    //
    public function index()
    {
        return view('supervisseur.test');
    }

    public function profile()
    {
        $user = Auth::user();
        
        return view('supervisseur.profile', compact('user'));
    }

    public function updateProfile(Request $request, $id)
    {
        $user = Auth::user();
        
        // Log l'accès non autorisé si l'ID ne correspond pas
        if ($user->id != $id) {
            LogHelper::logAction(
                'Tentative de modification non autorisée de profil',
                'Le superviseur ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a tenté de modifier le profil d\'un autre utilisateur (ID ciblé: ' . $id . ').',
                $user->id
            );
            return redirect()->back()->with('error', 'Vous ne pouvez modifier que votre propre profil.');
        }
        
        $oldUserAttributes = $user->getOriginal(); // Capture les attributs originaux de l'utilisateur

        $validatedData = $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'telephone' => 'nullable|string',
            'cin' => 'required|string|unique:users,cin,' . $id,
            'adresse' => 'nullable|string',
            'current_password' => ['nullable', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $validatedInputFields = $request->only(['nom', 'prenom', 'email', 'telephone', 'cin', 'adresse']);

        // Si mot de passe renseigné, on le prépare ici
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        // Met à jour tous les autres champs
        $user->fill($validatedInputFields);

        // Sauvegarde tout en une seule fois
        $user->save();

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA MISE A JOUR DU PROFIL
        $changes = [];

        // Itérer sur les champs validés et les attributs potentiellement modifiés (comme le mot de passe)
        $fieldsToCompare = array_keys($validatedInputFields);
        if ($request->filled('new_password')) {
            $fieldsToCompare[] = 'password'; // Inclure le mot de passe pour le log si il a été modifié
        }
        
        foreach ($fieldsToCompare as $field) {
            // Utiliser $user->getOriginal() pour l'ancienne valeur avant le fill() initial
            $oldValue = $oldUserAttributes[$field] ?? null;
            $newValue = $user->{$field};

            if ($field === 'password') {
                if ($request->filled('new_password')) {
                    $changes[] = "Mot de passe: [changé]";
                }
            } else if ($oldValue != $newValue) {
                $changes[] = ucfirst(str_replace('_', ' ', $field)) . ": '" . ($oldValue ?? 'vide') . "' -> '" . ($newValue ?? 'vide') . "'";
            }
        }

        $message = 'Le superviseur ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a mis à jour son profil. ';
        if (!empty($changes)) {
            $message .= 'Changements: ' . implode(', ', $changes) . '.';
        } else {
            $message .= 'Aucun changement significatif détecté.';
        }

        LogHelper::logAction(
            'Mise à jour profil superviseur',
            $message,
            $user->id
        );
        
        return redirect()->back()->with('success', 'Profil mis à jour avec succès !');
    }

    
}