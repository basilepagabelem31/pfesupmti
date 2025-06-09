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
use Illuminate\Support\Facades\Auth; // Importez le facade Auth

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function create()
    {
        // Cette méthode est utilisée pour afficher le formulaire de création, souvent via une modale.
        // Les variables nécessaires pour les listes déroulantes (rôles, statuts, pays, villes) doivent être passées.
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

            $role = $admin->role ? $admin->role->nom : null ;
            if ($role === 'Administrateur'|| $role === 'Superviseur'){
                return redirect()->route('admin.index')->with('success', 'Admin/Superviseur a été bien créé.');
            }else {
                // Correction de la route de redirection pour les stagiaires
                return redirect()->route('admin.users.stagiaires')->with('success', 'Stagiaire a été bien créé.');
            }
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput($request->except('password'))
                ->withErrors($e->errors())
                ->with('open_add_modal', true); // Indicateur pour rouvrir la modale d'ajout
        }
    }

    /**
     * Affiche le formulaire d'édition d'un utilisateur.
     * Le superviseur ne peut éditer que les stagiaires.
     * L'administrateur peut éditer tous les utilisateurs.
     *
     * @param User $user Le modèle User à éditer (Laravel Model Binding)
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $loggedInUser = Auth::user();

        // Si l'utilisateur connecté est un superviseur et que l'utilisateur à éditer n'est PAS un stagiaire
        if ($loggedInUser->isSuperviseur() && !$user->isStagiaire()) {
            abort(403, "Vous n'êtes pas autorisé à modifier ce type d'utilisateur.");
        }

        // Si l'utilisateur connecté est un stagiaire, il ne peut pas utiliser cette route.
        if ($loggedInUser->isStagiaire()) {
            abort(403, "Accès non autorisé.");
        }

        // Récupération des données pour le formulaire d'édition
        $roles = Role::all();
        $statuts = Statut::all();
        $pays = Pays::all();
        $paysVilles = Pays::with([
            'villes' => fn($q) => $q->select('id','nom','pays_id')->orderBy('nom')
        ])->orderBy('nom')->get(['id','nom']);
        $stagiaireId = Role::where('nom','Stagiaire')->value('id');

        // Déterminer la vue à retourner en fonction du rôle de l'utilisateur à éditer
        if ($user->isStagiaire()) {
            // Si l'utilisateur à éditer est un stagiaire, retournez la vue de gestion des stagiaires
            // Cette vue devrait avoir une modale d'édition prête à s'ouvrir avec $user comme données
            return view('admin.index_stagiaire', compact('user', 'roles', 'statuts', 'pays', 'paysVilles', 'stagiaireId'));
        } else {
            // Sinon (admin ou superviseur), retournez la vue de gestion des admins/superviseurs
            // Cette vue devrait avoir une modale d'édition prête à s'ouvrir avec $user comme données
            return view('admin.index', compact('user', 'roles', 'statuts', 'pays', 'paysVilles', 'stagiaireId'));
        }
    }

    /**
     * Met à jour un utilisateur.
     * Le superviseur ne peut mettre à jour que les stagiaires.
     * L'administrateur peut mettre à jour tous les utilisateurs.
     *
     * @param Request $request
     * @param User $user Le modèle User à mettre à jour (Laravel Model Binding)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $loggedInUser = Auth::user();

        // Si l'utilisateur connecté est un superviseur et que l'utilisateur à mettre à jour n'est PAS un stagiaire
        if ($loggedInUser->isSuperviseur() && !$user->isStagiaire()) {
            abort(403, "Vous n'êtes pas autorisé à modifier ce type d'utilisateur.");
        }
        // Empêcher un stagiaire d'accéder à cette méthode via une route non protégée.
        if ($loggedInUser->isStagiaire()) {
            abort(403, "Accès non autorisé.");
        }

        try {
            $rules = [
                "nom"=> "required|string",
                "prenom" => "required|string",
                "password" => "nullable|min:8", // Mot de passe facultatif, mais min 8 caractères si fourni
                "email" => "required|email|unique:users,email," . $user->id,
                "telephone" => "required|string",
                "cin" => "required|string|unique:users,cin," . $user->id,
                "adresse"=> "required|string",
                "pays_id" =>"required|exists:pays,id",
                "ville_id" =>"required|exists:villes,id",
                "statut_id" =>"required|exists:statuts,id",
            ];

            // Les superviseurs ne peuvent pas changer le rôle. Seuls les admins le peuvent.
            if ($loggedInUser->isAdministrateur()) {
                $rules['role_id'] = "required|exists:roles,id";
            } else {
                // Si ce n'est pas un administrateur, assurez-vous que role_id n'est pas dans la requête ou est ignoré
                $request->request->remove('role_id');
            }

            // Règles spécifiques aux stagiaires si l'utilisateur en cours de modification est un stagiaire
            if ($user->isStagiaire()) {
                $stagiaireRoleId = Role::where('nom', 'Stagiaire')->value('id'); // Assurez-vous d'avoir l'ID du rôle Stagiaire
                $rules['universite'] = 'required_if:role_id,' . $stagiaireRoleId . '|nullable|string|max:255';
                $rules['faculte'] = 'required_if:role_id,' . $stagiaireRoleId . '|nullable|string|max:255';
                $rules['titre_formation'] = 'required_if:role_id,' . $stagiaireRoleId . '|nullable|string|max:255';
                $rules['id_groupe'] = 'nullable|exists:groupes,id';
                $rules['id_sujet'] = 'nullable|exists:sujets,id';
                $rules['id_promotion'] = 'nullable|exists:promotions,id';
            } else {
                // Si l'utilisateur n'est pas un stagiaire, assurez-vous que ces champs ne sont pas traités
                $request->request->remove('universite');
                $request->request->remove('faculte');
                $request->request->remove('titre_formation');
                $request->request->remove('id_groupe');
                $request->request->remove('id_sujet');
                $request->request->remove('id_promotion');
            }

            $validatedData = $request->validate($rules);

            if (!empty($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            } else {
                unset($validatedData['password']);
            }

            $user->update($validatedData);

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
                ->with('edit_user_id', $user->id); // Flasher l'ID de l'utilisateur qui était en cours d'édition
        }
    }

    /**
     * Supprime un utilisateur.
     * Le superviseur ne peut supprimer que les stagiaires.
     * L'administrateur peut supprimer tous les utilisateurs (sauf lui-même).
     *
     * @param User $user Le modèle User à supprimer (Laravel Model Binding)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(User $user)
    {
        $loggedInUser = Auth::user();

        // Empêcher l'utilisateur de se supprimer lui-même
        if ($user->id === $loggedInUser->id) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Si l'utilisateur connecté est un superviseur et que l'utilisateur à supprimer n'est PAS un stagiaire
        if ($loggedInUser->isSuperviseur() && !$user->isStagiaire()) {
            abort(403, "Vous n'êtes pas autorisé à supprimer ce type d'utilisateur.");
        }
        // Empêcher un stagiaire d'accéder à cette méthode via une route non protégée.
        if ($loggedInUser->isStagiaire()) {
            abort(403, "Accès non autorisé.");
        }

        // Si l'utilisateur est un administrateur et tente de supprimer un autre administrateur,
        // vous pourriez vouloir ajouter une vérification pour le "dernier administrateur"
        if ($loggedInUser->isAdministrateur() && $user->isAdministrateur() && User::whereHas('role', function($q) { $q->where('nom', 'Administrateur'); })->count() <= 1) {
            return redirect()->back()->with('error', "Impossible de supprimer le dernier compte administrateur.");
        }

        $role = $user->role ? $user->role->nom : null; // Récupère le rôle avant la suppression

        $user->delete();

        // Redirection en fonction du rôle de l'utilisateur supprimé
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
}
