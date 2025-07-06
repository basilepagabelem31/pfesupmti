<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Log; // N'oubliez pas d'importer votre modèle Log
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Pour récupérer l'utilisateur connecté
use Illuminate\Validation\ValidationException; // Pour capturer les exceptions de validation

class RoleController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $roles = Role::all();

        // Enregistrement du log : Consultation de la liste des rôles
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_roles_list',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a consulté la liste des rôles. Nombre de rôles: {$roles->count()}.",
            'object_snapshot' => [
                'roles_count' => $roles->count(),
            ],
        ]);

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté

        // Enregistrement du log : Accès au formulaire de création de rôle
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_role_create_form',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a accédé au formulaire de création d'un nouveau rôle.",
            'object_snapshot' => [],
        ]);

        return view('roles.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $newRole = null; // Pour la portée du try/catch

        try {
            $validated = $request->validate([
                'nom' => 'required|max:255|unique:roles,nom', // Ajout de unique pour éviter les doublons
                'description' => 'required',
            ]);

            $newRole = Role::create($validated);

            // Enregistrement du log : Création de rôle réussie
            Log::create([
                'user_id' => $user->id,
                'action' => 'role_created_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a créé le rôle '{$newRole->nom}' (ID: {$newRole->id}).",
                'object_snapshot' => $newRole->toArray(),
            ]);

            return redirect()->route('roles.index')->with('success', 'Rôle créé avec succès.');

        } catch (ValidationException $e) {
            // Enregistrement du log : Échec de validation lors de la création de rôle
            Log::create([
                'user_id' => $user->id,
                'action' => 'role_create_validation_failed',
                'log_message' => "Échec de validation lors de la tentative de création d'un rôle par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'input' => $request->all(),
                    'errors' => $e->errors(),
                ],
            ]);
            return back()->withInput()->withErrors($e->errors())->with('error', 'Veuillez corriger les erreurs dans le formulaire.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la création de rôle
            Log::create([
                'user_id' => $user->id,
                'action' => 'role_create_critical_error',
                'log_message' => "Erreur critique inattendue lors de la création d'un rôle par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'input' => $request->all(),
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création du rôle : ' . $e->getMessage());
        }
    }

    public function show(Role $role)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté

        // Enregistrement du log : Consultation des détails d'un rôle
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_role_details',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a consulté les détails du rôle '{$role->nom}' (ID: {$role->id}).",
            'object_snapshot' => $role->toArray(),
        ]);

        return view('roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté

        // Enregistrement du log : Accès au formulaire d'édition de rôle
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_role_edit_form',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a accédé au formulaire d'édition du rôle '{$role->nom}' (ID: {$role->id}).",
            'object_snapshot' => $role->toArray(),
        ]);

        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $oldRoleData = $role->toArray(); // Capture l'état avant la mise à jour

        try {
            $validated = $request->validate([
                'nom' => 'required|max:255|unique:roles,nom,' . $role->id, // Unique, ignorant l'ID actuel du rôle
                'description' => 'required',
            ]);

            $role->update($validated);

            // Enregistrement du log : Mise à jour de rôle réussie
            Log::create([
                'user_id' => $user->id,
                'action' => 'role_updated_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a mis à jour le rôle '{$role->nom}' (ID: {$role->id}).",
                'object_snapshot' => [
                    'role_id' => $role->id,
                    'old_data' => $oldRoleData,
                    'new_data' => $role->toArray(),
                ],
            ]);

            return redirect()->route('roles.index')->with('success', 'Rôle mis à jour avec succès.');

        } catch (ValidationException $e) {
            // Enregistrement du log : Échec de validation lors de la mise à jour de rôle
            Log::create([
                'user_id' => $user->id,
                'action' => 'role_update_validation_failed',
                'log_message' => "Échec de validation lors de la tentative de mise à jour du rôle '{$role->nom}' (ID: {$role->id}) par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'role_id' => $role->id,
                    'input' => $request->all(),
                    'errors' => $e->errors(),
                ],
            ]);
            return back()->withInput()->withErrors($e->errors())->with('error', 'Veuillez corriger les erreurs dans le formulaire.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la mise à jour de rôle
            Log::create([
                'user_id' => $user->id,
                'action' => 'role_update_critical_error',
                'log_message' => "Erreur critique inattendue lors de la mise à jour du rôle '{$role->nom}' (ID: {$role->id}) par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'role_id' => $role->id,
                    'input' => $request->all(),
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour du rôle : ' . $e->getMessage());
        }
    }

    public function destroy(Role $role)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $roleData = $role->toArray(); // Capture les données avant suppression

        try {
            // Optionnel : Vérifier si le rôle est utilisé par des utilisateurs avant de supprimer
            if ($role->users()->count() > 0) {
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'role_delete_failed_in_use',
                    'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a tenté de supprimer le rôle '{$roleData['nom']}' (ID: {$roleData['id']}) mais il est encore attribué à des utilisateurs.",
                    'object_snapshot' => $roleData,
                ]);
                return back()->with('error', 'Impossible de supprimer ce rôle car il est attribué à des utilisateurs.');
            }

            $role->delete();

            // Enregistrement du log : Suppression de rôle réussie
            Log::create([
                'user_id' => $user->id,
                'action' => 'role_deleted_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a supprimé le rôle '{$roleData['nom']}' (ID: {$roleData['id']}).",
                'object_snapshot' => $roleData,
            ]);

            return redirect()->route('roles.index')->with('success', 'Rôle supprimé avec succès.');

        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la suppression de rôle
            Log::create([
                'user_id' => $user->id,
                'action' => 'role_delete_critical_error',
                'log_message' => "Erreur critique inattendue lors de la suppression du rôle '{$roleData['nom']}' (ID: {$roleData['id']}) par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'role_id' => $roleData['id'],
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->with('error', 'Une erreur est survenue lors de la suppression du rôle : ' . $e->getMessage());
        }
    }
}