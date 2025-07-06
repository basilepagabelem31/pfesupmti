<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use App\Models\Log; // Importez le modèle Log
use App\Notifications\NoteAdded;
use App\Notifications\NoteUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException; // Pour gérer les erreurs de validation

class NoteController extends Controller
{
    // Liste des stagiaires avec résumé des notes
    public function listeStagiaires(Request $request)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté

        $query = User::whereHas('role', function ($q) {
            $q->where('nom', 'Stagiaire');
        })->withCount('notes');

        // Filtre nom/prénom si présent dans la requête
        if ($request->filled('nom')) {
            $nom = $request->input('nom');
            $query->where(function ($q) use ($nom) {
                $q->where('nom', 'like', "%{$nom}%")
                    ->orWhere('prenom', 'like', "%{$nom}%");
            });
        }

        $stagiaires = $query->get();

        // Enregistrement du log : Consultation de la liste des stagiaires (pour les notes)
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_stagiaires_list_for_notes',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a consulté la liste des stagiaires pour la gestion des notes.",
            'object_snapshot' => [
                'filtered_by_name' => $request->filled('nom') ? $request->input('nom') : null,
                'stagiaires_count' => $stagiaires->count(),
            ],
        ]);

        return view('notes.liste_stagiaire', compact('stagiaires'));
    }

    // Affiche les notes d'un stagiaire
    public function ficheStagiaire($id)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        
        try {
            $stagiaire = User::findOrFail($id);

            // Vérifie si l'utilisateur est le stagiaire concerné ou un de ses coéquipiers
            $isStagiaire = $user->id == $stagiaire->id;
            // On s'assure que getAllCoequipiers() retourne une collection avec un pluck('id') pour le contains
            $isCoequipier = $stagiaire->getAllCoequipiers()->pluck('id')->contains($user->id);

            // Laisser l'accès aux admins/superviseurs
            $isAdmin = $user->isAdministrateur();
            $isSuperviseur = $user->isSuperviseur();

            if (!($isStagiaire || $isCoequipier || $isAdmin || $isSuperviseur)) {
                // Enregistrement du log : Accès non autorisé à la fiche d'un stagiaire
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'unauthorized_access_stagiaire_notes_fiche',
                    'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a tenté d'accéder sans autorisation à la fiche de notes du stagiaire '{$stagiaire->nom} {$stagiaire->prenom}' (ID: {$stagiaire->id}).",
                    'object_snapshot' => [
                        'stagiaire_id' => $stagiaire->id,
                        'user_role' => $user->role->nom,
                    ],
                ]);
                abort(403, 'Accès interdit');
            }

            $notes = Note::where('stagiaire_id', $stagiaire->id)->orderByDesc('date_note')->get();

            // Enregistrement du log : Consultation de la fiche de notes d'un stagiaire
            $accessType = '';
            if ($isStagiaire) $accessType = 'self_access';
            elseif ($isCoequipier) $accessType = 'teammate_access';
            elseif ($isAdmin) $accessType = 'admin_access';
            elseif ($isSuperviseur) $accessType = 'superviseur_access';

            Log::create([
                'user_id' => $user->id,
                'action' => 'view_stagiaire_notes_fiche',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a consulté la fiche de notes du stagiaire '{$stagiaire->nom} {$stagiaire->prenom}' (ID: {$stagiaire->id}). Type d'accès: {$accessType}.",
                'object_snapshot' => [
                    'stagiaire_id' => $stagiaire->id,
                    'stagiaire_nom' => $stagiaire->nom,
                    'notes_count' => $notes->count(),
                    'access_type' => $accessType,
                ],
            ]);

            return view('notes.fiche_stagiaire', compact('stagiaire', 'notes'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Enregistrement du log : Stagiaire non trouvé pour la fiche de notes
            Log::create([
                'user_id' => $user->id,
                'action' => 'stagiaire_not_found_notes_fiche',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a tenté d'accéder à la fiche de notes d'un stagiaire inexistant (ID: {$id}).",
                'object_snapshot' => [
                    'stagiaire_id_attempted' => $id,
                    'error' => $e->getMessage(),
                ],
            ]);
            abort(404, 'Stagiaire introuvable.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de l'affichage de la fiche de notes
            Log::create([
                'user_id' => $user->id,
                'action' => 'view_stagiaire_notes_fiche_error',
                'log_message' => "Erreur inattendue lors de l'affichage de la fiche de notes pour le stagiaire ID: {$id} par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'stagiaire_id_attempted' => $id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ],
            ]);
            abort(500, 'Erreur interne du serveur.');
        }
    }

    // Ajout d'une note (avec propagation si demandé)
    public function store(Request $request)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $stagiaireCible = null; // Sera défini après validation
        $coequipiersCount = 0;

        try {
            $validatedData = $request->validate([
                'valeur' => 'required|string',
                'visibilite' => 'required|in:all,donneur,donneur + stagiaire,superviseurs- stagiaire',
                'stagiaire_id' => 'required|exists:users,id',
                'propager' => 'nullable|boolean',
            ]);

            $stagiaireCible = User::find($validatedData['stagiaire_id']);

            // Note principale
            $note = Note::create([
                'valeur' => $validatedData['valeur'],
                'visibilite' => $validatedData['visibilite'],
                'date_note' => now(),
                'stagiaire_id' => $validatedData['stagiaire_id'],
                'donneur_id' => $user->id,
            ]);

            // Notification principale
            $note->stagiaire->notify(new NoteAdded($note));

            // Propagation aux coéquipiers si demandé
            if (isset($validatedData['propager']) && $validatedData['propager']) {
                foreach ($stagiaireCible->getAllCoequipiers() as $coequipier) {
                    $copie = Note::create([
                        'valeur' => $validatedData['valeur'],
                        'visibilite' => $validatedData['visibilite'],
                        'date_note' => now(),
                        'stagiaire_id' => $coequipier->id,
                        'donneur_id' => $user->id,
                    ]);
                    $coequipier->notify(new NoteAdded($copie));
                    $coequipiersCount++;
                }
            }

            // Enregistrement du log : Note ajoutée avec succès
            Log::create([
                'user_id' => $user->id,
                'action' => 'note_added_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a ajouté une note au stagiaire '{$stagiaireCible->nom} {$stagiaireCible->prenom}' (ID: {$stagiaireCible->id}). Propagée à {$coequipiersCount} coéquipier(s).",
                'object_snapshot' => array_merge($note->toArray(), [
                    'target_stagiaire_nom' => $stagiaireCible->nom,
                    'target_stagiaire_prenom' => $stagiaireCible->prenom,
                    'propagated_to_count' => $coequipiersCount,
                ]),
            ]);

            return back()->with('success', 'Note ajoutée avec succès !');

        } catch (ValidationException $e) {
            // Enregistrement du log : Erreur de validation lors de l'ajout de note
            Log::create([
                'user_id' => $user->id,
                'action' => 'note_add_validation_error',
                'log_message' => "Erreur de validation lors de la tentative d'ajout d'une note par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'input' => $request->all(),
                    'errors' => $e->errors(),
                ],
            ]);
            return back()->withInput()->withErrors($e->errors())->with('error', 'Veuillez corriger les erreurs dans le formulaire.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de l'ajout de note
            Log::create([
                'user_id' => $user->id,
                'action' => 'note_add_critical_error',
                'log_message' => "Erreur critique inattendue lors de l'ajout d'une note par '{$user->nom} {$user->prenom}' ({$user->role->nom}) pour le stagiaire ID: " . ($stagiaireCible->id ?? $request->stagiaire_id) . ".",
                'object_snapshot' => [
                    'input' => $request->all(),
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->withInput()->with('error', 'Une erreur est survenue lors de l\'ajout de la note : ' . $e->getMessage());
        }
    }

    // Edition d'une note
    public function edit(string $id)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté

        try {
            $note = Note::findOrFail($id);

            // Seul le donneur de la note peut modifier
            if ($user->id !== $note->donneur_id) {
                // Enregistrement du log : Tentative d'édition de note non autorisée
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'note_edit_unauthorized_access',
                    'log_message' => "Tentative d'accès non autorisée au formulaire d'édition de la note (ID: {$note->id}) par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                    'object_snapshot' => $note->toArray(),
                ]);
                abort(403);
            }

            // Enregistrement du log : Accès au formulaire d'édition de note
            Log::create([
                'user_id' => $user->id,
                'action' => 'view_note_edit_form',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a accédé au formulaire d'édition de la note (ID: {$note->id}) du stagiaire '{$note->stagiaire->nom} {$note->stagiaire->prenom}'.",
                'object_snapshot' => $note->toArray(),
            ]);

            return view('notes.edit', compact('note'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Enregistrement du log : Note non trouvée pour l'édition
            Log::create([
                'user_id' => $user->id,
                'action' => 'note_edit_not_found',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a tenté d'éditer une note inexistante (ID: {$id}).",
                'object_snapshot' => [
                    'note_id_attempted' => $id,
                    'error' => $e->getMessage(),
                ],
            ]);
            abort(404, 'Note introuvable.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de l'affichage du formulaire d'édition
            Log::create([
                'user_id' => $user->id,
                'action' => 'view_note_edit_form_error',
                'log_message' => "Erreur inattendue lors de l'affichage du formulaire d'édition de la note ID: {$id} par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'note_id_attempted' => $id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ],
            ]);
            abort(500, 'Erreur interne du serveur.');
        }
    }

    // Mise à jour d'une note
    public function update(Request $request, string $id)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $note = null; // Déclarer ici pour la portée du try/catch

        try {
            $note = Note::findOrFail($id);

            // Seul le donneur de la note peut modifier
            if ($user->id !== $note->donneur_id) {
                // Enregistrement du log : Tentative de mise à jour de note non autorisée
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'note_update_unauthorized_access',
                    'log_message' => "Tentative de mise à jour non autorisée de la note (ID: {$note->id}) par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                    'object_snapshot' => $note->toArray(),
                ]);
                abort(403);
            }

            $oldNoteSnapshot = $note->toArray(); // Capture l'état avant mise à jour

            $validatedData = $request->validate([
                'valeur' => 'required|string',
                'visibilite' => 'required|in:all,donneur,donneur + stagiaire,superviseurs- stagiaire',
            ]);

            // On recherche toutes les notes équivalentes (stagiaire + coéquipiers)
            // en se basant sur l'état initial de la note pour trouver toutes les copies
            Note::where('donneur_id', $oldNoteSnapshot['donneur_id'])
                ->where('date_note', $oldNoteSnapshot['date_note'])
                ->where('valeur', $oldNoteSnapshot['valeur']) // Ancienne valeur pour trouver les correspondances
                ->where('visibilite', $oldNoteSnapshot['visibilite']) // Ancienne visibilité
                ->update([
                    'valeur' => $validatedData['valeur'],
                    'visibilite' => $validatedData['visibilite'],
                ]);

            // Récupère toutes les notes concernées après update (pour notification)
            $updatedNotes = Note::where('donneur_id', $oldNoteSnapshot['donneur_id'])
                ->where('date_note', $oldNoteSnapshot['date_note'])
                ->where('valeur', $validatedData['valeur']) // Nouvelle valeur pour récupérer les notes mises à jour
                ->where('visibilite', $validatedData['visibilite']) // Nouvelle visibilité
                ->get();

            // Envoie une notification à chaque stagiaire concerné
            foreach ($updatedNotes as $n) {
                $n->stagiaire->notify(new NoteUpdated($n));
            }

            // Enregistrement du log : Note mise à jour avec succès
            Log::create([
                'user_id' => $user->id,
                'action' => 'note_updated_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a mis à jour la note (ID: {$note->id}) du stagiaire '{$note->stagiaire->nom} {$note->stagiaire->prenom}' et de ses coéquipiers ({$updatedNotes->count()} notes affectées).",
                'object_snapshot' => [
                    'note_id' => $note->id,
                    'old_valeur' => $oldNoteSnapshot['valeur'],
                    'new_valeur' => $validatedData['valeur'],
                    'old_visibilite' => $oldNoteSnapshot['visibilite'],
                    'new_visibilite' => $validatedData['visibilite'],
                    'notes_affected_count' => $updatedNotes->count(),
                ],
            ]);

            return redirect()->route('notes.fiche_stagiaire', $note->stagiaire_id)->with('success', 'Note modifiée chez le stagiaire et ses coéquipiers !');

        } catch (ValidationException $e) {
            // Enregistrement du log : Erreur de validation lors de la mise à jour de note
            Log::create([
                'user_id' => $user->id,
                'action' => 'note_update_validation_error',
                'log_message' => "Erreur de validation lors de la tentative de mise à jour de la note ID: {$id} par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'note_id_attempted' => $id,
                    'input' => $request->all(),
                    'errors' => $e->errors(),
                ],
            ]);
            return back()->withInput()->withErrors($e->errors())->with('error', 'Veuillez corriger les erreurs dans le formulaire.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Enregistrement du log : Note non trouvée pour la mise à jour
            Log::create([
                'user_id' => $user->id,
                'action' => 'note_update_not_found',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a tenté de mettre à jour une note inexistante (ID: {$id}).",
                'object_snapshot' => [
                    'note_id_attempted' => $id,
                    'error' => $e->getMessage(),
                ],
            ]);
            abort(404, 'Note introuvable.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la mise à jour de note
            Log::create([
                'user_id' => $user->id,
                'action' => 'note_update_critical_error',
                'log_message' => "Erreur critique inattendue lors de la mise à jour de la note ID: {$id} par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'note_id_attempted' => $id,
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour de la note : ' . $e->getMessage());
        }
    }

    // Suppression d'une note
    public function destroy(string $id)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $note = null; // Déclarer ici pour la portée du try/catch
        $deletedNotesCount = 0;

        try {
            $note = Note::findOrFail($id);

            // Seul le donneur de la note peut supprimer
            if ($user->id !== $note->donneur_id) {
                // Enregistrement du log : Tentative de suppression de note non autorisée
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'note_delete_unauthorized_access',
                    'log_message' => "Tentative de suppression non autorisée de la note (ID: {$note->id}) par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                    'object_snapshot' => $note->toArray(),
                ]);
                abort(403);
            }

            // Récupérer l'état initial de la note pour le log avant la suppression
            $deletedNoteSnapshot = $note->toArray();
            $targetStagiaireId = $note->stagiaire_id;
            $targetStagiaireName = $note->stagiaire->nom . ' ' . $note->stagiaire->prenom;


            // Récupérer tous les id des stagiaires concernés (stagiaire + coéquipiers)
            $stagiairePrincipal = $note->stagiaire;
            $coequipiers = $stagiairePrincipal->getAllCoequipiers();
            $idsToAffect = $coequipiers->pluck('id')->toArray();
            $idsToAffect[] = $stagiairePrincipal->id;

            // Supprime toutes les notes du groupe même si valeur/visibilité ont changé
            $deletedNotesCount = Note::where('donneur_id', $note->donneur_id)
                ->where('date_note', $note->date_note)
                ->whereIn('stagiaire_id', $idsToAffect) // Cible les notes du stagiaire principal et de ses coéquipiers
                ->delete();

            // Enregistrement du log : Note supprimée avec succès
            Log::create([
                'user_id' => $user->id,
                'action' => 'note_deleted_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a supprimé la note (ID initiale: {$deletedNoteSnapshot['id']}) du stagiaire '{$targetStagiaireName}' et de ses coéquipiers ({$deletedNotesCount} notes supprimées).",
                'object_snapshot' => [
                    'original_note_snapshot' => $deletedNoteSnapshot,
                    'notes_deleted_count' => $deletedNotesCount,
                ],
            ]);

            return redirect()->route('notes.fiche_stagiaire', $targetStagiaireId)
                ->with('success', 'Note supprimée avec succès chez le stagiaire et ses coéquipiers !');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Enregistrement du log : Note non trouvée pour la suppression
            Log::create([
                'user_id' => $user->id,
                'action' => 'note_delete_not_found',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a tenté de supprimer une note inexistante (ID: {$id}).",
                'object_snapshot' => [
                    'note_id_attempted' => $id,
                    'error' => $e->getMessage(),
                ],
            ]);
            abort(404, 'Note introuvable.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur critique lors de la suppression de note
            Log::create([
                'user_id' => $user->id,
                'action' => 'note_delete_critical_error',
                'log_message' => "Erreur critique inattendue lors de la suppression de la note ID: {$id} par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'note_id_attempted' => $id,
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la suppression de la note : ' . $e->getMessage());
        }
    }
}