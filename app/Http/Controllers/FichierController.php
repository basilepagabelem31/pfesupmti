<?php

namespace App\Http\Controllers;

use App\Models\Fichier;
use App\Models\User;
use App\Models\Sujet;
use App\Models\Role;
use App\Models\Log; // <-- Importez votre modèle Log
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
// Supprimez l'import de la façade Log si vous n'en avez plus besoin, mais souvent utile pour les erreurs inattendues
use Illuminate\Support\Facades\Log as DefaultLog; // Renommez si vous utilisez aussi la façade pour les erreurs non applicatives
use Illuminate\Validation\ValidationException;

class FichierController extends Controller
{
    /**
     * Affiche la liste des fichiers pour un stagiaire spécifique (pour superviseur/admin)
     * ou les propres fichiers du stagiaire connecté.
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $sujets = Sujet::all();

        $query = Fichier::with('stagiaire', 'televerseur', 'sujet')->latest();

        $currentStagiaire = null;
        $currentTeleverseur = null;
        $currentSearchQuery = $request->input('search_query', '');

        $stagiairesFilterList = collect();
        $televerseursFilterList = collect();

        // Enregistrement du log : Accès à la liste des fichiers
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_files_list',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a accédé à la liste des fichiers.",
            'object_snapshot' => [
                'user_role' => $user->role->nom,
                'search_query' => $currentSearchQuery,
                'filter_stagiaire_id' => $request->input('stagiaire_id'),
                'filter_televerseur_id' => $request->input('televerseur'),
            ],
        ]);

        if ($user->isStagiaire()) {
            $query->where('id_stagiaire', $user->id);
            $currentStagiaire = $user;

            $televerseursFilterList = User::whereHas('fichiersTeleverses', function($q) use ($user) {
                $q->where('id_stagiaire', $user->id);
            })->orWhere('id', $user->id)->get();

            if (!empty($currentSearchQuery)) {
                $query->whereHas('televerseur', function ($subQuery) use ($currentSearchQuery) {
                    $subQuery->where('prenom', 'like', '%' . $currentSearchQuery . '%')
                             ->orWhere('nom', 'like', '%' . $currentSearchQuery . '%');
                });
            }

            $fichiers = $query->get();

            return view('fichiers.index_stagiaire', compact('fichiers', 'sujets', 'currentStagiaire', 'televerseursFilterList', 'currentSearchQuery'));

        } elseif ($user->isSuperviseur() || $user->isAdministrateur()) {
            $stagiairesFilterList = User::whereHas('role', function ($q) {
                $q->where('nom', 'Stagiaire');
            })->orderBy('prenom')->orderBy('nom')->get();

            $televerseursFilterList = User::whereHas('fichiersTeleverses')
                                         ->orderBy('prenom')->orderBy('nom')->get();

            if (!empty($currentSearchQuery)) {
                $query->where(function ($q) use ($currentSearchQuery) {
                    $q->whereHas('stagiaire', function ($subQuery) use ($currentSearchQuery) {
                        $subQuery->where('prenom', 'like', '%' . $currentSearchQuery . '%')
                                 ->orWhere('nom', 'like', '%' . $currentSearchQuery . '%');
                    })->orWhereHas('televerseur', function ($subQuery) use ($currentSearchQuery) {
                        $subQuery->where('prenom', 'like', '%' . $currentSearchQuery . '%')
                                 ->orWhere('nom', 'like', '%' . $currentSearchQuery . '%');
                    });
                });
            }

            if ($request->filled('stagiaire_id')) {
                $stagiaireId = $request->input('stagiaire_id');
                $stagiaireFiltered = User::find($stagiaireId);
                if ($stagiaireFiltered && $stagiaireFiltered->isStagiaire()) {
                    $query->where('id_stagiaire', $stagiaireFiltered->id);
                    $currentStagiaire = $stagiaireFiltered;
                }
            }

            if ($request->filled('televerseur')) {
                $televerseurId = $request->input('televerseur');
                $televerseurFiltered = User::find($televerseurId);
                if ($televerseurFiltered) {
                    $query->where('id_superviseur_televerseur', $televerseurFiltered->id);
                    $currentTeleverseur = $televerseurFiltered;
                }
            }

            $fichiers = $query->get();

            return view('fichiers.index_superviseur', compact('fichiers', 'stagiairesFilterList', 'currentStagiaire', 'sujets', 'televerseursFilterList', 'currentTeleverseur', 'currentSearchQuery'));
        }

        // Enregistrement du log : Accès non autorisé à la liste des fichiers
        Log::create([
            'user_id' => $user->id,
            'action' => 'unauthorized_file_list_access',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a tenté d'accéder sans autorisation à la liste des fichiers.",
            'object_snapshot' => ['user_role' => $user->role->nom],
        ]);
        abort(403, 'Accès non autorisé.');
    }

    /**
     * Affiche le formulaire de téléversement de fichier.
     * Peut être pour un stagiaire (il téléverse pour lui-même) ou un superviseur (il téléverse pour un stagiaire).
     * @param User|null $stagiaire
     * @return \Illuminate\View\View
     */
    public function create(User $stagiaire = null)
    {
        $user = Auth::user();
        $stagiaires = null;
        $sujets = Sujet::all();

        // Enregistrement du log : Accès au formulaire de création de fichier
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_file_create_form',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a accédé au formulaire de création de fichier.",
            'object_snapshot' => [
                'user_role' => $user->role->nom,
                'target_stagiaire_id' => $stagiaire ? $stagiaire->id : 'N/A'
            ],
        ]);

        if ($user->isSuperviseur() || $user->isAdministrateur()) {
            $stagiaires = User::whereHas('role', function ($query) {
                $query->where('nom', 'Stagiaire');
            })->get();
        } elseif ($user->isStagiaire()) {
            $stagiaire = $user;
        } else {
            // Enregistrement du log : Accès non autorisé au formulaire de création de fichier
            Log::create([
                'user_id' => $user->id,
                'action' => 'unauthorized_file_create_form_access',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a tenté d'accéder sans autorisation au formulaire de création de fichier.",
                'object_snapshot' => ['user_role' => $user->role->nom],
            ]);
            abort(403, 'Accès non autorisé.');
        }

        return view('fichiers.create', compact('stagiaire', 'stagiaires', 'sujets'));
    }

    /**
     * Gère le téléversement et l'enregistrement d'un nouveau fichier.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $stagiaireRoleId = Role::where('nom', 'Stagiaire')->value('id');

        try {
            $rules = [
                'nom_fichier' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'fichier' => 'required|file|max:10240', // Max 10MB (10240 KB)
                'type_fichier' => 'required|string|in:convention,rapport,attestation,autre',
                'sujet_id' => 'nullable|exists:sujets,id',
            ];

            if ($user->isSuperviseur() || $user->isAdministrateur()) {
                $rules['id_stagiaire'] = 'required|exists:users,id';
            }

            $validatedData = $request->validate($rules);

            $idStagiaireProprietaire = $user->isStagiaire() ? $user->id : $validatedData['id_stagiaire'];

            $path = $request->file('fichier')->store('public/fichiers');
            $urlFichier = Storage::url($path);

            $fichier = Fichier::create([
                'nom_fichier' => $validatedData['nom_fichier'],
                'description' => $validatedData['description'],
                'url_fichier' => $urlFichier,
                'id_stagiaire' => $idStagiaireProprietaire,
                'id_superviseur_televerseur' => $user->id,
                'peut_modifier' => $user->isStagiaire() ? true : $request->boolean('peut_modifier', false),
                'peut_supprimer' => $user->isStagiaire() ? true : $request->boolean('peut_supprimer', false),
                'type_fichier' => $validatedData['type_fichier'],
                'sujet_id' => $validatedData['sujet_id'],
            ]);

            // Enregistrement du log : Fichier téléversé avec succès
            Log::create([
                'user_id' => $user->id,
                'action' => 'file_uploaded',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a téléversé le fichier '{$fichier->nom_fichier}' (ID: {$fichier->id}) pour le stagiaire ID: {$idStagiaireProprietaire}.",
                'object_snapshot' => $fichier->toArray(),
            ]);

            return redirect()->route('fichiers.index')->with('success', 'Fichier téléversé avec succès !');

        } catch (ValidationException $e) {
            // Enregistrement du log : Erreur de validation lors du téléversement
            Log::create([
                'user_id' => $user->id,
                'action' => 'file_upload_validation_error',
                'log_message' => "Erreur de validation lors du téléversement d'un fichier par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'input' => $request->all(),
                    'errors' => $e->errors(),
                ],
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors du téléversement
            Log::create([
                'user_id' => $user->id,
                'action' => 'file_upload_critical_error',
                'log_message' => "Erreur critique inattendue lors du téléversement d'un fichier par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'input' => $request->all(),
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            // Utilisez la façade Log de Laravel pour les erreurs système critiques si vous voulez les séparer
            DefaultLog::critical('Erreur inattendue lors du téléversement de fichier', [
                'user_id' => $user->id,
                'user_role' => $user->role->nom,
                'input' => $request->all(),
                'exception_message' => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Une erreur inattendue est survenue lors du téléversement.');
        }
    }

    /**
     * Affiche le formulaire d'édition d'un fichier.
     * @param Fichier $fichier
     * @return \Illuminate\View\View
     */
    public function edit(Fichier $fichier)
    {
        $user = Auth::user();

        if ($user->isStagiaire() && ($fichier->id_stagiaire !== $user->id || !$fichier->peut_modifier)) {
            // Enregistrement du log : Tentative non autorisée d'accès au formulaire d'édition
            Log::create([
                'user_id' => $user->id,
                'action' => 'unauthorized_file_edit_form_access',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a tenté d'accéder sans autorisation au formulaire d'édition du fichier '{$fichier->nom_fichier}' (ID: {$fichier->id}).",
                'object_snapshot' => [
                    'fichier_id' => $fichier->id,
                    'fichier_proprietaire_id' => $fichier->id_stagiaire,
                    'peut_modifier_permission' => $fichier->peut_modifier,
                ],
            ]);
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce fichier.');
        } elseif (!$user->isSuperviseur() && !$user->isAdministrateur() && !$user->isStagiaire()) {
            // Enregistrement du log : Accès non autorisé au formulaire d'édition (rôle non défini)
            Log::create([
                'user_id' => $user->id,
                'action' => 'unauthorized_file_edit_form_access_unknown_role',
                'log_message' => "Accès non autorisé au formulaire d'édition du fichier '{$fichier->nom_fichier}' (ID: {$fichier->id}) par un utilisateur avec un rôle non défini.",
                'object_snapshot' => [
                    'user_id' => $user->id,
                    'user_role' => $user->role->nom,
                    'fichier_id' => $fichier->id,
                ],
            ]);
            abort(403, 'Accès non autorisé.');
        }

        // Enregistrement du log : Accès au formulaire d'édition de fichier
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_file_edit_form',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a accédé au formulaire d'édition du fichier '{$fichier->nom_fichier}' (ID: {$fichier->id}).",
            'object_snapshot' => [
                'fichier_id' => $fichier->id,
                'nom_fichier' => $fichier->nom_fichier,
            ],
        ]);

        $sujets = Sujet::all();

        return view('fichiers.edit', compact('fichier', 'sujets'));
    }

    /**
     * Met à jour un fichier existant.
     * @param Request $request
     * @param Fichier $fichier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Fichier $fichier)
    {
        $user = Auth::user();

        if ($user->isStagiaire() && ($fichier->id_stagiaire !== $user->id || !$fichier->peut_modifier)) {
            // Enregistrement du log : Tentative non autorisée de mise à jour de fichier
            Log::create([
                'user_id' => $user->id,
                'action' => 'unauthorized_file_update_attempt',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a tenté de modifier sans autorisation le fichier '{$fichier->nom_fichier}' (ID: {$fichier->id}).",
                'object_snapshot' => [
                    'fichier_id' => $fichier->id,
                    'fichier_proprietaire_id' => $fichier->id_stagiaire,
                    'peut_modifier_permission' => $fichier->peut_modifier,
                ],
            ]);
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce fichier.');
        } elseif (!$user->isSuperviseur() && !$user->isAdministrateur() && !$user->isStagiaire()) {
            // Enregistrement du log : Accès non autorisé à la mise à jour (rôle non défini)
            Log::create([
                'user_id' => $user->id,
                'action' => 'unauthorized_file_update_attempt_unknown_role',
                'log_message' => "Accès non autorisé à la mise à jour du fichier '{$fichier->nom_fichier}' (ID: {$fichier->id}) par un utilisateur avec un rôle non défini.",
                'object_snapshot' => [
                    'user_id' => $user->id,
                    'user_role' => $user->role->nom,
                    'fichier_id' => $fichier->id,
                ],
            ]);
            abort(403, 'Accès non autorisé.');
        }

        $stagiaireRoleId = Role::where('nom', 'Stagiaire')->value('id');

        try {
            $rules = [
                'nom_fichier' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'fichier' => 'nullable|file|max:10240',
                'type_fichier' => 'required|string|in:convention,rapport,attestation,autre',
                'sujet_id' => 'nullable|exists:sujets,id',
            ];

            if ($user->isSuperviseur() || $user->isAdministrateur()) {
                $rules['peut_modifier'] = 'boolean';
                $rules['peut_supprimer'] = 'boolean';
            }

            $validatedData = $request->validate($rules);

            // Sauvegarde l'état actuel de l'utilisateur AVANT la mise à jour pour le log
            $oldFichierSnapshot = $fichier->toArray();

            $dataToUpdate = [
                'nom_fichier' => $validatedData['nom_fichier'],
                'description' => $validatedData['description'],
                'type_fichier' => $validatedData['type_fichier'],
                'sujet_id' => $validatedData['sujet_id'],
            ];

            $oldUrlFichier = $fichier->url_fichier;

            if ($request->hasFile('fichier')) {
                if ($fichier->url_fichier && Storage::exists(str_replace('/storage/', 'public/', $fichier->url_fichier))) {
                    Storage::delete(str_replace('/storage/', 'public/', $fichier->url_fichier));
                    // Log pour la suppression de l'ancien fichier
                    Log::create([
                        'user_id' => $user->id,
                        'action' => 'old_file_deleted_on_update',
                        'log_message' => "Ancien fichier physique (URL: '{$oldUrlFichier}') supprimé du stockage lors de la mise à jour du fichier '{$fichier->nom_fichier}' (ID: {$fichier->id}).",
                        'object_snapshot' => ['fichier_id' => $fichier->id, 'old_url_fichier' => $oldUrlFichier],
                    ]);
                }
                $path = $request->file('fichier')->store('public/fichiers');
                $dataToUpdate['url_fichier'] = Storage::url($path);
                // Log pour le nouveau fichier téléversé
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'new_file_uploaded_on_update',
                    'log_message' => "Nouveau fichier téléversé (chemin: '{$path}') pour la mise à jour du fichier '{$fichier->nom_fichier}' (ID: {$fichier->id}).",
                    'object_snapshot' => ['fichier_id' => $fichier->id, 'new_path' => $path],
                ]);
            }

            if ($user->isSuperviseur() || $user->isAdministrateur()) {
                $dataToUpdate['peut_modifier'] = $request->boolean('peut_modifier', false);
                $dataToUpdate['peut_supprimer'] = $request->boolean('peut_supprimer', false);
            }

            $fichier->update($dataToUpdate);

            // Enregistrement du log : Fichier mis à jour avec succès
            Log::create([
                'user_id' => $user->id,
                'action' => 'file_updated',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a mis à jour le fichier '{$fichier->nom_fichier}' (ID: {$fichier->id}).",
                'object_snapshot' => [
                    'old' => $oldFichierSnapshot,
                    'new' => $fichier->fresh()->toArray(),
                    'changes' => array_diff_assoc($dataToUpdate, $oldFichierSnapshot), // Logique pour les champs modifiés
                ],
            ]);

            return redirect()->route('fichiers.index')->with('success', 'Fichier mis à jour avec succès !');

        } catch (ValidationException $e) {
            // Enregistrement du log : Erreur de validation lors de la mise à jour
            Log::create([
                'user_id' => $user->id,
                'action' => 'file_update_validation_error',
                'log_message' => "Erreur de validation lors de la mise à jour du fichier '{$fichier->nom_fichier}' (ID: {$fichier->id}) par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'fichier_id' => $fichier->id,
                    'input' => $request->all(),
                    'errors' => $e->errors(),
                ],
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la mise à jour
            Log::create([
                'user_id' => $user->id,
                'action' => 'file_update_critical_error',
                'log_message' => "Erreur critique inattendue lors de la mise à jour du fichier '{$fichier->nom_fichier}' (ID: {$fichier->id}) par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'fichier_id' => $fichier->id,
                    'input' => $request->all(),
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            DefaultLog::critical('Erreur inattendue lors de la mise à jour de fichier', [
                'user_id' => $user->id,
                'user_role' => $user->role->nom,
                'fichier_id' => $fichier->id,
                'input' => $request->all(),
                'exception_message' => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour du fichier.');
        }
    }

    /**
     * Supprime un fichier.
     * @param Fichier $fichier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Fichier $fichier)
    {
        $user = Auth::user();

        if ($user->isStagiaire() && ($fichier->id_stagiaire !== $user->id || !$fichier->peut_supprimer)) {
            // Enregistrement du log : Tentative non autorisée de suppression
            Log::create([
                'user_id' => $user->id,
                'action' => 'unauthorized_file_delete_attempt',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a tenté de supprimer sans autorisation le fichier '{$fichier->nom_fichier}' (ID: {$fichier->id}).",
                'object_snapshot' => [
                    'fichier_id' => $fichier->id,
                    'fichier_proprietaire_id' => $fichier->id_stagiaire,
                    'peut_supprimer_permission' => $fichier->peut_supprimer,
                ],
            ]);
            abort(403, 'Vous n\'êtes pas autorisé à supprimer ce fichier.');
        } elseif (!$user->isSuperviseur() && !$user->isAdministrateur() && !$user->isStagiaire()) {
            // Enregistrement du log : Accès non autorisé à la suppression (rôle non défini)
            Log::create([
                'user_id' => $user->id,
                'action' => 'unauthorized_file_delete_attempt_unknown_role',
                'log_message' => "Accès non autorisé à la suppression du fichier '{$fichier->nom_fichier}' (ID: {$fichier->id}) par un utilisateur avec un rôle non défini.",
                'object_snapshot' => [
                    'user_id' => $user->id,
                    'user_role' => $user->role->nom,
                    'fichier_id' => $fichier->id,
                ],
            ]);
            abort(403, 'Accès non autorisé.');
        }

        try {
            $oldUrlFichier = $fichier->url_fichier;
            $fichierId = $fichier->id;
            $nomFichier = $fichier->nom_fichier;
            $fichierProprietaireId = $fichier->id_stagiaire;

            if ($fichier->url_fichier && Storage::exists(str_replace('/storage/', 'public/', $fichier->url_fichier))) {
                Storage::delete(str_replace('/storage/', 'public/', $fichier->url_fichier));
                // Log de suppression du fichier physique
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'file_physical_deleted',
                    'log_message' => "Fichier physique '{$nomFichier}' (ID: {$fichierId}, URL: '{$oldUrlFichier}') supprimé du stockage par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                    'object_snapshot' => ['fichier_id' => $fichierId, 'url_fichier' => $oldUrlFichier],
                ]);
            }

            $fichier->delete();

            // Enregistrement du log : Fichier supprimé avec succès
            Log::create([
                'user_id' => $user->id,
                'action' => 'file_deleted',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a supprimé le fichier '{$nomFichier}' (ID: {$fichierId}) appartenant au stagiaire ID: {$fichierProprietaireId}.",
                'object_snapshot' => [
                    'fichier_id' => $fichierId,
                    'nom_fichier' => $nomFichier,
                    'url_fichier_before_delete' => $oldUrlFichier,
                    'id_stagiaire_proprietaire' => $fichierProprietaireId,
                ],
            ]);

            return redirect()->route('fichiers.index')->with('success', 'Fichier supprimé avec succès !');

        } catch (\Exception $e) {
            // Enregistrement du log : Erreur critique lors de la suppression
            Log::create([
                'user_id' => $user->id,
                'action' => 'file_delete_critical_error',
                'log_message' => "Erreur critique lors de la suppression du fichier '{$fichier->nom_fichier}' (ID: {$fichier->id}) par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'fichier_id' => $fichier->id ?? 'unknown',
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            DefaultLog::critical('Erreur lors de la suppression du fichier', [
                'user_id' => $user->id,
                'user_role' => $user->role->nom,
                'fichier_id' => $fichier->id ?? 'unknown',
                'exception_message' => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la suppression du fichier.');
        }
    }

    /**
     * Permet de télécharger un fichier.
     * @param Fichier $fichier
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(Fichier $fichier)
{
    $user = Auth::user();
    
    // Stocke les infos de l'utilisateur et du fichier pour le log, même en cas d'erreur.
    $logContext = [
        'user_id' => $user->id,
        'user_role' => $user->role->nom,
        'fichier_id' => $fichier->id,
        'nom_fichier' => $fichier->nom_fichier, // <--- Use $fichier->nom_fichier directly here
        'fichier_proprietaire_id' => $fichier->id_stagiaire,
    ];

    if ($user->id !== $fichier->id_stagiaire && !$user->isSuperviseur() && !$user->isAdministrateur()) {
        // Enregistrement du log : Tentative non autorisée de téléchargement
        Log::create([
            'user_id' => $user->id,
            'action' => 'unauthorized_file_download_attempt',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a tenté de télécharger sans autorisation le fichier '{$fichier->nom_fichier}' (ID: {$fichier->id}).",
            'object_snapshot' => $logContext,
        ]);
        abort(403, 'Accès non autorisé au téléchargement de ce fichier.');
    }

    $filePath = str_replace('/storage/', 'public/', $fichier->url_fichier);

    if (Storage::exists($filePath)) {
        $extension = pathinfo(Storage::path($filePath), PATHINFO_EXTENSION);
        $downloadFileName = preg_replace('/\.[^.]+$/', '', $fichier->nom_fichier);
        $downloadFileName .= '.' . $extension;

        // Enregistrement du log : Fichier téléchargé avec succès
        Log::create([
            'user_id' => $user->id,
            'action' => 'file_downloaded',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a téléchargé le fichier '{$fichier->nom_fichier}' (ID: {$fichier->id}, Nom du fichier de téléchargement: '{$downloadFileName}').", // <--- Corrected this line
            'object_snapshot' => array_merge($logContext, ['download_filename' => $downloadFileName]),
        ]);

        return Storage::download($filePath, $downloadFileName);
    }

    // Enregistrement du log : Fichier non trouvé pour le téléchargement
    Log::create([
        'user_id' => $user->id,
        'action' => 'file_download_not_found',
        'log_message' => "Tentative de téléchargement du fichier '{$fichier->nom_fichier}' (ID: {$fichier->id}) échouée : fichier physique non trouvé.",
        'object_snapshot' => array_merge($logContext, [
            'url_fichier_expected' => $fichier->url_fichier,
            'resolved_path' => $filePath,
        ]),
    ]);
    abort(404, 'Fichier non trouvé.');
}
}