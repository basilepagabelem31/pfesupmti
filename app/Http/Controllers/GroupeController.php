<?php

namespace App\Http\Controllers;

use App\Models\Groupe;
use Illuminate\Http\Request;

class GroupeController extends Controller
{
    public function index()
    {
        $groupes = Groupe::all();
        return view('groupes.index', compact('groupes'));
    }

    public function create()
    {
        return view('groupes.create');
    }

  public function store(Request $request)
{
    $request->validate([
        'nom' => 'required',
        'jour' => 'required',
        'heure_debut' => 'required|date_format:H:i',
        'heure_fin' => 'required|date_format:H:i',
    ]);

    if (!empty($request->groupe_id)) {  
        $groupe = Groupe::find($request->groupe_id);
        if ($groupe) {
            $groupe->update([
                'nom' => $request->nom,
                'jour' => $request->jour,
                'heure_debut' => $request->heure_debut,
                'heure_fin' => $request->heure_fin,
            ]);
            return redirect()->route('groupes.index')->with('success', 'Groupe mis à jour avec succès');
        } else {
            return redirect()->route('groupes.index')->with('error', 'Groupe non trouvé');
        }
    } else {
        Groupe::create([
            'code' => strtoupper(substr($request->nom, 0, 3)) . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT),
            'nom' => $request->nom,
            'jour' => $request->jour,
            'heure_debut' => $request->heure_debut,
            'heure_fin' => $request->heure_fin,
        ]);
        return redirect()->route('groupes.index')->with('success', 'Groupe ajouté avec succès');
    }
}




    public function edit($id)
{
    $groupe = Groupe::findOrFail($id);
    return view('groupes.edit', compact('groupe'));
}



    public function update(Request $request, $id)
{
    $request->validate([
        'nom' => 'required',
        'jour' => 'required',
        'heure_debut' => 'required|date_format:H:i',
        'heure_fin' => 'required|date_format:H:i',
    ]);

    $groupe = Groupe::findOrFail($id);
    $groupe->update($request->only(['nom', 'jour', 'heure_debut', 'heure_fin']));

    return redirect()->route('groupes.index')->with('success', 'Groupe mis à jour avec succès');
}



    public function destroy(Groupe $groupe)
{
    if ($groupe->users()->count() > 0) {
        return redirect()->back()->with('error', 'Impossible de supprimer ce groupe : il contient des stagiaires.');
    }

    $groupe->delete();
    return redirect()->back()->with('success', 'Groupe supprimé avec succès.');
}

public function show($id)
{
    $groupe = Groupe::findOrFail($id);
    return response()->json($groupe);
}


}

