<?php

namespace App\Http\Controllers;

use App\Models\Groupe;
use App\Models\Promotion;
use App\Models\Sujet;
use App\Models\User;
use App\Models\Role; // Importez le modèle Role
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // Importez la façade Validator

class SujetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sujets = Sujet::with(['promotion', 'groupe', 'stagiaires'])->orderByDesc('created_at')->get(); // Ajout de 'stagiaires' pour eager loading
        $promotions = Promotion::where('status', 'active')->get();
        $groupes = Groupe::all();
        // Pour les stagiaires, vous pouvez récupérer ceux qui ont le rôle 'Stagiaire'
        // Cette partie est pour l'affichage initial de la page si vous en avez besoin pour d'autres fonctionnalités
        $stagiaires = User::whereHas('role', function ($query) {
            $query->where('nom', 'Stagiaire'); // Utilisez le nom de la colonne de votre rôle si c'est 'nom_role'
        })->get();

        return view('sujets.index', compact('sujets', 'promotions', 'groupes', 'stagiaires'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
        // Vérifie si la promotion est bien active
        $promotion = Promotion::find($validated['promotion_id']);
        if ($promotion->status !== 'active') {
            return redirect()->route('sujets.index')->with('error', 'Impossible d\'associer à une promotion archivée.');
        }

        Sujet::create($validated);
        return redirect()->route('sujets.index')->with('success', 'Sujet ajouté avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sujet $sujet)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:225',
            'description' => 'required|string',
            'promotion_id' => 'required|exists:promotions,id',
            'groupe_id' => 'required|exists:groupes,id'
        ]);

        $promotion = Promotion::find($validated['promotion_id']);
        if ($promotion->status !== 'active') {
            return redirect()->route('sujets.index')->with('error', 'Impossible d\'associer à une promotion archivée.');
        }
        $sujet->update($validated);
        return redirect()->route('sujets.index')->with('success', 'Sujet modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sujet $sujet)
    {
        // On va utiliser la relation dans sujet avec User
        if ($sujet->stagiaires()->count() > 0) {
            return redirect()->route('sujets.index')->with('error', 'Suppression impossible : des stagiaires sont déjà inscrits à ce sujet.');
        }
        $sujet->delete();
        return redirect()->route('sujets.index')->with('success', 'Sujet supprimé avec succès.');
    }

    /**
     * Récupère les stagiaires inscrits et disponibles pour un sujet.
     * Utilisé par AJAX pour la modale d'inscription.
     */
    public function getStagiairesForEnrollment(Sujet $sujet)
    {
        // Stagiaires déjà inscrits à ce sujet
        $inscribedStagiaires = $sujet->stagiaires->map(function ($stagiaire) {
            return ['id' => $stagiaire->id, 'prenom' => $stagiaire->prenom, 'nom' => $stagiaire->nom];
        });

        // Récupérer l'ID du rôle 'Stagiaire'.
        // Assurez-vous que votre table 'roles' et le modèle 'Role' existent
        // et que la colonne du nom de rôle est 'nom_role' ou 'name'.
        $stagiaireRole = Role::where('nom', 'Stagiaire')->first(); // Utilisez 'nom_role' ou 'name' selon votre BD
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
        $validator = Validator::make($request->all(), [
            'stagiaire_id' => 'required|exists:users,id', // Assurez-vous que 'users' est la bonne table pour les stagiaires
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator)
                         ->with('error', 'Échec de l\'inscription. Veuillez corriger les erreurs.')
                         ->with('sujet_id_for_modal', $sujet->id) // Important pour rouvrir la modale
                         ->with('sujet_titre_for_modal', $sujet->titre); // Important pour pré-remplir le titre
        }

        $stagiaire = User::find($request->stagiaire_id);

        if ($stagiaire && !$sujet->stagiaires->contains($stagiaire->id)) {
            $sujet->stagiaires()->attach($stagiaire->id);
            return back()->with('success', 'Stagiaire inscrit avec succès!');
        }

        return back()->with('error', 'Le stagiaire est déjà inscrit à ce sujet ou n\'existe pas.');
    }

    /**
     * Gère la désinscription d'un stagiaire d'un sujet.
     */
    public function desinscrire(Sujet $sujet, User $stagiaire) // Utilisez le type-hinting pour $stagiaire
    {
        if ($sujet->stagiaires->contains($stagiaire->id)) {
            $sujet->stagiaires()->detach($stagiaire->id);
            return back()->with('success', 'Stagiaire désinscrit avec succès!');
        }

        return back()->with('error', 'Ce stagiaire n\'est pas inscrit à ce sujet.');
    }
}
