<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Pays;
use App\Models\Ville;
use App\Models\Statut;
use Illuminate\Support\Facades\Hash;
class SuperviseurController extends Controller
{
    //

    public function index()
    {
        return view('supervisseur.test');
    }

    public function profile()
    {
        $user = Auth::user();
       
        return view('supervisseur.profile', compact('user'));
    }

    public function updateProfile(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->id != $id) {
            return redirect()->back()->with('error', 'Vous ne pouvez modifier que votre propre profil.');
        }
        
        $validatedData = $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'telephone' => 'nullable|string',
            'cin' => 'required|string|unique:users,cin,' . $id,
            'adresse' => 'nullable|string',
            'current_password' => ['nullable', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

       $validatedData = $request->only(['nom', 'prenom', 'email', 'telephone', 'cin', 'adresse']);

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