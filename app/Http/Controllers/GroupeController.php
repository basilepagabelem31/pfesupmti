<?php

namespace App\Http\Controllers;

use App\Models\Groupe;
use App\Models\User; // Assurez-vous d'importer le modèle User pour la contrainte
use App\Models\Log; // Importez le modèle Log
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Pour la génération du code
use Illuminate\Support\Facades\Auth; // Pour récupérer l'utilisateur connecté
use Illuminate\Validation\ValidationException; // Pour les exceptions de validation

class GroupeController extends Controller
{
    public function stagiairesActifs($groupeId)
    {
        try {
            $user = Auth::user(); // Obtenez l'utilisateur connecté

            $groupe = Groupe::findOrFail($groupeId);

            // Cette requête utilise la relation 'stagiaires()' définie dans le modèle Groupe,
            // qui devrait déjà filtrer par rôle 'Stagiaire'.
            // Ensuite, nous filtrons en plus par le statut 'Actif' du stagiaire.
            $stagiaires = $groupe->stagiaires()
                ->whereHas('statut', function($q) {
                    $q->where('nom', 'Actif');
                })
                ->select('id', 'nom', 'prenom') // Sélectionnez uniquement les champs nécessaires pour la réponse JSON
                ->get();

            // Enregistrement du log : Récupération des stagiaires actifs d'un groupe (succès)
            Log::create([
                'user_id' => $user->id,
                'action' => 'fetch_active_group_stagiaires_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a récupéré les stagiaires actifs du groupe '{$groupe->nom}' (ID: {$groupe->id}).",
                'object_snapshot' => [
                    'groupe_id' => $groupe->id,
                    'groupe_nom' => $groupe->nom,
                    'stagiaires_count' => $stagiaires->count(),
                ],
            ]);

            // Retourne les stagiaires au format JSON
            return response()->json($stagiaires);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Enregistrement du log : Groupe non trouvé lors de la récupération des stagiaires
            $user = Auth::user(); // Obtenez l'utilisateur connecté pour le log
            Log::create([
                'user_id' => $user->id,
                'action' => 'fetch_active_group_stagiaires_not_found',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a tenté de récupérer les stagiaires pour un groupe inexistant (ID: {$groupeId}).",
                'object_snapshot' => [
                    'groupe_id_attempted' => $groupeId,
                    'error' => $e->getMessage(),
                ],
            ]);
            return response()->json(['error' => 'Groupe introuvable.'], 404);
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la récupération des stagiaires actifs
            $user = Auth::user(); // Obtenez l'utilisateur connecté pour le log
            Log::create([
                'user_id' => $user->id,
                'action' => 'fetch_active_group_stagiaires_error',
                'log_message' => "Erreur inattendue lors de la récupération des stagiaires actifs pour le groupe ID: {$groupeId} par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'groupe_id_attempted' => $groupeId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ],
            ]);
            return response()->json(['error' => 'Erreur interne du serveur lors du chargement des stagiaires.'], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $groupes = Groupe::all();

        // Enregistrement du log : Consultation de la liste des groupes
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_group_list',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a consulté la liste des groupes.",
            'object_snapshot' => [
                'user_role' => $user->role->nom,
                'group_count' => $groupes->count(),
            ],
        ]);

        return view('groupes.index', compact('groupes'));
    }

    /**
     * Show the form for creating a new resource.
     * (Non utilisé directement car le formulaire est dans un modal sur index)
     */
    public function create()
    {
        // Enregistrement du log : Accès au formulaire de création de groupe (même si modal)
        $user = Auth::user();
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_group_create_form',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a accédé au formulaire de création de groupe.",
            'object_snapshot' => ['user_role' => $user->role->nom],
        ]);

        return view('groupes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté

        try {
            $validatedData = $request->validate([
                'nom' => 'required|string|max:255|unique:groupes,nom', // Ajout unique validation for 'nom'
                'description' => 'nullable|string',
            ], [
                'nom.required' => 'Le nom du groupe est obligatoire.',
                'nom.unique' => 'Ce nom de groupe existe déjà.',
            ]);

            // Génération automatique du code unique (exemple: XXXYYY)
            do {
                $code = Str::upper(Str::random(3)) . Str::upper(Str::random(3));
            } while (Groupe::where('code', $code)->exists());

            $groupe = Groupe::create(array_merge($validatedData, ['code' => $code]));

            // Enregistrement du log : Groupe créé avec succès
            Log::create([
                'user_id' => $user->id,
                'action' => 'group_created',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a créé le groupe '{$groupe->nom}' (ID: {$groupe->id}, Code: {$groupe->code}).",
                'object_snapshot' => $groupe->toArray(),
            ]);

            return redirect()->route('groupes.index')->with('success', 'Groupe ajouté avec succès!');

        } catch (ValidationException $e) {
            // Enregistrement du log : Erreur de validation lors de la création du groupe
            Log::create([
                'user_id' => $user->id,
                'action' => 'group_create_validation_error',
                'log_message' => "Erreur de validation lors de la tentative de création d'un groupe par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'input' => $request->all(),
                    'errors' => $e->errors(),
                ],
            ]);
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Veuillez corriger les erreurs dans le formulaire.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la création du groupe
            Log::create([
                'user_id' => $user->id,
                'action' => 'group_create_critical_error',
                'log_message' => "Erreur critique inattendue lors de la création d'un groupe par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'input' => $request->all(),
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'ajout du groupe : ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     * (Non utilisé directement car le formulaire est dans un modal sur index)
     */
    public function edit(Groupe $groupe)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté

        // Enregistrement du log : Accès au formulaire d'édition de groupe (même si modal)
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_group_edit_form',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a accédé au formulaire d'édition du groupe '{$groupe->nom}' (ID: {$groupe->id}).",
            'object_snapshot' => $groupe->toArray(),
        ]);

        return view('groupes.edit', compact('groupe'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Groupe $groupe)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté

        try {
            // Sauvegarde l'état actuel du groupe AVANT la mise à jour pour le log
            $oldGroupeSnapshot = $groupe->toArray();

            // Tentez la validation
            $validatedData = $request->validate([
                'nom' => 'required|string|max:255|unique:groupes,nom,' . $groupe->id,
                'description' => 'nullable|string',
            ], [
                'nom.required' => 'Le nom du groupe est obligatoire.',
                'nom.unique' => 'Ce nom de groupe existe déjà.',
            ]);

            // Si la validation passe, procédez à la mise à jour
            $groupe->update($validatedData);

            // Enregistrement du log : Groupe mis à jour avec succès
            Log::create([
                'user_id' => $user->id,
                'action' => 'group_updated',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a mis à jour le groupe '{$groupe->nom}' (ID: {$groupe->id}).",
                'object_snapshot' => [
                    'old' => $oldGroupeSnapshot,
                    'new' => $groupe->fresh()->toArray(), // Récupère le nouvel état après la mise à jour
                    'changes' => array_diff_assoc($validatedData, $oldGroupeSnapshot), // Affiche les champs modifiés
                ],
            ]);

            return redirect()->route('groupes.index')->with('success', 'Le groupe a été mis à jour avec succès !');

        } catch (ValidationException $e) {
            // Enregistrement du log : Erreur de validation lors de la mise à jour
            Log::create([
                'user_id' => $user->id,
                'action' => 'group_update_validation_error',
                'log_message' => "Erreur de validation lors de la tentative de mise à jour du groupe '{$groupe->nom}' (ID: {$groupe->id}) par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'groupe_id' => $groupe->id,
                    'input' => $request->all(),
                    'errors' => $e->errors(),
                ],
            ]);
            // Si c'est une erreur de validation, redirigez avec les erreurs spécifiques
            return back()->withInput()->withErrors($e->errors())->with('error', 'Veuillez corriger les erreurs dans le formulaire.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la mise à jour
            Log::create([
                'user_id' => $user->id,
                'action' => 'group_update_critical_error',
                'log_message' => "Erreur critique inattendue lors de la mise à jour du groupe '{$groupe->nom}' (ID: {$groupe->id}) par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'groupe_id' => $groupe->id,
                    'input' => $request->all(),
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            // Pour toutes les autres erreurs (DB, etc.)
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour du groupe : ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Groupe $groupe)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté

        // Sauvegarde l'état du groupe AVANT la suppression pour le log
        $deletedGroupeSnapshot = $groupe->toArray();

        // Vérifier si le groupe contient des stagiaires
        if ($groupe->stagiaires()->exists()) {
            // Enregistrement du log : Tentative de suppression d'un groupe avec stagiaires
            Log::create([
                'user_id' => $user->id,
                'action' => 'group_delete_failed_has_stagiaires',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a tenté de supprimer le groupe '{$groupe->nom}' (ID: {$groupe->id}), mais il contient des stagiaires.",
                'object_snapshot' => $deletedGroupeSnapshot,
            ]);
            return redirect()->route('groupes.index')->with('error', 'Impossible de supprimer ce groupe car il contient des stagiaires.');
        }

        try {
            $groupe->delete();

            // Enregistrement du log : Groupe supprimé avec succès
            Log::create([
                'user_id' => $user->id,
                'action' => 'group_deleted',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a supprimé le groupe '{$deletedGroupeSnapshot['nom']}' (ID: {$deletedGroupeSnapshot['id']}, Code: {$deletedGroupeSnapshot['code']}).",
                'object_snapshot' => $deletedGroupeSnapshot, // L'état complet du groupe avant suppression
            ]);

            return redirect()->route('groupes.index')->with('success', 'Groupe supprimé avec succès!');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur critique lors de la suppression du groupe
            Log::create([
                'user_id' => $user->id,
                'action' => 'group_delete_critical_error',
                'log_message' => "Erreur critique inattendue lors de la suppression du groupe '{$deletedGroupeSnapshot['nom']}' (ID: {$deletedGroupeSnapshot['id']}) par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'groupe_id' => $deletedGroupeSnapshot['id'],
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la suppression du groupe : ' . $e->getMessage());
        }
    }
}