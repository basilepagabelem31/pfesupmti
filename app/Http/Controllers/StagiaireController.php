<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Note;
use App\Models\Fichier;
use App\Models\Pays;
use App\Models\Ville;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StagiaireController extends Controller
{
    //
    public function index ()
    {
        return view('stagiaires.dashboard');
    }

    public function profiles()
{
    $user = Auth::user()->load('notes', 'fichiers');
   
    return view('stagiaires.profiles', compact('user'));
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
        'current_password' => ['nullable', 'current_password'],
        'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        'universite' => 'nullable|string|max:255',
        'faculte' => 'nullable|string|max:255',
        'titre_formation' => 'nullable|string|max:255',
    ]);

    $validatedData = $request->only([
    'nom', 'prenom', 'email', 'telephone', 'cin', 'adresse',
    'universite', 'faculte','titre_formation', 
]);


    $user = User::findOrFail($id);

// Si mot de passe renseigné, on le prépare ici
if ($request->filled('new_password')) {
    $user->password = Hash::make($request->new_password);
}

// Met à jour tous les autres champs
$user->fill($validatedData);

// Sauvegarde tout en une seule fois
$user->save();

  

    return redirect()->back()->with('success', 'Profil mis à jour avec succès !');
}

}
