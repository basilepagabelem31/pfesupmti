<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Note;
use App\Models\Fichier;
use App\Models\Pays;
use App\Models\Ville;
use Illuminate\Support\Facades\Auth;
class StagiaireController extends Controller
{
    //
    public function index ()
    {
        return view('stagiaires.dashboard');
    }

    public function profiles()
{
    $user = Auth::user()->load('notes', 'fichiers', 'ville', 'pays');
    $pays = Pays::all();
    $villes = Ville::all();

    return view('stagiaires.profiles', compact('user', 'pays', 'villes'));
}



public function update(Request $request, $id)
{
    $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'telephone' => 'nullable|string|max:20',
        'cin' => 'nullable|string|max:20',
        'adresse' => 'nullable|string|max:255',
        'pays_id' => 'required|exists:pays,id',
        'ville_id' => 'required|exists:villes,id',
    ]);

    $validatedData = $request->only(['nom', 'prenom', 'email', 'telephone', 'cin', 'adresse', 'pays_id', 'ville_id']);

    $user = User::findOrFail($id);
    $user->update($validatedData);

    return redirect()->back()->with('success', 'Profil mis à jour avec succès !');
}

}
