<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\helper\LogHelper; 
class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|max:255',
            'description' => 'required',
        ]);

        $role = Role::create($request->all());

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA CREATION
        $user = Auth::user();
        LogHelper::logAction(
            'Création de rôle',
            'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a créé le rôle "' . $role->nom . '" (ID: ' . $role->id . ').',
            $user->id
        );

        return redirect()->route('roles.index')->with('success', 'Rôle créé avec succès.');
    }

    public function show(Role $role)
    {
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'nom' => 'required|max:255',
            'description' => 'required',
        ]);

        $user = Auth::user();
        $oldRoleData = $role->toArray(); // Capture les données avant la mise à jour

        $role->update($request->all());

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA MODIFICATION
        $changes = [];
        $validatedData = $request->all();
        foreach ($validatedData as $key => $value) {
            if (isset($oldRoleData[$key]) && $oldRoleData[$key] != $value) {
                $changes[] = ucfirst(str_replace('_', ' ', $key)) . ": '" . $oldRoleData[$key] . "' -> '" . $value . "'";
            }
        }

        $message = 'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a modifié le rôle "' . $role->nom . '" (ID: ' . $role->id . '). ';
        if (!empty($changes)) {
            $message .= 'Changements: ' . implode(', ', $changes) . '.';
        } else {
            $message .= 'Aucun changement détecté.';
        }

        LogHelper::logAction(
            'Modification de rôle',
            $message,
            $user->id
        );

        return redirect()->route('roles.index')->with('success', 'Rôle mis à jour avec succès.');
    }

    public function destroy(Role $role)
    {
        $user = Auth::user();

        // Capture les informations du rôle avant la suppression
        $deletedRoleNom = $role->nom;
        $deletedRoleId = $role->id;

        $role->delete();

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA SUPPRESSION
        LogHelper::logAction(
            'Suppression de rôle',
            'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a supprimé le rôle "' . $deletedRoleNom . '" (ID: ' . $deletedRoleId . ').',
            $user->id
        );

        return redirect()->route('roles.index')->with('success', 'Rôle supprimé avec succès.');
    }
}