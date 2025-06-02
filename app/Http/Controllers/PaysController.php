<?php

namespace App\Http\Controllers;

use App\Models\Pays;
use Illuminate\Http\Request;

class PaysController extends Controller
{
    // Afficher le formulaire d'ajout d'un pays
    public function create()
    {
        return view('pays.create');
    }

    // Enregistrer un pays dans la base de données
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:pays,code',
            'nom' => 'required|string|max:255',
        ]);

        Pays::create([
            'code' => $request->code,
            'nom' => $request->nom,
        ]);

        return redirect()->route('pays.index')->with('success', 'Pays ajouté avec succès!');
    }

    public function index(Request $request)
    {
        $pays = Pays::all();
        return view('pays.index', compact('pays'));
    }
    



    public function destroy($id)
{
    $pays = Pays::findOrFail($id);
    $pays->delete();

    return redirect()->route('pays.index')->with('success', 'Pays supprimé avec succès!');
}



// Formulaire pour modifier un pays
public function edit($id)
{
    $pay = Pays::findOrFail($id);
    return view('pays.edit', compact('pay'));
}



// Mettre à jour un pays
public function update(Request $request, $id)
{
    $request->validate([
        'code' => 'required|string|max:10',
        'nom' => 'required|string|max:255',
    ]);

    $pay = Pays::findOrFail($id);
    $pay->update([
        'code' => $request->code,
        'nom' => $request->nom,
    ]);

    return redirect()->route('pays.index')->with('success', 'Pays mis à jour avec succès!');
}

}
