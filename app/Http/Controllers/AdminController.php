<?php

namespace App\Http\Controllers;

use App\Models\Log; // <-- Importez le modèle Log
use App\Models\Pays;
use App\Models\Role;
use App\Models\Statut;
use App\Models\User;
use App\Models\Ville;
use App\Models\Groupe;
use App\Models\Promotion;
use App\Models\Sujet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;


class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function create()
    {
        $roles = Role::all();
        $statuts = Statut::all();
        $pays = Pays::all();
        $paysVilles = Pays::with([
            'villes' => fn($q) =>$q->select('id','nom','pays_id')->orderBy('nom')
        ])->orderBy('nom')->get(['id','nom']);
        $stagiaireId = Role::where('nom','Stagiaire')->value('id');
        
        $groupes = Groupe::all();
        $promotions = Promotion::where('status', 'active')->get();
        $sujets = Sujet::with('promotion')->get();

        return view("admin.create", compact('roles', 'statuts', 'pays', 'paysVilles', 'stagiaireId', 'groupes', 'promotions', 'sujets'));
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
        
        $groupes = Groupe::all();
        $promotions = Promotion::where('status', 'active')->get();
        $sujets = Sujet::with('promotion')->get();

        return view('admin.index',compact('admins','roles','statuts','pays','paysVilles','stagiaireId', 'groupes', 'promotions', 'sujets'));
    }

    /**
     * Affiche la liste des stagiaires avec des options de filtrage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function indexStagiaire(Request $request)
    {
        // Commencez par la requête de base pour les stagiaires
        $query = User::with(['pays', 'ville', 'role', 'statut', 'groupe', 'promotion', 'sujets'])
            ->whereHas('role', function($q) {
                $q->where('nom', 'Stagiaire');
            });

        // Appliquer les filtres basés sur la requête GET
        if ($request->filled('nom')) {
            $searchTerm = $request->input('nom');
            $query->where(function(Builder $q) use ($searchTerm) { // Utiliser Builder pour les clauses de groupe
                $q->where('nom', 'like', '%' . $searchTerm . '%')
                  ->orWhere('prenom', 'like', '%' . $searchTerm . '%')
                  ->orWhere('code', 'like', '%' . $searchTerm . '%'); // NOUVEAU : Inclure le champ 'code' dans la recherche
            });
        }

        if ($request->filled('statut_id')) {
            $query->where('statut_id', $request->input('statut_id'));
        }

        if ($request->filled('groupe_id')) {
            $query->where('groupe_id', $request->input('groupe_id'));
        }

        if ($request->filled('promotion_id')) {
            $query->where('promotion_id', $request->input('promotion_id'));
        }

        // Exécutez la requête paginée (avec 10 résultats par page, ajustable)
        $admins = $query->paginate(10); // Le nom de variable 'admins' peut être changé pour 'stagiaires' si plus approprié

        // Passer les données nécessaires pour les filtres et les modales aux vues
        $roles = Role::all();
        $statuts = Statut::all();
        $pays = Pays::all();
        $paysVilles = Pays::with([
            'villes' => fn($q) =>$q->select('id','nom','pays_id')->orderBy('nom')
        ])->orderBy('nom')->get(['id','nom']);
        $stagiaireId = Role::where('nom','Stagiaire')->value('id');

        $groupes = Groupe::all();
        $promotions = Promotion::where('status', 'active')->get();
        $sujets = Sujet::with('promotion')->get();

        return view('admin.index_stagiaire', compact('admins', 'roles', 'statuts', 'pays', 'paysVilles', 'stagiaireId', 'groupes', 'promotions', 'sujets'));
    }

    public function store(Request $request)
    {
        try {
            $stagiaireRoleId = Role::where('nom', 'Stagiaire')->value('id');

            $rules = [
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
                'universite' => 'nullable|string|max:255',
                'faculte' => 'nullable|string|max:255',
                'titre_formation' => 'nullable|string|max:255',
                'groupe_id' => 'nullable|exists:groupes,id',
                'promotion_id' => 'nullable|exists:promotions,id',
                'sujet_ids' => 'nullable|array',
                'sujet_ids.*' => 'exists:sujets,id',
            ];

            if ($request->input('role_id') == $stagiaireRoleId) {
                $rules['universite'] = 'required|string|max:255';
                $rules['faculte'] = 'required|string|max:255';
                $rules['titre_formation'] = 'required|string|max:255';
                $rules['groupe_id'] = 'nullable|exists:groupes,id';
                $rules['promotion_id'] = 'nullable|exists:promotions,id';
            }

            $validated = $request->validate($rules);

            $validated['password'] = Hash::make($validated['password']);
            
            do {
                $code = Str::upper(Str::random(6));
            } while (User::where('code', $code)->exists());
            $validated['code'] = $code;

            $sujetIds = $validated['sujet_ids'] ?? [];
            unset($validated['sujet_ids']);

            $user = User::create($validated);

            if ($user->role_id === $stagiaireRoleId && !empty($sujetIds)) {
                $user->sujets()->attach($sujetIds);
            }

            // --- Ajout du log de création ---
            Log::create([
                'user_id' => Auth::id(), // ID de l'administrateur connecté
                'action' => 'user_created',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a été créé.",
                'object_snapshot' => $user->toArray(), // Enregistre l'état de l'utilisateur créé
            ]);
            // --- Fin de l'ajout du log ---

            $role = $user->role ? $user->role->nom : null ;
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
        
        $groupes = Groupe::all();
        $promotions = Promotion::where('status', 'active')->get();
        $sujets = Sujet::with('promotion')->get();

        if ($user->isStagiaire()) {
            return view('admin.index_stagiaire', compact('user', 'roles', 'statuts', 'pays', 'paysVilles', 'stagiaireId', 'groupes', 'promotions', 'sujets'));
        } else {
            return view('admin.index', compact('user', 'roles', 'statuts', 'pays', 'paysVilles', 'stagiaireId', 'groupes', 'promotions', 'sujets'));
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
            $stagiaireRoleId = Role::where('nom', 'Stagiaire')->value('id');

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

            $isStagiaireInForm = ($loggedInUser->isAdministrateur() && $request->input('role_id') == $stagiaireRoleId) || $user->isStagiaire();

            if ($isStagiaireInForm) {
                $rules['universite'] = 'required|string|max:255';
                $rules['faculte'] = 'required|string|max:255';
                $rules['titre_formation'] = 'required|string|max:255';
                $rules['groupe_id'] = 'nullable|exists:groupes,id';

                $rules['promotion_id'] = 'nullable|exists:promotions,id';

                $rules['sujet_ids'] = 'nullable|array';
                $rules['sujet_ids.*'] = 'exists:sujets,id';
            } else {
                $rules['universite'] = 'nullable|string|max:255';
                $rules['faculte'] = 'nullable|string|max:255';
                $rules['titre_formation'] = 'nullable|string|max:255';
                $rules['groupe_id'] = 'nullable|exists:groupes,id';
                $rules['promotion_id'] = 'nullable|exists:promotions,id';
                $rules['sujet_ids'] = 'nullable|array';
            }

            $validatedData = $request->validate($rules);

            // --- Sauvegarde l'état actuel de l'utilisateur AVANT la mise à jour pour le log ---
            $oldUserSnapshot = $user->toArray(); 
            // --- Fin de la sauvegarde de l'état précédent ---

            if (!empty($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            } else {
                unset($validatedData['password']);
            }

            if (!$isStagiaireInForm) {
                $validatedData['universite'] = null;
                $validatedData['faculte'] = null;
                $validatedData['titre_formation'] = null;
                $validatedData['groupe_id'] = null;
                $validatedData['promotion_id'] = null;
            }
            
            $sujetIds = $validatedData['sujet_ids'] ?? [];
            unset($validatedData['sujet_ids']);

            $user->update($validatedData);

            if ($isStagiaireInForm) {
                $user->sujets()->sync($sujetIds);
            } else {
                $user->sujets()->detach();
            }

            // --- Ajout du log de mise à jour ---
            Log::create([
                'user_id' => Auth::id(), // ID de l'administrateur connecté
                'action' => 'user_updated',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a été mis à jour.",
                'object_snapshot' => [
                    'old' => $oldUserSnapshot,
                    'new' => $user->fresh()->toArray(), // Récupère le nouvel état après la mise à jour
                ],
            ]);
            // --- Fin de l'ajout du log ---


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

        // --- Sauvegarde l'état de l'utilisateur AVANT la suppression pour le log ---
        $deletedUserSnapshot = $user->toArray();
        $deletedUserRoleName = $user->role ? $user->role->nom : 'Inconnu';
        // --- Fin de la sauvegarde de l'état précédent ---

        $role = $user->role ? $user->role->nom : null;

        if ($user->isStagiaire()) {
            $user->sujets()->detach();
        }

        $user->delete();

        // --- Ajout du log de suppression ---
        Log::create([
            'user_id' => Auth::id(), // ID de l'administrateur connecté
            'action' => 'user_deleted',
            'log_message' => "L'utilisateur '{$deletedUserSnapshot['nom']} {$deletedUserSnapshot['prenom']}' (ID: {$deletedUserSnapshot['id']}, Rôle: {$deletedUserRoleName}) a été supprimé.",
            'object_snapshot' => $deletedUserSnapshot, // Enregistre l'état de l'utilisateur supprimé
        ]);
        // --- Fin de l'ajout du log ---


        if ($role === 'Administrateur' || $role === 'Superviseur') {
            return redirect()->route('admin.index')->with('success', 'Utilisateur (Admin/Superviseur) supprimé avec succès.');
        } else {
            return redirect()->route('admin.users.stagiaires')->with('success', 'Stagiaire supprimé avec succès.');
        }
    }




public function showStagiaireDetails(User $user)
    {
        // Assurez-vous que l'utilisateur est bien un stagiaire
        if (!$user->isStagiaire()) {
            abort(404, 'Stagiaire non trouvé ou accès non autorisé.');
        }

        // Charger toutes les relations nécessaires pour la page de détails
        // J'utilise `with` pour charger les relations Eager Loading afin d'éviter les problèmes N+1.
        $user->load([
            'notes',
            'fichiersPossedes',
            'fichiersTeleverses',
            'pays',
            'ville',
            'statut',
            'groupe',
            'promotion',
            'sujets',
            'role',
        ]);

        // Pour les coéquipiers : trouver d'autres stagiaires dans le même groupe (s'il y en a un)
        $coequipiers = collect();
        if ($user->groupe_id) { // Vérifier si le stagiaire a un groupe assigné
            $coequipiers = User::where('groupe_id', $user->groupe_id)
                               ->where('id', '!=', $user->id) // Exclure le stagiaire actuel
                               ->whereHas('role', function ($query) {
                                   $query->where('nom', 'Stagiaire'); // S'assurer que ce sont bien des stagiaires
                               })
                               ->get();
        }

        return view('admin.stagiaires.show', compact('user', 'coequipiers'));
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
$validatedData = $request->only(['nom', 'prenom', 'email', 'telephone', 'cin', 'adresse']);

    $user = User::findOrFail($id);

    // --- Sauvegarde l'état actuel de l'utilisateur AVANT la mise à jour pour le log ---
    $oldUserSnapshot = $user->toArray();
    // --- Fin de la sauvegarde de l'état précédent ---

// Si mot de passe renseigné, on le prépare ici
if ($request->filled('new_password')) {
    $user->password = Hash::make($request->new_password);
}

// Met à jour tous les autres champs
$user->fill($validatedData);

// Sauvegarde tout en une seule fois
$user->save();

    // --- Ajout du log de mise à jour de profil ---
    Log::create([
        'user_id' => Auth::id(), // ID de l'utilisateur connecté (celui qui modifie son profil)
        'action' => 'profile_updated',
        'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}) a mis à jour son propre profil.",
        'object_snapshot' => [
            'old' => $oldUserSnapshot,
            'new' => $user->fresh()->toArray(),
        ],
    ]);
    // --- Fin de l'ajout du log ---
    
    return redirect()->back()->with('success', 'Profil mis à jour avec succès !');
}



public function showLogs(Request $request)
    {
        // Récupérer tous les logs, ou paginer si vous en avez beaucoup
        // On charge aussi l'utilisateur qui a effectué l'action pour l'afficher
        $logsQuery = Log::with('user');

        // Optionnel : Ajouter des filtres (par utilisateur, par action, par date)
        if ($request->filled('user_id')) {
            $logsQuery->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('action')) {
            $logsQuery->where('action', $request->input('action'));
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $logsQuery->whereBetween('created_at', [$request->input('start_date') . ' 00:00:00', $request->input('end_date') . ' 23:59:59']);
        }


        $logs = $logsQuery->latest()->paginate(20); // Les logs les plus récents en premier, avec pagination

        // Pour les filtres, vous pourriez vouloir passer les utilisateurs et les types d'actions
        $usersForFilter = User::select('id', 'nom', 'prenom')->orderBy('nom')->get();
        // Une liste d'actions uniques si vous voulez un filtre déroulant
        $actionsForFilter = Log::select('action')->distinct()->pluck('action');


        return view('admin.logs.index', compact('logs', 'usersForFilter', 'actionsForFilter'));
    }

}