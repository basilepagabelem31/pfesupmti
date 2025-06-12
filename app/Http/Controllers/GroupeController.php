<?php

namespace App\Http\Controllers;

use App\Models\Groupe;
use App\Models\User; // Assurez-vous d'importer le modèle User pour la contrainte
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Pour la génération du code

class GroupeController extends Controller
{

    //recuperer les stagiaires lie a un groupe 
     public function getStagiaires($id)
    {
        $groupe = Groupe::findOrFail($id);
        $stagiaires = $groupe->stagiaires;

        return response()->json($stagiaires);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groupes = Groupe::all();
        // Le dashboard est déjà protégé par 'auth' et 'verified'
        // Pour les groupes, nous allons le protéger par 'role:Administrateur,Superviseur' dans les routes
        return view('groupes.index', compact('groupes'));
    }

    /**
     * Show the form for creating a new resource.
     * (Non utilisé directement car le formulaire est dans un modal sur index)
     */
    public function create()
    {
        // En général, cette méthode est utilisée pour afficher un formulaire de création.
        // Puisque nous utilisons un modal, ce n'est pas une route directement appelée pour l'affichage du formulaire complet.
        // On pourrait la garder pour un cas de non-modal ou si le modal charge son contenu via AJAX.
        // Pour cet exemple, le formulaire est intégré via un composant Blade ou directement dans l'index.
        return view('groupes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'jour' => 'required|date', // Validation comme date
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
        ]);

        // Génération automatique du code unique (exemple: XXXYYY)
        do {
            $code = Str::upper(Str::random(3)) . Str::upper(Str::random(3));
        } while (Groupe::where('code', $code)->exists());

        Groupe::create(array_merge($request->all(), ['code' => $code]));

        return redirect()->route('groupes.index')->with('success', 'Groupe ajouté avec succès!');
    }

    /**
     * Show the form for editing the specified resource.
     * (Non utilisé directement car le formulaire est dans un modal sur index)
     */
    public function edit(Groupe $groupe)
    {
        // Comme pour 'create', cette vue sera probablement chargée via AJAX dans le modal.
        return view('groupes.edit', compact('groupe'));
    }

    /**
     * Update the specified resource in storage.
     */
     // app/Http/Controllers/GroupeController.php

// ...

public function update(Request $request, Groupe $groupe)
{
    try {
        // Tentez la validation
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255|unique:groupes,nom,' . $groupe->id,
            'description' => 'nullable|string',
            'jour' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
        ], [
            'nom.required' => 'Le nom du groupe est obligatoire.',
            'nom.unique' => 'Ce nom de groupe existe déjà.',
            'jour.required' => 'Le jour est obligatoire.',
            'jour.date' => 'Le jour doit être une date valide.',
            'heure_debut.required' => 'L\'heure de début est obligatoire.',
            'heure_debut.date_format' => 'Le format de l\'heure de début doit être HH:MM.',
            'heure_fin.required' => 'L\'heure de fin est obligatoire.',
            'heure_fin.date_format' => 'Le format de l\'heure de fin doit être HH:MM.',
            'heure_fin.after' => 'L\'heure de fin doit être postérieure à l\'heure de début.',
        ]);

        // Si la validation passe, procédez à la mise à jour
        $groupe->update($validatedData);

        return redirect()->route('groupes.index')->with('success', 'Le groupe a été mis à jour avec succès !');

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Si c'est une erreur de validation, redirigez avec les erreurs spécifiques
        return back()->withInput()->withErrors($e->errors())->with('error', 'Veuillez corriger les erreurs dans le formulaire.');
    }
    catch (\Exception $e) {
        // Pour toutes les autres erreurs (DB, etc.)
        return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour du groupe : ' . $e->getMessage());
    }
}

// ...

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Groupe $groupe)
    {
        // Vérifier si le groupe contient des stagiaires
        if ($groupe->stagiaires()->exists()) {
            return redirect()->route('groupes.index')->with('error', 'Impossible de supprimer ce groupe car il contient des stagiaires.');
        }

        $groupe->delete();

        return redirect()->route('groupes.index')->with('success', 'Groupe supprimé avec succès!');
    }
}