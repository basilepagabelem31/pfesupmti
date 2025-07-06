<?php

namespace App\Http\Controllers;

use App\Models\Groupe;
use App\Models\Promotion;
use App\Models\Sujet;
use App\Models\User;
use App\Models\Role; // Importez le modèle Role
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // Importez la façade Validator
use Illuminate\Support\Facades\Auth; // Importez Auth
use App\Models\Log; // Importez votre modèle Log
use Illuminate\Validation\ValidationException; // Pour capturer les exceptions de validation

class SujetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté

        $sujets = Sujet::with(['promotion', 'groupe', 'stagiaires'])->orderByDesc('created_at')->get();
        $promotions = Promotion::where('status', 'active')->get();
        $groupes = Groupe::all();
        $stagiaires = User::whereHas('role', function ($query) {
            $query->where('nom', 'Stagiaire'); // Assurez-vous que 'nom' est la bonne colonne
        })->get();

        // Enregistrement du log : Consultation de la liste des sujets
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_sujets_list',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a consulté la liste des sujets. Nombre de sujets: {$sujets->count()}.",
            'object_snapshot' => [
                'sujets_count' => $sujets->count(),
            ],
        ]);

        return view('sujets.index', compact('sujets', 'promotions', 'groupes', 'stagiaires'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté

        // Enregistrement du log : Accès au formulaire de création de sujet
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_sujet_create_form',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a accédé au formulaire de création d'un nouveau sujet.",
            'object_snapshot' => [],
        ]);
        // Si vous avez un formulaire de création distinct, retournez-le ici
        // return view('sujets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $newSujet = null; // Pour la portée du try/catch

        try {
            $validated = $request->validate([
                'titre' => 'required|string|max:225',
                'description' => 'required|string',
                'promotion_id' => 'required|exists:promotions,id',
                'groupe_id' => 'required|exists:groupes,id'
            ]);

            $promotion = Promotion::find($validated['promotion_id']);
            if (!$promotion || $promotion->status !== 'active') {
                // Enregistrement du log : Tentative de création avec promotion archivée/inexistante
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'sujet_create_promotion_inactive',
                    'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a tenté de créer un sujet avec une promotion inactive ou inexistante (ID: {$validated['promotion_id']}).",
                    'object_snapshot' => [
                        'input' => $request->all(),
                        'promotion_status' => $promotion ? $promotion->status : 'not_found',
                    ],
                ]);
                return redirect()->route('sujets.index')->with('error', 'Impossible d\'associer à une promotion archivée ou introuvable.');
            }

            $newSujet = Sujet::create($validated);

            // Enregistrement du log : Création de sujet réussie
            Log::create([
                'user_id' => $user->id,
                'action' => 'sujet_created_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a créé le sujet '{$newSujet->titre}' (ID: {$newSujet->id}) pour la promotion ID: {$newSujet->promotion_id} et le groupe ID: {$newSujet->groupe_id}.",
                'object_snapshot' => $newSujet->toArray(),
            ]);

            return redirect()->route('sujets.index')->with('success', 'Sujet ajouté avec succès.');

        } catch (ValidationException $e) {
            // Enregistrement du log : Échec de validation lors de la création de sujet
            Log::create([
                'user_id' => $user->id,
                'action' => 'sujet_create_validation_failed',
                'log_message' => "Échec de validation lors de la tentative de création d'un sujet par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'input' => $request->all(),
                    'errors' => $e->errors(),
                ],
            ]);
            return back()->withInput()->withErrors($e->errors())->with('error', 'Veuillez corriger les erreurs dans le formulaire.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la création de sujet
            Log::create([
                'user_id' => $user->id,
                'action' => 'sujet_create_critical_error',
                'log_message' => "Erreur critique inattendue lors de la création d'un sujet par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'input' => $request->all(),
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création du sujet : ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté

        try {
            $sujet = Sujet::with(['promotion', 'groupe', 'stagiaires'])->findOrFail($id);

            // Enregistrement du log : Consultation des détails d'un sujet
            Log::create([
                'user_id' => $user->id,
                'action' => 'view_sujet_details',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a consulté les détails du sujet '{$sujet->titre}' (ID: {$sujet->id}).",
                'object_snapshot' => $sujet->toArray(),
            ]);

            return view('sujets.show', compact('sujet')); // Assurez-vous que cette vue existe
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::create([
                'user_id' => $user->id,
                'action' => 'sujet_show_not_found',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a tenté de consulter un sujet inexistant (ID: {$id}).",
                'object_snapshot' => [
                    'sujet_id_attempted' => $id,
                    'error_message' => $e->getMessage(),
                ],
            ]);
            abort(404, 'Sujet introuvable.');
        } catch (\Exception $e) {
            Log::create([
                'user_id' => $user->id,
                'action' => 'sujet_show_critical_error',
                'log_message' => "Erreur critique lors de la consultation du sujet ID: {$id} par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'sujet_id_attempted' => $id,
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            abort(500, 'Erreur interne du serveur lors de la consultation du sujet.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté

        try {
            $sujet = Sujet::with(['promotion', 'groupe'])->findOrFail($id); // Eager load pour l'édition si besoin

            // Enregistrement du log : Accès au formulaire d'édition de sujet
            Log::create([
                'user_id' => $user->id,
                'action' => 'view_sujet_edit_form',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a accédé au formulaire d'édition du sujet '{$sujet->titre}' (ID: {$sujet->id}).",
                'object_snapshot' => $sujet->toArray(),
            ]);

            return view('sujets.edit', compact('sujet')); // Assurez-vous que cette vue existe
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::create([
                'user_id' => $user->id,
                'action' => 'sujet_edit_not_found',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a tenté d'éditer un sujet inexistant (ID: {$id}).",
                'object_snapshot' => [
                    'sujet_id_attempted' => $id,
                    'error_message' => $e->getMessage(),
                ],
            ]);
            abort(404, 'Sujet introuvable pour édition.');
        } catch (\Exception $e) {
            Log::create([
                'user_id' => $user->id,
                'action' => 'sujet_edit_critical_error',
                'log_message' => "Erreur critique lors de l'accès au formulaire d'édition du sujet ID: {$id} par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'sujet_id_attempted' => $id,
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            abort(500, 'Erreur interne du serveur lors de l\'édition du sujet.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sujet $sujet)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $oldSujetData = $sujet->toArray(); // Capture l'état avant la mise à jour

        try {
            $validated = $request->validate([
                'titre' => 'required|string|max:225',
                'description' => 'required|string',
                'promotion_id' => 'required|exists:promotions,id',
                'groupe_id' => 'required|exists:groupes,id'
            ]);

            $promotion = Promotion::find($validated['promotion_id']);
            if (!$promotion || $promotion->status !== 'active') {
                // Enregistrement du log : Tentative de mise à jour avec promotion archivée/inexistante
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'sujet_update_promotion_inactive',
                    'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a tenté de modifier le sujet '{$sujet->titre}' (ID: {$sujet->id}) avec une promotion inactive ou inexistante (ID: {$validated['promotion_id']}).",
                    'object_snapshot' => [
                        'sujet_id' => $sujet->id,
                        'input' => $request->all(),
                        'promotion_status' => $promotion ? $promotion->status : 'not_found',
                    ],
                ]);
                return redirect()->route('sujets.index')->with('error', 'Impossible d\'associer à une promotion archivée ou introuvable.');
            }

            $sujet->update($validated);

            // Enregistrement du log : Mise à jour de sujet réussie
            Log::create([
                'user_id' => $user->id,
                'action' => 'sujet_updated_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a modifié le sujet '{$sujet->titre}' (ID: {$sujet->id}).",
                'object_snapshot' => [
                    'sujet_id' => $sujet->id,
                    'old_data' => $oldSujetData,
                    'new_data' => $sujet->toArray(),
                ],
            ]);

            return redirect()->route('sujets.index')->with('success', 'Sujet modifié avec succès.');

        } catch (ValidationException $e) {
            // Enregistrement du log : Échec de validation lors de la mise à jour de sujet
            Log::create([
                'user_id' => $user->id,
                'action' => 'sujet_update_validation_failed',
                'log_message' => "Échec de validation lors de la tentative de mise à jour du sujet '{$sujet->titre}' (ID: {$sujet->id}) par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'sujet_id' => $sujet->id,
                    'input' => $request->all(),
                    'errors' => $e->errors(),
                ],
            ]);
            return back()->withInput()->withErrors($e->errors())->with('error', 'Veuillez corriger les erreurs dans le formulaire.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la mise à jour de sujet
            Log::create([
                'user_id' => $user->id,
                'action' => 'sujet_update_critical_error',
                'log_message' => "Erreur critique inattendue lors de la mise à jour du sujet '{$sujet->titre}' (ID: {$sujet->id}) par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'sujet_id' => $sujet->id,
                    'input' => $request->all(),
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la modification du sujet : ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sujet $sujet)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $sujetData = $sujet->toArray(); // Capture les données avant suppression

        try {
            // Vérifier si des stagiaires sont inscrits
            if ($sujet->stagiaires()->count() > 0) {
                // Enregistrement du log : Tentative de suppression de sujet avec stagiaires inscrits
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'sujet_delete_failed_stagiaires_enrolled',
                    'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a tenté de supprimer le sujet '{$sujetData['titre']}' (ID: {$sujetData['id']}) mais des stagiaires y sont inscrits.",
                    'object_snapshot' => $sujetData,
                ]);
                return redirect()->route('sujets.index')->with('error', 'Suppression impossible : des stagiaires sont déjà inscrits à ce sujet.');
            }

            $sujet->delete();

            // Enregistrement du log : Suppression de sujet réussie
            Log::create([
                'user_id' => $user->id,
                'action' => 'sujet_deleted_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a supprimé le sujet '{$sujetData['titre']}' (ID: {$sujetData['id']}).",
                'object_snapshot' => $sujetData,
            ]);

            return redirect()->route('sujets.index')->with('success', 'Sujet supprimé avec succès.');

        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la suppression de sujet
            Log::create([
                'user_id' => $user->id,
                'action' => 'sujet_delete_critical_error',
                'log_message' => "Erreur critique inattendue lors de la suppression du sujet '{$sujetData['titre']}' (ID: {$sujetData['id']}) par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'sujet_id' => $sujetData['id'],
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->with('error', 'Une erreur est survenue lors de la suppression du sujet : ' . $e->getMessage());
        }
    }

    /**
     * Récupère les stagiaires inscrits et disponibles pour un sujet.
     * Utilisé par AJAX pour la modale d'inscription.
     */
    public function getStagiairesForEnrollment(Sujet $sujet)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté

        try {
            // Stagiaires déjà inscrits à ce sujet
            $inscribedStagiaires = $sujet->stagiaires->map(function ($stagiaire) {
                return ['id' => $stagiaire->id, 'prenom' => $stagiaire->prenom, 'nom' => $stagiaire->nom];
            });

            // Récupérer l'ID du rôle 'Stagiaire'.
            $stagiaireRole = Role::where('nom', 'Stagiaire')->first();
            $stagiaireRoleId = $stagiaireRole ? $stagiaireRole->id : null;

            // Tous les utilisateurs qui sont des stagiaires
            $allStagiaires = collect();
            if ($stagiaireRoleId) {
                $allStagiaires = User::where('role_id', $stagiaireRoleId)->get();
            }

            // Stagiaires disponibles (ceux qui ne sont pas déjà inscrits au sujet)
            $availableStagiaires = $allStagiaires->diff($sujet->stagiaires)->map(function ($stagiaire) {
                return ['id' => $stagiaire->id, 'prenom' => $stagiaire->prenom, 'nom' => $stagiaire->nom];
            });

            // Enregistrement du log : Récupération des stagiaires pour l'inscription (AJAX)
            Log::create([
                'user_id' => $user->id,
                'action' => 'sujet_get_stagiaires_for_enrollment_ajax',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a récupéré la liste des stagiaires pour l'inscription au sujet '{$sujet->titre}' (ID: {$sujet->id}).",
                'object_snapshot' => [
                    'sujet_id' => $sujet->id,
                    'inscribed_count' => $inscribedStagiaires->count(),
                    'available_count' => $availableStagiaires->count(),
                ],
            ]);

            return response()->json([
                'inscribed' => $inscribedStagiaires,
                'available' => $availableStagiaires,
            ]);

        } catch (\Exception $e) {
            // Enregistrement du log : Erreur lors de la récupération des stagiaires pour l'inscription
            Log::create([
                'user_id' => $user->id,
                'action' => 'sujet_get_stagiaires_for_enrollment_critical_error',
                'log_message' => "Erreur critique lors de la récupération des stagiaires pour l'inscription au sujet ID: {$sujet->id} par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'sujet_id' => $sujet->id,
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return response()->json(['error' => 'Une erreur est survenue lors de la récupération des stagiaires.'], 500);
        }
    }

    /**
     * Gère l'inscription d'un stagiaire à un sujet.
     */
    public function inscrire(Request $request, Sujet $sujet)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $stagiaireId = $request->input('stagiaire_id');
        $stagiaire = null; // Pour la portée du try/catch

        try {
            $validator = Validator::make($request->all(), [
                'stagiaire_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                // Enregistrement du log : Échec de validation lors de l'inscription
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'sujet_enroll_validation_failed',
                    'log_message' => "Échec de validation lors de la tentative d'inscription du stagiaire ID: {$stagiaireId} au sujet '{$sujet->titre}' (ID: {$sujet->id}) par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                    'object_snapshot' => [
                        'sujet_id' => $sujet->id,
                        'stagiaire_id_attempted' => $stagiaireId,
                        'errors' => $validator->errors()->toArray(),
                    ],
                ]);
                return back()->withInput()->withErrors($validator)
                            ->with('error', 'Échec de l\'inscription. Veuillez corriger les erreurs.')
                            ->with('sujet_id_for_modal', $sujet->id)
                            ->with('sujet_titre_for_modal', $sujet->titre);
            }

            $stagiaire = User::find($stagiaireId);

            if ($stagiaire && !$sujet->stagiaires->contains($stagiaire->id)) {
                $sujet->stagiaires()->attach($stagiaire->id);
                // Enregistrement du log : Inscription réussie
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'sujet_enroll_success',
                    'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a inscrit le stagiaire '{$stagiaire->nom} {$stagiaire->prenom}' (ID: {$stagiaire->id}) au sujet '{$sujet->titre}' (ID: {$sujet->id}).",
                    'object_snapshot' => [
                        'sujet_id' => $sujet->id,
                        'stagiaire_id' => $stagiaire->id,
                    ],
                ]);
                return back()->with('success', 'Stagiaire inscrit avec succès!');
            }

            // Enregistrement du log : Stagiaire déjà inscrit ou introuvable
            Log::create([
                'user_id' => $user->id,
                'action' => 'sujet_enroll_failed_already_enrolled_or_not_found',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a tenté d'inscrire le stagiaire ID: {$stagiaireId} au sujet '{$sujet->titre}' (ID: {$sujet->id}), mais le stagiaire est déjà inscrit ou introuvable.",
                'object_snapshot' => [
                    'sujet_id' => $sujet->id,
                    'stagiaire_id_attempted' => $stagiaireId,
                    'is_already_enrolled' => $stagiaire ? $sujet->stagiaires->contains($stagiaire->id) : false,
                    'stagiaire_exists' => (bool)$stagiaire,
                ],
            ]);
            return back()->with('error', 'Le stagiaire est déjà inscrit à ce sujet ou n\'existe pas.');

        } catch (\Exception $e) {
            // Enregistrement du log : Erreur critique lors de l'inscription
            Log::create([
                'user_id' => $user->id,
                'action' => 'sujet_enroll_critical_error',
                'log_message' => "Erreur critique inattendue lors de la tentative d'inscription du stagiaire ID: {$stagiaireId} au sujet '{$sujet->titre}' (ID: {$sujet->id}) par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'sujet_id' => $sujet->id,
                    'stagiaire_id_attempted' => $stagiaireId,
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->with('error', 'Une erreur est survenue lors de l\'inscription : ' . $e->getMessage());
        }
    }

    /**
     * Gère la désinscription d'un stagiaire d'un sujet.
     */
    public function desinscrire(Sujet $sujet, User $stagiaire)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté

        try {
            if ($sujet->stagiaires->contains($stagiaire->id)) {
                $sujet->stagiaires()->detach($stagiaire->id);
                // Enregistrement du log : Désinscription réussie
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'sujet_unenroll_success',
                    'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a désinscrit le stagiaire '{$stagiaire->nom} {$stagiaire->prenom}' (ID: {$stagiaire->id}) du sujet '{$sujet->titre}' (ID: {$sujet->id}).",
                    'object_snapshot' => [
                        'sujet_id' => $sujet->id,
                        'stagiaire_id' => $stagiaire->id,
                    ],
                ]);
                return back()->with('success', 'Stagiaire désinscrit avec succès!');
            }

            // Enregistrement du log : Stagiaire non inscrit au sujet
            Log::create([
                'user_id' => $user->id,
                'action' => 'sujet_unenroll_failed_not_enrolled',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a tenté de désinscrire le stagiaire '{$stagiaire->nom} {$stagiaire->prenom}' (ID: {$stagiaire->id}) du sujet '{$sujet->titre}' (ID: {$sujet->id}), mais ce stagiaire n'y est pas inscrit.",
                'object_snapshot' => [
                    'sujet_id' => $sujet->id,
                    'stagiaire_id' => $stagiaire->id,
                ],
            ]);
            return back()->with('error', 'Ce stagiaire n\'est pas inscrit à ce sujet.');

        } catch (\Exception $e) {
            // Enregistrement du log : Erreur critique lors de la désinscription
            Log::create([
                'user_id' => $user->id,
                'action' => 'sujet_unenroll_critical_error',
                'log_message' => "Erreur critique inattendue lors de la désinscription du stagiaire '{$stagiaire->nom} {$stagiaire->prenom}' (ID: {$stagiaire->id}) du sujet '{$sujet->titre}' (ID: {$sujet->id}) par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'sujet_id' => $sujet->id,
                    'stagiaire_id' => $stagiaire->id,
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->with('error', 'Une erreur est survenue lors de la désinscription : ' . $e->getMessage());
        }
    }
}