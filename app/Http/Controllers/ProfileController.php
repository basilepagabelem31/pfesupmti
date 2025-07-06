<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Log; // N'oubliez pas d'importer votre modèle Log
use Illuminate\Validation\ValidationException; // Pour capturer les exceptions de validation

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Enregistrement du log : Affichage du formulaire de profil
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_profile_form',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}) a accédé à son formulaire de profil.",
            'object_snapshot' => [
                'user_id' => $user->id,
                'email' => $user->email,
            ],
        ]);

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $oldEmail = $user->email; // Capture l'ancien email pour le log
        $oldName = $user->nom;
        $oldPrenom = $user->prenom;

        try {
            $user->fill($request->validated());

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
                // Enregistrement du log : Changement d'email détecté
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'profile_email_change_detected',
                    'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}) a changé son adresse e-mail de '{$oldEmail}' à '{$user->email}'. La vérification de l'e-mail a été réinitialisée.",
                    'object_snapshot' => [
                        'user_id' => $user->id,
                        'old_email' => $oldEmail,
                        'new_email' => $user->email,
                    ],
                ]);
            }

            $user->save();

            // Enregistrement du log : Mise à jour du profil réussie
            Log::create([
                'user_id' => $user->id,
                'action' => 'profile_update_success',
                'log_message' => "L'utilisateur '{$oldName} {$oldPrenom}' (ID: {$user->id}) a mis à jour son profil avec succès. Nouveau nom: '{$user->nom} {$user->prenom}'.",
                'object_snapshot' => [
                    'user_id' => $user->id,
                    'old_nom' => $oldName,
                    'old_prenom' => $oldPrenom,
                    'old_email' => $oldEmail,
                    'new_nom' => $user->nom,
                    'new_prenom' => $user->prenom,
                    'new_email' => $user->email,
                ],
            ]);

            return Redirect::route('profile.edit')->with('status', 'profile-updated');

        } catch (ValidationException $e) {
            // Enregistrement du log : Échec de validation lors de la mise à jour du profil
            Log::create([
                'user_id' => $user->id,
                'action' => 'profile_update_validation_failed',
                'log_message' => "Échec de validation lors de la tentative de mise à jour du profil par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'user_id' => $user->id,
                    'input_data' => $request->all(),
                    'errors' => $e->errors(),
                ],
            ]);
            // Rendre l'erreur à l'utilisateur
            throw $e; // Relaunch the exception so Laravel's default error handling for validation can take over
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la mise à jour du profil
            Log::create([
                'user_id' => $user->id,
                'action' => 'profile_update_critical_error',
                'log_message' => "Erreur critique inattendue lors de la mise à jour du profil par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'user_id' => $user->id,
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return Redirect::route('profile.edit')->with('error', 'Une erreur est survenue lors de la mise à jour de votre profil.');
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        $userName = $user->nom . ' ' . $user->prenom;
        $userId = $user->id;

        try {
            $request->validateWithBag('userDeletion', [
                'password' => ['required', 'current_password'],
            ]);

            Auth::logout();

            $user->delete();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Enregistrement du log : Suppression de compte réussie
            Log::create([
                'user_id' => $userId, // Utilisation de l'ID capturé avant la suppression de l'objet user
                'action' => 'account_deletion_success',
                'log_message' => "Le compte de l'utilisateur '{$userName}' (ID: {$userId}) a été supprimé avec succès.",
                'object_snapshot' => [
                    'deleted_user_id' => $userId,
                    'deleted_user_name' => $userName,
                ],
            ]);

            return Redirect::to('/');

        } catch (ValidationException $e) {
            // Enregistrement du log : Échec de validation du mot de passe lors de la suppression de compte
            Log::create([
                'user_id' => $userId,
                'action' => 'account_deletion_password_validation_failed',
                'log_message' => "Échec de validation du mot de passe lors de la tentative de suppression de compte par l'utilisateur '{$userName}' (ID: {$userId}).",
                'object_snapshot' => [
                    'user_id' => $userId,
                    'errors' => $e->errors(),
                ],
            ]);
            return Redirect::back()->withErrors($e->errors(), 'userDeletion');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la suppression de compte
            Log::create([
                'user_id' => $userId,
                'action' => 'account_deletion_critical_error',
                'log_message' => "Erreur critique inattendue lors de la tentative de suppression de compte par l'utilisateur '{$userName}' (ID: {$userId}).",
                'object_snapshot' => [
                    'user_id' => $userId,
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return Redirect::back()->with('error', 'Une erreur est survenue lors de la suppression de votre compte.');
        }
    }
}