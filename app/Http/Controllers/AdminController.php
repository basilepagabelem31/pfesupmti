<?php

namespace App\Http\Controllers;

use App\Models\Pays;
use App\Models\Role;
use App\Models\Statut;
use App\Models\User;
use App\Models\Ville;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth; 
use App\helper\LogHelper; 

class AdminController extends Controller
{
    public function dashboard()
    {
        // Log l'accès au tableau de bord de l'admin
        LogHelper::logAction(
            'Accès au tableau de bord Administrateur',
            'L\'utilisateur ' . Auth::user()->nom . ' ' . Auth::user()->prenom . ' (ID: ' . Auth::id() . ') a accédé au tableau de bord administrateur.',
            Auth::id()
        );
        return view('admin.dashboard');
    }

    public function create()
    {
        // Aucune action de création réelle ici, juste l'affichage du formulaire. Pas besoin de loguer.
        $roles = Role::all();
        $statuts = Statut::all();
        $pays = Pays::all();
        $paysVilles = Pays::with([
            'villes' => fn($q) =>$q->select('id','nom','pays_id')->orderBy('nom')
        ])->orderBy('nom')->get(['id','nom']);
        $stagiaireId = Role::where('nom','Stagiaire')->value('id');

        return view("admin.create", compact('roles', 'statuts', 'pays', 'paysVilles', 'stagiaireId'));
    }

    public function index(){
        // Pas besoin de loguer l'affichage d'une liste
        $admins = User::with(['pays','ville','role','statut'])
        ->whereHas('role',function($q){
            $q->whereIn('nom',['Administrateur','Superviseur']);
        })->paginate(10);
        $roles=Role::all();
        $statuts=Statut::all();
        $pays=Pays::all();
        $paysVilles=Pays::with([
            'villes' => fn($q) =>$q->select('id','nom','pays_id')->orderBy('nom')
        ])->orderBy('nom')->get(['id','nom']);
        $stagiaireId = Role::where('nom','Stagiaire')->value('id');
        return view('admin.index',compact('admins','roles','statuts','pays','paysVilles','stagiaireId'));
    }

    public function indexStagiaire()
    {
        // Pas besoin de loguer l'affichage d'une liste
        $admins = User::with(['pays','ville','role','statut'])
            ->whereHas('role', function($q){
                $q->where('nom', 'Stagiaire');
            })->paginate(10);

        $roles = Role::all();
        $statuts = Statut::all();
        $pays = Pays::all();
        $paysVilles = Pays::with([
            'villes' => fn($q) =>$q->select('id','nom','pays_id')->orderBy('nom')
        ])->orderBy('nom')->get(['id','nom']);
        $stagiaireId = Role::where('nom','Stagiaire')->value('id');

        return view('admin.index_stagiaire', compact('admins', 'roles', 'statuts', 'pays', 'paysVilles', 'stagiaireId'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                "nom"=> "required|string",
                "prenom" => "required|string",
                "password" => "required",
                "email" => "required|email|unique:users",
                "telephone" => "required|string",
                "cin" => "required|unique:users|string",
                "adresse"=> "required|string",
                "pays_id" =>"required|exists:pays,id",
                "ville_id" =>"required|exists:villes,id",
                "role_id" => "required|exists:roles,id",
                "statut_id" =>"required|exists:statuts,id",
                'universite' => 'required_if:role_id,' . Role::where('nom', 'Stagiaire')->value('id'),
                'faculte' => 'required_if:role_id,' . Role::where('nom', 'Stagiaire')->value('id'),
                'titre_formation' => 'required_if:role_id,' . Role::where('nom', 'Stagiaire')->value('id'),
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $admin = User::create($validated);

            // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA CREATION
            $creator = Auth::user(); // L'utilisateur (Super Admin/Superviseur) qui crée le compte
            $roleNom = $admin->role ? $admin->role->nom : 'N/A'; // Récupérer le nom du rôle

            LogHelper::logAction(
                'Création de compte utilisateur',
                'Le ' . $creator->role->nom . ' ' . $creator->nom . ' ' . $creator->prenom . ' (ID: ' . $creator->id . ') a créé le compte de ' . $roleNom . ' : ' . $admin->prenom . ' ' . $admin->nom . ' (ID: ' . $admin->id . ', Email: ' . $admin->email . ').',
                Auth::id()
            );

            $role = $admin->role ? $admin->role->nom : null ;
            if ($role === 'Administrateur'|| $role === 'Superviseur'){
                return redirect()->route('admin.index')->with('success', 'Admin/Superviseur a été bien créé.');
            }else {
                return redirect()->route('admin.users.stagiaires')->with('success', 'Stagiaire a été bien créé.');
            }
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput($request->except('password'))
                ->withErrors($e->errors())
                ->with('open_add_modal', true);
        }
    }

    public function edit(User $user)
    {
        // Pas besoin de loguer l'affichage du formulaire d'édition.
        $loggedInUser = Auth::user();

        if ($loggedInUser->isSuperviseur() && !$user->isStagiaire()) {
            abort(403, "Vous n'êtes pas autorisé à modifier ce type d'utilisateur.");
        }

        if ($loggedInUser->isStagiaire()) {
            abort(403, "Accès non autorisé.");
        }

        $roles = Role::all();
        $statuts = Statut::all();
        $pays = Pays::all();
        $paysVilles = Pays::with([
            'villes' => fn($q) => $q->select('id','nom','pays_id')->orderBy('nom')
        ])->orderBy('nom')->get(['id','nom']);
        $stagiaireId = Role::where('nom','Stagiaire')->value('id');

        if ($user->isStagiaire()) {
            return view('admin.index_stagiaire', compact('user', 'roles', 'statuts', 'pays', 'paysVilles', 'stagiaireId'));
        } else {
            return view('admin.index', compact('user', 'roles', 'statuts', 'pays', 'paysVilles', 'stagiaireId'));
        }
    }

    public function update(Request $request, User $user)
    {
        $loggedInUser = Auth::user();

        if ($loggedInUser->isSuperviseur() && !$user->isStagiaire()) {
            abort(403, "Vous n'êtes pas autorisé à modifier ce type d'utilisateur.");
        }
        if ($loggedInUser->isStagiaire()) {
            abort(403, "Accès non autorisé.");
        }

        try {
            $rules = [
                "nom"=> "required|string",
                "prenom" => "required|string",
                "password" => "nullable|min:8",
                "email" => "required|email|unique:users,email," . $user->id,
                "telephone" => "required|string",
                "cin" => "required|string|unique:users,cin," . $user->id,
                "adresse"=> "required|string",
                "pays_id" =>"required|exists:pays,id",
                "ville_id" =>"required|exists:villes,id",
                "statut_id" =>"required|exists:statuts,id",
            ];

            if ($loggedInUser->isAdministrateur()) {
                $rules['role_id'] = "required|exists:roles,id";
            } else {
                $request->request->remove('role_id');
            }

            if ($user->isStagiaire()) {
                $stagiaireRoleId = Role::where('nom', 'Stagiaire')->value('id');
                $rules['universite'] = 'required_if:role_id,' . $stagiaireRoleId . '|nullable|string|max:255';
                $rules['faculte'] = 'required_if:role_id,' . $stagiaireRoleId . '|nullable|string|max:255';
                $rules['titre_formation'] = 'required_if:role_id,' . $stagiaireRoleId . '|nullable|string|max:255';
                $rules['id_groupe'] = 'nullable|exists:groupes,id';
                $rules['id_sujet'] = 'nullable|exists:sujets,id';
                $rules['id_promotion'] = 'nullable|exists:promotions,id';
            } else {
                $request->request->remove('universite');
                $request->request->remove('faculte');
                $request->request->remove('titre_formation');
                $request->request->remove('id_groupe');
                $request->request->remove('id_sujet');
                $request->request->remove('id_promotion');
            }

            $validatedData = $request->validate($rules);

            // Sauvegarder les données originales pour le log
            $oldData = $user->getOriginal();
            $oldRole = $user->role ? $user->role->nom : 'N/A'; // Ancien rôle

            if (!empty($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            } else {
                unset($validatedData['password']);
            }

            $user->update($validatedData);

            // Recharger l'utilisateur pour avoir les nouvelles données et le nouveau rôle
            $user->load('role');
            $newRole = $user->role ? $user->role->nom : 'N/A';

            // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA MODIFICATION
            $modifier = Auth::user(); // L'utilisateur (Super Admin/Superviseur) qui modifie
            $logMessage = 'Le ' . $modifier->role->nom . ' ' . $modifier->nom . ' ' . $modifier->prenom . ' (ID: ' . $modifier->id . ') a modifié le compte de ' . ($user->prenom ?? '') . ' ' . ($user->nom ?? '') . ' (ID: ' . $user->id . ', Email: ' . $user->email . '). ';

            // Comparaison des champs clés pour un log plus détaillé
            $changes = [];
            foreach (['nom', 'prenom', 'email', 'telephone', 'cin', 'adresse', 'pays_id', 'ville_id', 'statut_id'] as $field) {
                if (isset($validatedData[$field]) && $oldData[$field] != $validatedData[$field]) {
                    $changes[] = $field . ": '" . $oldData[$field] . "' -> '" . $validatedData[$field] . "'";
                }
            }
            if ($loggedInUser->isAdministrateur() && $oldRole !== $newRole) {
                $changes[] = "Rôle: '" . $oldRole . "' -> '" . $newRole . "'";
            }

            if (!empty($changes)) {
                $logMessage .= 'Changements: ' . implode(', ', $changes) . '.';
            } else {
                $logMessage .= 'Aucun changement significatif de données de profil (ou seulement le mot de passe).';
            }


            LogHelper::logAction(
                'Modification de compte utilisateur',
                $logMessage,
                Auth::id()
            );


            $role = $user->role ? $user->role->nom : null ;
            if ($role === 'Administrateur' || $role === 'Superviseur'){
                return redirect()->route('admin.index')->with('success', 'Admin/Superviseur a été bien mis à jour.');
            }else {
                return redirect()->route('admin.users.stagiaires')->with('success', 'Stagiaire a été bien mis à jour.');
            }
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput($request->except('password'))
                ->withErrors($e->errors())
                ->with('edit_user_id', $user->id);
        }
    }

    public function delete(User $user)
    {
        $loggedInUser = Auth::user();

        if ($user->id === $loggedInUser->id) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        if ($loggedInUser->isSuperviseur() && !$user->isStagiaire()) {
            abort(403, "Vous n'êtes pas autorisé à supprimer ce type d'utilisateur.");
        }
        if ($loggedInUser->isStagiaire()) {
            abort(403, "Accès non autorisé.");
        }

        if ($loggedInUser->isAdministrateur() && $user->isAdministrateur() && User::whereHas('role', function($q) { $q->where('nom', 'Administrateur'); })->count() <= 1) {
            return redirect()->back()->with('error', "Impossible de supprimer le dernier compte administrateur.");
        }

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA SUPPRESSION
        $deleter = Auth::user(); // L'utilisateur (Super Admin/Superviseur) qui supprime
        $deletedUserName = $user->prenom . ' ' . $user->nom;
        $deletedUserId = $user->id;
        $deletedUserEmail = $user->email;
        $deletedUserRole = $user->role ? $user->role->nom : 'N/A'; // Récupérer le rôle avant suppression

        $user->delete();

        LogHelper::logAction(
            'Suppression de compte utilisateur',
            'Le ' . $deleter->role->nom . ' ' . $deleter->nom . ' ' . $deleter->prenom . ' (ID: ' . $deleter->id . ') a supprimé le compte de ' . $deletedUserRole . ' : ' . $deletedUserName . ' (ID: ' . $deletedUserId . ', Email: ' . $deletedUserEmail . ').',
            Auth::id()
        );

        $role = $deletedUserRole; // Utiliser la variable déjà capturée
        if ($role === 'Administrateur' || $role === 'Superviseur') {
            return redirect()->route('admin.index')->with('success', 'Utilisateur (Admin/Superviseur) supprimé avec succès.');
        } else {
            return redirect()->route('admin.users.stagiaires')->with('success', 'Stagiaire supprimé avec succès.');
        }
    }

    public function getStagiaireRoleId()
    {
        return Role::where('nom', 'Stagiaire')->value('id');
    }

    public function profile()
    {
        $user = auth()->user();
        $pays = Pays::all();
        $villes = Ville::where('pays_id', $user->pays_id)->get();
        $statuts = Statut::all();

        return view('admin.profile', compact('user'));
    }

    public function updateProfile(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'telephone' => 'nullable|string',
            'cin' => 'required|string|unique:users,cin,' . $id,
            'adresse' => 'nullable|string',
        ]);

        $user = User::findOrFail($id);

        // Sauvegarder les données originales pour le log
        $oldData = $user->getOriginal();

        // Si mot de passe renseigné, on le prépare ici
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        // Met à jour tous les autres champs
        $user->fill($request->only(['nom', 'prenom', 'email', 'telephone', 'cin', 'adresse']));

        // Sauvegarde tout en une seule fois
        $user->save();

        // Recharger l'utilisateur pour avoir les nouvelles données
        $user->fresh();

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA MISE A JOUR DE SON PROPRE PROFIL PAR L'ADMIN
        $logMessage = 'Le Super Admin ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a modifié son propre profil. ';

        $changes = [];
        foreach (['nom', 'prenom', 'email', 'telephone', 'cin', 'adresse'] as $field) {
            if ($oldData[$field] != $user->{$field}) {
                $changes[] = $field . ": '" . ($oldData[$field] ?? 'null') . "' -> '" . ($user->{$field} ?? 'null') . "'";
            }
        }
        if ($request->filled('new_password')) {
            $changes[] = "Mot de passe: modifié";
        }

        if (!empty($changes)) {
            $logMessage .= 'Changements: ' . implode(', ', $changes) . '.';
        } else {
            $logMessage .= 'Aucun changement significatif de données de profil.';
        }

        LogHelper::logAction(
            'Modification de son propre profil (Super Admin)',
            $logMessage,
            $user->id // L'ID de l'utilisateur qui a effectué l'action
        );

        return redirect()->back()->with('success', 'Profil mis à jour avec succès !');
    }

    public function show(User $admin) // Utilisez 'User' si tous vos utilisateurs sont dans la même table
    {
        // Assurez-vous que l'utilisateur est bien un administrateur/super admin, si nécessaire
        if (!$admin->isAdministrateur()) { // Adapter si vous avez isSuperAdmin()
            abort(404);
        }
        return view('admin.admins.show', compact('admin'));
    }
}