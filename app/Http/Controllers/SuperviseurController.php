<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Pays;
use App\Models\Ville;
use App\Models\Statut;
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
        $pays = Pays::all();
        $villes = Ville::all();
        $statuts = Statut::all();

        return view('supervisseur.profile', compact('user', 'pays', 'villes', 'statuts'));
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
            'pays_id' => 'required|exists:pays,id',
            'ville_id' => 'required|exists:villes,id',
        ]);

        $user->update($validatedData);

        return redirect()->back()->with('success', 'Profil mis à jour avec succès !');
    }

}