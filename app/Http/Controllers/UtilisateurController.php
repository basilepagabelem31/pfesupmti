<?php

namespace App\Http\Controllers;
use  App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;
use App\Models\Email_log;
use App\Models\Pays;
use App\Models\Ville;
use App\Models\Role;
use App\Models\Statut;

use Illuminate\Http\Request;

class UtilisateurController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function index()
{
    // Récupérer tous les utilisateurs avec leurs relations (pays, ville, rôle, statut, email_log)
    $utilisateurs = Utilisateur::with(['pays', 'ville', 'role', 'statut', 'emailLog'])->get();

    // Retourner la vue avec les utilisateurs
    return view('utilisateurs.index', compact('utilisateurs'));
}


     public function create()
     {
         // Récupérer les données nécessaires aux relations (pays, villes, rôles, statuts, emailLogs)
         $pays = Pays::all();
         $villes = Ville::all();
         $roles = Role::all();  // Vous pouvez filtrer pour le rôle superviseur
        //  $roles = Role::where('nom', 'superviseur')->get();  // Vous pouvez filtrer pour le rôle superviseur
         $statuts = Statut::all();
         $emailLogs = Email_log::all();
     
         return view('utilisateurs.create', compact('pays', 'villes', 'roles', 'statuts', 'emailLogs'));
     }

     


     public function store(Request $request)
{
    // Valider les données reçues
    $validated = $request->validate([
        'nom' => 'required|string',
        'prenom' => 'required|string',
        'email' => 'required|email|unique:utilisateurs,email',
        'telephone' => 'required|string',
        'password'=>'required|string',
        'cin' => 'required|string|unique:utilisateurs,cin',
        'code' => 'required|string|unique:utilisateurs,code',
        'adresse' => 'required|string',
        'pays_id' => 'required|exists:pays,id',
        'ville_id' => 'required|exists:villes,id',
        'role_id' => 'required|exists:roles,id',
        'statut_id' => 'required|exists:statuts,id',
        'email_log_id' => 'required|exists:email_logs,id',
    ]);

    $validated['password'] = Hash::make($validated['password']);


    // Créer l'utilisateur
    $utilisateur = Utilisateur::create($validated);

    // Rediriger ou retourner une réponse JSON
    return redirect()->route('utilisateurs.index')->with('success', 'Superviseur créé avec succès!');
}




public function edit($id)
{
    // Récupérer l'utilisateur à éditer
    $utilisateur = Utilisateur::with(['pays', 'ville', 'role', 'statut', 'emailLog'])->findOrFail($id);

    // Récupérer les données pour les relations (pays, villes, rôles, statuts, emailLogs)
    $pays = Pays::all();
    $villes = Ville::all();
    $roles = Role::all();
    $statuts = Statut::all();
    $emailLogs = Email_log::all();

    return view('utilisateurs.edit', compact('utilisateur', 'pays', 'villes', 'roles', 'statuts', 'emailLogs'));
}




public function update(Request $request, $id)
{
    // Récupérer l'utilisateur à mettre à jour
    $utilisateur = Utilisateur::findOrFail($id);

    // Valider les données reçues
    $validated = $request->validate([
        'nom' => 'required|string',
        'prenom' => 'required|string',
        'email' => 'required|email|unique:utilisateurs,email,' . $utilisateur->id,
        'telephone' => 'required|string',
        'password' => 'required|string',
        'cin' => 'required|string|unique:utilisateurs,cin,' . $utilisateur->id,
        'code' => 'required|string|unique:utilisateurs,code,' . $utilisateur->id,
        'adresse' => 'required|string',
        'pays_id' => 'required|exists:pays,id',
        'ville_id' => 'required|exists:villes,id',
        'role_id' => 'required|exists:roles,id',
        'statut_id' => 'required|exists:statuts,id',
        'email_log_id' => 'required|exists:email_logs,id',
    ]);

    $validated['password'] = Hash::make($validated['password']);


    // Mettre à jour l'utilisateur avec les données validées
    $utilisateur->update($validated);

    // Rediriger ou retourner une réponse JSON
    return redirect()->route('utilisateurs.index')->with('success', 'Superviseur mis à jour avec succès!');
}




public function destroy($id)
{
    // Récupérer l'utilisateur à supprimer
    $utilisateur = Utilisateur::findOrFail($id);

    // Supprimer l'utilisateur
    $utilisateur->delete();

    // Rediriger avec un message de succès
    return redirect()->route('utilisateurs.index')->with('success', 'Superviseur supprimé avec succès!');
}

}
