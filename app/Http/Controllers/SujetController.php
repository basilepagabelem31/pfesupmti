<?php

namespace App\Http\Controllers;

use App\Models\Sujet;
use App\Models\Promotion; // Pour les listes déroulantes
use App\Models\Groupe;    // Pour les listes déroulantes
use App\Models\User;     // Pour la contrainte de suppression (stagiaires)
use App\Models\Role;     // Pour la contrainte de suppression (stagiaires)
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException; // Pour attraper les erreurs de validation

class SujetController extends Controller
{
    /**
     * Affiche la liste de tous les sujets.
     */
    public function index()
    {
        // Récupère tous les sujets avec leurs promotions et groupes associés (eager loading)
        $sujets = Sujet::with(['promotion', 'groupe'])->get();

        // Récupère toutes les promotions et groupes pour les listes déroulantes dans les modals
        $promotions = Promotion::all();
        $groupes = Groupe::all();

        return view('sujets.index', compact('sujets', 'promotions', 'groupes'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau sujet.
     * (Non utilisé directement si le formulaire est dans un modal sur la page index)
     */
    public function create()
    {
        // Peut être utilisé pour retourner les données pour les listes déroulantes via AJAX si nécessaire
        $promotions = Promotion::all();
        $groupes = Groupe::all();
        return view('sujets.create', compact('promotions', 'groupes'));
    }

    /**
     * Stocke un nouveau sujet dans la base de données.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'titre' => 'required|string|max:255|unique:sujets,titre',
                'description' => 'nullable|string',
                'promotion_id' => 'required|exists:promotions,id', // Assure que la promotion existe
                'groupe_id' => 'required|exists:groupes,id',       // Assure que le groupe existe
            ], [
                'titre.required' => 'Le titre du sujet est obligatoire.',
                'titre.unique' => 'Ce titre de sujet existe déjà.',
                'promotion_id.required' => 'La promotion est obligatoire.',
                'promotion_id.exists' => 'La promotion sélectionnée n\'existe pas.',
                'groupe_id.required' => 'Le groupe est obligatoire.',
                'groupe_id.exists' => 'Le groupe sélectionné n\'existe pas.',
            ]);

            Sujet::create($validatedData);

            return redirect()->route('sujets.index')->with('success', 'Sujet ajouté avec succès!');

        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors())->with('error', 'Veuillez corriger les erreurs dans le formulaire.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Une erreur est survenue lors de l\'ajout du sujet : ' . $e->getMessage());
        }
    }

    /**
     * Affiche le formulaire de modification du sujet spécifié.
     * (Non utilisé directement si le formulaire est dans un modal sur la page index)
     */
    public function edit(Sujet $sujet)
    {
        // Peut être utilisé pour retourner les données pour les listes déroulantes via AJAX si nécessaire
        $promotions = Promotion::all();
        $groupes = Groupe::all();
        return view('sujets.edit', compact('sujet', 'promotions', 'groupes'));
    }

    /**
     * Met à jour le sujet spécifié dans la base de données.
     */
    public function update(Request $request, Sujet $sujet)
    {
        try {
            $validatedData = $request->validate([
                'titre' => 'required|string|max:255|unique:sujets,titre,' . $sujet->id, // Unique sauf pour le sujet actuel
                'description' => 'nullable|string',
                'promotion_id' => 'required|exists:promotions,id',
                'groupe_id' => 'required|exists:groupes,id',
            ], [
                'titre.required' => 'Le titre du sujet est obligatoire.',
                'titre.unique' => 'Ce titre de sujet existe déjà.',
                'promotion_id.required' => 'La promotion est obligatoire.',
                'promotion_id.exists' => 'La promotion sélectionnée n\'existe pas.',
                'groupe_id.required' => 'Le groupe est obligatoire.',
                'groupe_id.exists' => 'Le groupe sélectionné n\'existe pas.',
            ]);

            $sujet->update($validatedData);

            return redirect()->route('sujets.index')->with('success', 'Sujet mis à jour avec succès!');

        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors())->with('error', 'Veuillez corriger les erreurs dans le formulaire.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour du sujet : ' . $e->getMessage());
        }
    }

    /**
     * Supprime le sujet spécifié de la base de données.
     */
    public function destroy(Sujet $sujet)
    {
        // Vérifier si des stagiaires sont inscrits à ce sujet
        if ($sujet->stagiaires()->exists()) {
            return redirect()->route('sujets.index')->with('error', 'Impossible de supprimer ce sujet car des stagiaires y sont inscrits.');
        }

        try {
            $sujet->delete();
            return redirect()->route('sujets.index')->with('success', 'Sujet supprimé avec succès!');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la suppression du sujet : ' . $e->getMessage());
        }
    }
}