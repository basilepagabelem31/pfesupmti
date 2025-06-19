<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\helper\LogHelper; 

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $oldUserAttributes = $user->getOriginal(); // Capture les attributs originaux de l'utilisateur

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA MISE A JOUR DU PROFIL
        $changes = [];
        $fillableAttributes = array_keys($request->validated()); // Seuls les champs validés peuvent être mis à jour

        foreach ($fillableAttributes as $attribute) {
            $oldValue = $oldUserAttributes[$attribute] ?? 'Non défini';
            $newValue = $user->{$attribute};

            if ($oldValue != $newValue) {
                // Masquer les changements de mot de passe pour la sécurité
                if ($attribute === 'password') {
                    $changes[] = ucfirst($attribute) . ": Changé";
                } else {
                    $changes[] = ucfirst(str_replace('_', ' ', $attribute)) . ": '" . $oldValue . "' -> '" . $newValue . "'";
                }
            }
        }

        $message = 'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a mis à jour son profil.';
        if (!empty($changes)) {
            $message .= ' Changements: ' . implode(', ', $changes) . '.';
        } else {
            $message .= ' Aucun changement significatif détecté (peut-être seulement des détails internes).';
        }

        LogHelper::logAction(
            'Mise à jour de profil',
            $message,
            $user->id
        );

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Capture les informations de l'utilisateur avant la déconnexion et la suppression pour le log
        $deletedUserName = $user->prenom . ' ' . $user->nom;
        $deletedUserEmail = $user->email;
        $deletedUserId = $user->id;
        $deletedUserRole = $user->role->nom;


        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA SUPPRESSION DE COMPTE
        LogHelper::logAction(
            'Suppression de compte',
            'Le compte du ' . $deletedUserRole . ' ' . $deletedUserName . ' (Email: ' . $deletedUserEmail . ', ID: ' . $deletedUserId . ') a été supprimé.',
            $deletedUserId // Utiliser l'ID de l'utilisateur supprimé comme user_id si possible, ou null/super_admin_id
        );

        return Redirect::to('/');
    }
}