<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Pays;
use App\Models\Ville;
use App\Models\Statut;
use Illuminate\Support\Facades\Hash;
use App\Models\Log; // Importez votre modèle Log
use Illuminate\Validation\ValidationException; // Pour capturer les exceptions de validation

class SuperviseurController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Obtenez l'utilisateur (superviseur) connecté

        // Enregistrement du log : Accès au tableau de bord du superviseur
        Log::create([
            'user_id' => $user->id,
            'action' => 'superviseur_dashboard_view',
            'log_message' => "Le superviseur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a accédé à son tableau de bord.",
            'object_snapshot' => [
                'superviseur_id' => $user->id,
            ],
        ]);

        return view('supervisseur.dashboard');
    }

    public function profile()
    {
        $user = Auth::user(); // Obtenez l'utilisateur (superviseur) connecté

        // Enregistrement du log : Consultation du profil du superviseur
        Log::create([
            'user_id' => $user->id,
            'action' => 'superviseur_profile_view',
            'log_message' => "Le superviseur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a consulté son profil.",
            'object_snapshot' => [
                'superviseur_id' => $user->id,
                'email' => $user->email,
            ],
        ]);
        
        return view('supervisseur.profile', compact('user'));
    }

    public function updateProfile(Request $request, $id)
    {
        $user = Auth::user(); // L'utilisateur qui tente la modification
        $targetUser = null; // L'utilisateur dont le profil est modifié
        $oldUserData = null; // Pour capturer l'état avant la mise à jour

        try {
            // Vérifier que le superviseur ne peut modifier que son propre profil
            if ($user->id != $id) {
                // Enregistrement du log : Tentative de modification de profil non autorisée
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'superviseur_profile_update_unauthorized',
                    'log_message' => "Tentative non autorisée de modification du profil de l'utilisateur ID: {$id} par le superviseur ID: {$user->id}.",
                    'object_snapshot' => [
                        'attempted_user_id' => $user->id,
                        'target_user_id' => $id,
                        'request_data' => $request->all(),
                    ],
                ]);
                return redirect()->back()->with('error', 'Vous ne pouvez modifier que votre propre profil.');
            }

            $targetUser = User::findOrFail($id);
            $oldUserData = $targetUser->toArray(); // Capture l'état avant la mise à jour

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

            // Si un nouveau mot de passe est fourni, le hacher et le mettre à jour
            if ($request->filled('new_password')) {
                $targetUser->password = Hash::make($request->new_password);
                session()->flash('password_changed', true); // Ajoute un flash de session pour le succès du changement de mot de passe
                // Log spécifique pour le changement de mot de passe
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'superviseur_password_changed',
                    'log_message' => "Le superviseur '{$user->nom} {$user->prenom}' (ID: {$user->id}) a changé son mot de passe.",
                    'object_snapshot' => [
                        'superviseur_id' => $user->id,
                    ],
                ]);
            }

            // Mettre à jour tous les autres champs
            // Utilisez $validatedData directement, car $request->only() est déjà fait par validate()
            $targetUser->fill($request->only(['nom', 'prenom', 'email', 'telephone', 'cin', 'adresse']));

            // Si l'email a été modifié, réinitialiser email_verified_at
            if ($targetUser->isDirty('email')) {
                $targetUser->email_verified_at = null;
                // Log spécifique si l'email change
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'superviseur_email_changed',
                    'log_message' => "Le superviseur '{$user->nom} {$user->prenom}' (ID: {$user->id}) a changé son adresse e-mail de '{$oldUserData['email']}' à '{$targetUser->email}'. La vérification de l'e-mail a été réinitialisée.",
                    'object_snapshot' => [
                        'superviseur_id' => $user->id,
                        'old_email' => $oldUserData['email'],
                        'new_email' => $targetUser->email,
                    ],
                ]);
            }

            // Sauvegarder toutes les modifications
            $targetUser->save();

            // Enregistrement du log : Mise à jour du profil superviseur réussie
            Log::create([
                'user_id' => $user->id,
                'action' => 'superviseur_profile_updated_success',
                'log_message' => "Le profil du superviseur '{$targetUser->nom} {$targetUser->prenom}' (ID: {$targetUser->id}) a été mis à jour avec succès par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'superviseur_id' => $targetUser->id,
                    'old_data' => $oldUserData,
                    'new_data' => $targetUser->toArray(),
                ],
            ]);

            return redirect()->back()->with('success', 'Profil mis à jour avec succès !');

        } catch (ValidationException $e) {
            // Enregistrement du log : Échec de validation lors de la mise à jour du profil superviseur
            Log::create([
                'user_id' => $user->id,
                'action' => 'superviseur_profile_update_validation_failed',
                'log_message' => "Échec de validation lors de la tentative de mise à jour du profil du superviseur ID: {$id} par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'target_user_id' => $id,
                    'input' => $request->all(),
                    'errors' => $e->errors(),
                ],
            ]);
            return back()->withInput()->withErrors($e->errors())->with('error', 'Veuillez corriger les erreurs dans le formulaire.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Enregistrement du log : Superviseur non trouvé pour mise à jour
            Log::create([
                'user_id' => $user->id,
                'action' => 'superviseur_profile_update_not_found',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}) a tenté de modifier un profil de superviseur inexistant (ID: {$id}).",
                'object_snapshot' => [
                    'target_user_id_attempted' => $id,
                    'error' => $e->getMessage(),
                ],
            ]);
            abort(404, 'Superviseur introuvable.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la mise à jour du profil superviseur
            Log::create([
                'user_id' => $user->id,
                'action' => 'superviseur_profile_update_critical_error',
                'log_message' => "Erreur critique inattendue lors de la mise à jour du profil du superviseur ID: {$id} par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'target_user_id' => $id,
                    'input' => $request->all(),
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour de votre profil : ' . $e->getMessage());
        }
    }
}