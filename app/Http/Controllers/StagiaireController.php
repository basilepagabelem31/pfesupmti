<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Note;
use App\Models\Fichier; // Assurez-vous que Fichier est importé
use App\Models\Pays;    // Assurez-vous que Pays est importé
use App\Models\Ville;   // Assurez-vous que Ville est importé
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Log; // Importez votre modèle Log
use Illuminate\Validation\ValidationException; // Pour capturer les exceptions de validation

class StagiaireController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Obtenez l'utilisateur (stagiaire) connecté

        // Enregistrement du log : Accès au tableau de bord du stagiaire
        Log::create([
            'user_id' => $user->id,
            'action' => 'stagiaire_dashboard_view',
            'log_message' => "Le stagiaire '{$user->nom} {$user->prenom}' (ID: {$user->id}) a accédé à son tableau de bord.",
            'object_snapshot' => [
                'stagiaire_id' => $user->id,
            ],
        ]);

        return view('stagiaires.dashboard');
    }

    public function profiles()
    {
        $user = Auth::user()->load('notes', 'fichiersPossedes'); // Charger les relations pour le log
        
        // Enregistrement du log : Consultation du profil du stagiaire
        Log::create([
            'user_id' => $user->id,
            'action' => 'stagiaire_profile_view',
            'log_message' => "Le stagiaire '{$user->nom} {$user->prenom}' (ID: {$user->id}) a consulté son profil.",
            'object_snapshot' => [
                'stagiaire_id' => $user->id,
                'email' => $user->email,
            ],
        ]);

        return view('stagiaires.profiles', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user(); // L'utilisateur qui tente la modification
        $targetUser = null; // L'utilisateur dont le profil est modifié
        $oldUserData = null; // Pour capturer l'état avant la mise à jour

        try {
            $targetUser = User::findOrFail($id);

            // Vérifier que le stagiaire ne peut modifier que son propre profil
            if ($user->id !== $targetUser->id) {
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'stagiaire_profile_update_unauthorized',
                    'log_message' => "Tentative non autorisée de modification du profil de l'utilisateur ID: {$targetUser->id} par le stagiaire ID: {$user->id}.",
                    'object_snapshot' => [
                        'attempted_user_id' => $user->id,
                        'target_user_id' => $targetUser->id,
                        'request_data' => $request->all(),
                    ],
                ]);
                abort(403, 'Vous n\'êtes pas autorisé à modifier ce profil.');
            }

            $oldUserData = $targetUser->toArray(); // Capture l'état avant la validation et la mise à jour

            $validatedData = $request->validate([
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

            // Si un nouveau mot de passe est fourni, le hacher et le mettre à jour
            if ($request->filled('new_password')) {
                $targetUser->password = Hash::make($request->new_password);
                session()->flash('password_changed', true); // Add session flash for password change success
                // Log spécifique pour le changement de mot de passe
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'stagiaire_password_changed',
                    'log_message' => "Le stagiaire '{$user->nom} {$user->prenom}' (ID: {$user->id}) a changé son mot de passe.",
                    'object_snapshot' => [
                        'stagiaire_id' => $user->id,
                    ],
                ]);
            }

            // Mettre à jour tous les autres champs
            $targetUser->fill($validatedData);

            // Si l'email a été modifié, réinitialiser email_verified_at
            if ($targetUser->isDirty('email')) {
                $targetUser->email_verified_at = null;
                // Log spécifique si l'email change
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'stagiaire_email_changed',
                    'log_message' => "Le stagiaire '{$user->nom} {$user->prenom}' (ID: {$user->id}) a changé son adresse e-mail de '{$oldUserData['email']}' à '{$targetUser->email}'. La vérification de l'e-mail a été réinitialisée.",
                    'object_snapshot' => [
                        'stagiaire_id' => $user->id,
                        'old_email' => $oldUserData['email'],
                        'new_email' => $targetUser->email,
                    ],
                ]);
            }

            // Sauvegarder toutes les modifications
            $targetUser->save();

            // Enregistrement du log : Mise à jour du profil stagiaire réussie
            Log::create([
                'user_id' => $user->id,
                'action' => 'stagiaire_profile_updated_success',
                'log_message' => "Le profil du stagiaire '{$targetUser->nom} {$targetUser->prenom}' (ID: {$targetUser->id}) a été mis à jour avec succès par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'stagiaire_id' => $targetUser->id,
                    'old_data' => $oldUserData,
                    'new_data' => $targetUser->toArray(),
                ],
            ]);

            return redirect()->back()->with('success', 'Profil mis à jour avec succès !');

        } catch (ValidationException $e) {
            // Enregistrement du log : Échec de validation lors de la mise à jour du profil stagiaire
            Log::create([
                'user_id' => $user->id,
                'action' => 'stagiaire_profile_update_validation_failed',
                'log_message' => "Échec de validation lors de la tentative de mise à jour du profil du stagiaire ID: {$id} par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'target_user_id' => $id,
                    'input' => $request->all(),
                    'errors' => $e->errors(),
                ],
            ]);
            return back()->withInput()->withErrors($e->errors())->with('error', 'Veuillez corriger les erreurs dans le formulaire.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Enregistrement du log : Stagiaire non trouvé pour mise à jour
            Log::create([
                'user_id' => $user->id,
                'action' => 'stagiaire_profile_update_not_found',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}) a tenté de modifier un profil de stagiaire inexistant (ID: {$id}).",
                'object_snapshot' => [
                    'target_user_id_attempted' => $id,
                    'error' => $e->getMessage(),
                ],
            ]);
            abort(404, 'Stagiaire introuvable.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la mise à jour du profil stagiaire
            Log::create([
                'user_id' => $user->id,
                'action' => 'stagiaire_profile_update_critical_error',
                'log_message' => "Erreur critique inattendue lors de la mise à jour du profil du stagiaire ID: {$id} par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
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