<?php

namespace App\Http\Controllers;

use App\Models\Groupe;
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Validation\ValidationException;
use App\helper\LogHelper; 

class GroupeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groupes = Groupe::all();
        return view('groupes.index', compact('groupes'));
    }

    /**
     * Show the form for creating a new resource.
     * (Non utilisé directement car le formulaire est dans un modal sur index)
     */
    public function create()
    {
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
            'jour' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
        ]);

        do {
            $code = Str::upper(Str::random(3)) . Str::upper(Str::random(3));
        } while (Groupe::where('code', $code)->exists());

        $groupe = Groupe::create(array_merge($request->all(), ['code' => $code]));

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA CREATION
        $user = Auth::user();
        LogHelper::logAction(
            'Création de groupe',
            'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a créé le groupe "' . $groupe->nom . '" (ID: ' . $groupe->id . ', Code: ' . $groupe->code . ').',
            $user->id
        );

        return redirect()->route('groupes.index')->with('success', 'Groupe ajouté avec succès!');
    }

    /**
     * Show the form for editing the specified resource.
     * (Non utilisé directement car le formulaire est dans un modal sur index)
     */
    public function edit(Groupe $groupe)
    {
        return view('groupes.edit', compact('groupe'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Groupe $groupe)
    {
        try {
            // Sauvegarder l'état actuel du groupe pour comparer les changements
            $oldGroupeData = $groupe->toArray();

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

            $groupe->update($validatedData);

            // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA MODIFICATION
            $user = Auth::user();
            $changes = [];

            // Compare les champs validés avec les anciennes valeurs
            foreach ($validatedData as $key => $value) {
                if ($oldGroupeData[$key] != $value) {
                    $changes[] = ucfirst(str_replace('_', ' ', $key)) . ": '" . $oldGroupeData[$key] . "' -> '" . $value . "'";
                }
            }

            $message = 'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a modifié le groupe "' . $groupe->nom . '" (ID: ' . $groupe->id . '). ';
            if (!empty($changes)) {
                $message .= 'Changements: ' . implode(', ', $changes) . '.';
            } else {
                $message .= 'Aucun changement détecté.';
            }

            LogHelper::logAction(
                'Modification de groupe',
                $message,
                $user->id
            );

            return redirect()->route('groupes.index')->with('success', 'Le groupe a été mis à jour avec succès !');

        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors())->with('error', 'Veuillez corriger les erreurs dans le formulaire.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour du groupe : ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Groupe $groupe)
    {
        // Vérifier si le groupe contient des stagiaires
        if ($groupe->stagiaires()->exists()) {
            return redirect()->route('groupes.index')->with('error', 'Impossible de supprimer ce groupe car il contient des stagiaires.');
        }

        // Sauvegarder les informations du groupe avant la suppression pour le log
        $deletedGroupeNom = $groupe->nom;
        $deletedGroupeId = $groupe->id;
        $deletedGroupeCode = $groupe->code;

        $groupe->delete();

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA SUPPRESSION
        $user = Auth::user();
        LogHelper::logAction(
            'Suppression de groupe',
            'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a supprimé le groupe "' . $deletedGroupeNom . '" (ID: ' . $deletedGroupeId . ', Code: ' . $deletedGroupeCode . ').',
            $user->id
        );

        return redirect()->route('groupes.index')->with('success', 'Groupe supprimé avec succès!');
    }
}