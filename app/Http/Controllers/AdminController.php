<?php

namespace App\Http\Controllers;

use App\Models\Pays;
use App\Models\Role;
use App\Models\Statut;
use App\Models\User;
use App\Models\Ville;
use App\Models\Groupe;    // Importez le modèle Groupe
use App\Models\Promotion; // Importez le modèle Promotion
use App\Models\Sujet;     // Importez le modèle Sujet
use Illuminate\Http\Request; // Importez la classe Request
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
            $nom = $request->input('nom');
            $query->where(function($q) use ($nom) {
                $q->where('nom', 'like', '%' . $nom . '%')
                  ->orWhere('prenom', 'like', '%' . $nom . '%');
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
        $admins = $query->paginate(10);

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
                $rules['groupe_id'] = 'required|exists:groupes,id';
                $rules['promotion_id'] = 'required|exists:promotions,id';
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
                $rules['groupe_id'] = 'required|exists:groupes,id';
                $rules['promotion_id'] = 'required|exists:promotions,id';
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

        $role = $user->role ? $user->role->nom : null;

        if ($user->isStagiaire()) {
            $user->sujets()->detach();
        }

        $user->delete();

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