<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Note;
use App\Models\Fichier; // Assurez-vous que Fichier est importé
use App\Models\Pays;    // Assurez-vous que Pays est importé
use App\Models\Ville;   // Assurez-vous que Ville est importé
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StagiaireController extends Controller
{
    public function index ()
    {
        return view('stagiaires.dashboard');
    }

    public function profiles()
    {
        // FIX: Changed 'fichiers' to 'fichiersPossedes' to match the User model's defined relationship.
        $user = Auth::user()->load('notes', 'fichiersPossedes');
        
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
            // Note: 'pays_id' and 'ville_id' validations removed as they are read-only in the UI.
            'current_password' => ['nullable', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'universite' => 'nullable|string|max:255',
            'faculte' => 'nullable|string|max:255',
            'titre_formation' => 'nullable|string|max:255',
        ]);

        $validatedData = $request->only([
            'nom', 'prenom', 'email', 'telephone', 'cin', 'adresse',
            'universite', 'faculte', 'titre_formation', 
        ]);

        $user = User::findOrFail($id);

        // If a new password is provided, hash and update it
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
            session()->flash('password_changed', true); // Add session flash for password change success
        }

        // Update all other fields
        $user->fill($validatedData);

        // Save everything in one go
        $user->save();

        return redirect()->back()->with('success', 'Profil mis à jour avec succès !');
    }
}
