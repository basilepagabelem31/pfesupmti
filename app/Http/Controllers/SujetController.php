<?php

namespace App\Http\Controllers;

use App\Models\Groupe;
use App\Models\Promotion;
use App\Models\Sujet;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; 
use App\helper\LogHelper; 
class SujetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sujets = Sujet::with(['promotion', 'groupe', 'stagiaires'])->orderByDesc('created_at')->get();
        $promotions = Promotion::where('status', 'active')->get();
        $groupes = Groupe::all();
        $stagiaires = User::whereHas('role', function ($query) {
            $query->where('nom', 'Stagiaire');
        })->get();

        return view('sujets.index', compact('sujets', 'promotions', 'groupes', 'stagiaires'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Pas de log pour l'affichage de formulaire
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:225',
            'description' => 'required|string',
            'promotion_id' => 'required|exists:promotions,id',
            'groupe_id' => 'required|exists:groupes,id'
        ]);

        $promotion = Promotion::find($validated['promotion_id']);
        if ($promotion->status !== 'active') {
            $user = Auth::user();
            LogHelper::logAction(
                'Échec création sujet',
                'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a tenté de créer un sujet ("' . $validated['titre'] . '") mais la promotion associée ("' . $promotion->titre . '", ID: ' . $promotion->id . ') n\'est pas active.',
                $user->id
            );
            return redirect()->route('sujets.index')->with('error', 'Impossible d\'associer à une promotion archivée.');
        }

        $sujet = Sujet::create($validated);

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA CREATION
        $user = Auth::user();
        LogHelper::logAction(
            'Création de sujet',
            'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a créé le sujet "' . $sujet->titre . '" (ID: ' . $sujet->id . ') pour la promotion "' . $sujet->promotion->titre . '" et le groupe "' . $sujet->groupe->nom . '".',
            $user->id
        );

        return redirect()->route('sujets.index')->with('success', 'Sujet ajouté avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Pas de log pour l'affichage
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Pas de log pour l'affichage de formulaire
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sujet $sujet)
    {
        $user = Auth::user(); // Récupérer l'utilisateur avant la validation pour les logs d'erreur de validation
        $oldSujetData = $sujet->toArray(); // Capture les données avant la mise à jour

        $validated = $request->validate([
            'titre' => 'required|string|max:225',
            'description' => 'required|string',
            'promotion_id' => 'required|exists:promotions,id',
            'groupe_id' => 'required|exists:groupes,id'
        ]);

        $promotion = Promotion::find($validated['promotion_id']);
        if ($promotion->status !== 'active') {
            LogHelper::logAction(
                'Échec modification sujet',
                'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a tenté de modifier le sujet "' . $sujet->titre . '" (ID: ' . $sujet->id . ') mais la nouvelle promotion associée ("' . $promotion->titre . '", ID: ' . $promotion->id . ') n\'est pas active.',
                $user->id
            );
            return redirect()->route('sujets.index')->with('error', 'Impossible d\'associer à une promotion archivée.');
        }

        $sujet->update($validated);

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA MODIFICATION
        $changes = [];
        foreach ($validated as $key => $value) {
            // Comparer l'ancienne valeur avec la nouvelle
            if (isset($oldSujetData[$key]) && $oldSujetData[$key] != $value) {
                // Pour les clés promotion_id et groupe_id, obtenir le nom réel pour un log plus lisible
                if ($key === 'promotion_id') {
                    $oldPromotion = Promotion::find($oldSujetData[$key])->titre ?? 'Inconnu';
                    $newPromotion = Promotion::find($value)->titre ?? 'Inconnu';
                    $changes[] = 'Promotion: "' . $oldPromotion . '" -> "' . $newPromotion . '"';
                } elseif ($key === 'groupe_id') {
                    $oldGroupe = Groupe::find($oldSujetData[$key])->nom ?? 'Inconnu';
                    $newGroupe = Groupe::find($value)->nom ?? 'Inconnu';
                    $changes[] = 'Groupe: "' . $oldGroupe . '" -> "' . $newGroupe . '"';
                } else {
                    $changes[] = ucfirst(str_replace('_', ' ', $key)) . ": '" . $oldSujetData[$key] . "' -> '" . $value . "'";
                }
            }
        }

        $message = 'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a modifié le sujet "' . $sujet->titre . '" (ID: ' . $sujet->id . '). ';
        if (!empty($changes)) {
            $message .= 'Changements: ' . implode(', ', $changes) . '.';
        } else {
            $message .= 'Aucun changement détecté.';
        }

        LogHelper::logAction(
            'Modification de sujet',
            $message,
            $user->id
        );

        return redirect()->route('sujets.index')->with('success', 'Sujet modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sujet $sujet)
    {
        $user = Auth::user();

        // On va utiliser la relation dans sujet avec User
        if ($sujet->stagiaires()->count() > 0) {
            LogHelper::logAction(
                'Échec suppression sujet',
                'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a tenté de supprimer le sujet "' . $sujet->titre . '" (ID: ' . $sujet->id . ') mais celui-ci contient ' . $sujet->stagiaires()->count() . ' stagiaires.',
                $user->id
            );
            return redirect()->route('sujets.index')->with('error', 'Suppression impossible : des stagiaires sont déjà inscrits à ce sujet.');
        }

        // Capture les informations du sujet avant la suppression
        $deletedSujetTitle = $sujet->titre;
        $deletedSujetId = $sujet->id;
        $deletedSujetPromotion = $sujet->promotion->titre ?? 'Inconnue';
        $deletedSujetGroupe = $sujet->groupe->nom ?? 'Inconnu';

        $sujet->delete();

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA SUPPRESSION
        LogHelper::logAction(
            'Suppression de sujet',
            'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a supprimé le sujet "' . $deletedSujetTitle . '" (ID: ' . $deletedSujetId . ') associé à la promotion "' . $deletedSujetPromotion . '" et au groupe "' . $deletedSujetGroupe . '".',
            $user->id
        );

        return redirect()->route('sujets.index')->with('success', 'Sujet supprimé avec succès.');
    }

    /**
     * Récupère les stagiaires inscrits et disponibles pour un sujet.
     * Utilisé par AJAX pour la modale d'inscription.
     */
    public function getStagiairesForEnrollment(Sujet $sujet)
    {
        // Pas de log pour l'affichage de données AJAX
        $inscribedStagiaires = $sujet->stagiaires->map(function ($stagiaire) {
            return ['id' => $stagiaire->id, 'prenom' => $stagiaire->prenom, 'nom' => $stagiaire->nom];
        });

        $stagiaireRole = Role::where('nom', 'Stagiaire')->first();
        $stagiaireRoleId = $stagiaireRole ? $stagiaireRole->id : null;

        $allStagiaires = collect();
        if ($stagiaireRoleId) {
            $allStagiaires = User::where('role_id', $stagiaireRoleId)->get();
        }

        $availableStagiaires = $allStagiaires->diff($sujet->stagiaires)->map(function ($stagiaire) {
            return ['id' => $stagiaire->id, 'prenom' => $stagiaire->prenom, 'nom' => $stagiaire->nom];
        });

        return response()->json([
            'inscribed' => $inscribedStagiaires,
            'available' => $availableStagiaires,
        ]);
    }

    /**
     * Gère l'inscription d'un stagiaire à un sujet.
     */
    public function inscrire(Request $request, Sujet $sujet)
    {
        $user = Auth::user(); // Récupérer l'utilisateur connecté

        $validator = Validator::make($request->all(), [
            'stagiaire_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            LogHelper::logAction(
                'Échec inscription stagiaire à sujet',
                'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a tenté d\'inscrire un stagiaire au sujet "' . $sujet->titre . '" (ID: ' . $sujet->id . '), mais la validation a échoué. Erreurs: ' . json_encode($validator->errors()),
                $user->id
            );
            return back()->withInput()->withErrors($validator)
                         ->with('error', 'Échec de l\'inscription. Veuillez corriger les erreurs.')
                         ->with('sujet_id_for_modal', $sujet->id)
                         ->with('sujet_titre_for_modal', $sujet->titre);
        }

        $stagiaire = User::find($request->stagiaire_id);

        if ($stagiaire && !$sujet->stagiaires->contains($stagiaire->id)) {
            $sujet->stagiaires()->attach($stagiaire->id);
            // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR L'INSCRIPTION
            LogHelper::logAction(
                'Inscription stagiaire à sujet',
                'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a inscrit le stagiaire "' . $stagiaire->prenom . ' ' . $stagiaire->nom . '" (ID: ' . $stagiaire->id . ') au sujet "' . $sujet->titre . '" (ID: ' . $sujet->id . ').',
                $user->id
            );
            return back()->with('success', 'Stagiaire inscrit avec succès!');
        }

        // Log si le stagiaire est déjà inscrit ou n'existe pas
        if ($stagiaire && $sujet->stagiaires->contains($stagiaire->id)) {
            LogHelper::logAction(
                'Tentative inscription stagiaire (déjà inscrit)',
                'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a tenté d\'inscrire le stagiaire "' . $stagiaire->prenom . ' ' . $stagiaire->nom . '" (ID: ' . $stagiaire->id . ') au sujet "' . $sujet->titre . '" (ID: ' . $sujet->id . '), mais le stagiaire est déjà inscrit.',
                $user->id
            );
        } else {
            LogHelper::logAction(
                'Tentative inscription stagiaire (stagiaire inexistant)',
                'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a tenté d\'inscrire un stagiaire (ID inexistant: ' . $request->stagiaire_id . ') au sujet "' . $sujet->titre . '" (ID: ' . $sujet->id . ').',
                $user->id
            );
        }
        return back()->with('error', 'Le stagiaire est déjà inscrit à ce sujet ou n\'existe pas.');
    }

    /**
     * Gère la désinscription d'un stagiaire d'un sujet.
     */
    public function desinscrire(Sujet $sujet, User $stagiaire)
    {
        $user = Auth::user(); // Récupérer l'utilisateur connecté

        if ($sujet->stagiaires->contains($stagiaire->id)) {
            $sujet->stagiaires()->detach($stagiaire->id);
            // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA DESINSCRIPTION
            LogHelper::logAction(
                'Désinscription stagiaire de sujet',
                'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a désinscrit le stagiaire "' . $stagiaire->prenom . ' ' . $stagiaire->nom . '" (ID: ' . $stagiaire->id . ') du sujet "' . $sujet->titre . '" (ID: ' . $sujet->id . ').',
                $user->id
            );
            return back()->with('success', 'Stagiaire désinscrit avec succès!');
        }

        // Log si le stagiaire n'est pas inscrit à ce sujet
        LogHelper::logAction(
            'Tentative désinscription stagiaire (non inscrit)',
            'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a tenté de désinscrire le stagiaire "' . $stagiaire->prenom . ' ' . $stagiaire->nom . '" (ID: ' . $stagiaire->id . ') du sujet "' . $sujet->titre . '" (ID: ' . $sujet->id . '), mais le stagiaire n\'y est pas inscrit.',
            $user->id
        );
        return back()->with('error', 'Ce stagiaire n\'est pas inscrit à ce sujet.');
    }
}