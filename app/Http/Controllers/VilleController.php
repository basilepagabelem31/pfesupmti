<?php

namespace App\Http\Controllers;

use App\Models\Ville;
use App\Models\Pays;
use Illuminate\Http\Request;

class VilleController extends Controller
{



    public function getVilles($pays_id)
    
    {
    $villes = Ville::where('pays_id', $pays_id)->get();

    return response()->json($villes);
    }

    // Afficher toutes les villes
    public function index()
    {
        $villes = Ville::with('pays')->get();
        return view('villes.index', compact('villes'));
    }

    // Afficher le formulaire d'ajout d'une ville
    public function create()
    {
        $pays = Pays::all();
        return view('villes.create', compact('pays'));
    }

    // Enregistrer une nouvelle ville
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'pays_id' => 'required|exists:pays,id',
            'codeville' => 'nullable|string|max:10',
        ]);

        $code = $request->code ?? strtoupper(substr($request->nom, 0, 3));

        Ville::create([
            'code' => $code,
            'nom' => $request->nom,
            'pays_id' => $request->pays_id,
        ]);

        return redirect()->route('villes.index')->with('success', 'Ville ajoutée avec succès!');
    }

    // Afficher le formulaire de modification
    public function edit($id)
    {
        $ville = Ville::findOrFail($id);
        $pays = Pays::all();
        return view('villes.edit', compact('ville', 'pays'));
    }

    // Mettre à jour une ville
    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'pays_id' => 'required|exists:pays,id',
            'code' => 'nullable|string|max:10',
        ]);

        $ville = Ville::findOrFail($id);

        $code = $request->code ?? strtoupper(substr($request->nom, 0, 3));

        $ville->update([
            'code' => $code,
            'nom' => $request->nom,
            'pays_id' => $request->pays_id,
        ]);

        return redirect()->route('villes.index')->with('success', 'Ville mise à jour avec succès!');
    }

    // Supprimer une ville
    public function destroy($id)
    {
        $ville = Ville::findOrFail($id);
        $ville->delete();

        return redirect()->route('villes.index')->with('success', 'Ville supprimée avec succès!');
    }
}
