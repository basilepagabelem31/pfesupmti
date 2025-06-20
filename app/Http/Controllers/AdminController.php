<?php

namespace App\Http\Controllers;

use App\helper\LogHelper;
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
use Illuminate\Support\Facades\Auth; // Importez le facade Auth
use Illuminate\Support\Str; 

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
        
        $groupes = Groupe::all();
        $promotions = Promotion::where('status', 'active')->get();
        $sujets = Sujet::with('promotion')->get();

        return view("admin.create", compact('roles', 'statuts', 'pays', 'paysVilles', 'stagiaireId', 'groupes', 'promotions', 'sujets'));
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
            "nom"            => "required|string",
            "prenom"         => "required|string",
            "password"       => "required",
            "email"          => "required|email|unique:users",
            "telephone"      => "required|string",
            "cin"            => "required|unique:users|string",
            "adresse"        => "required|string",
            "pays_id"        => "required|exists:pays,id",
            "ville_id"       => "required|exists:villes,id",
            "role_id"        => "required|exists:roles,id",
            "statut_id"      => "required|exists:statuts,id",
            'universite'     => 'nullable|string|max:255',
            'faculte'        => 'nullable|string|max:255',
            'titre_formation'=> 'nullable|string|max:255',
            'groupe_id'      => 'nullable|exists:groupes,id',
            'promotion_id'   => 'nullable|exists:promotions,id',
            'sujet_ids'      => 'nullable|array',
            'sujet_ids.*'    => 'exists:sujets,id',
        ];

        if ($request->input('role_id') == $stagiaireRoleId) {
            // champs additionnels obligatoires pour un stagiaire
            $rules['universite']     = 'required|string|max:255';
            $rules['faculte']        = 'required|string|max:255';
            $rules['titre_formation']= 'required|string|max:255';
            $rules['groupe_id']      = 'nullable|exists:groupes,id';
            $rules['promotion_id']   = 'nullable|exists:promotions,id';
        }

        $validated = $request->validate($rules);
        $validated['password'] = Hash::make($validated['password']);

        // génération du code unique
        do {
            $code = Str::upper(Str::random(6));
        } while (User::where('code', $code)->exists());
        $validated['code'] = $code;

        // détacher les sujet_ids pour la création
        $sujetIds = $validated['sujet_ids'] ?? [];
        unset($validated['sujet_ids']);

        // *** Création d'un seul utilisateur ***
        $user = User::create($validated);

        // Log de la création
        $creator = Auth::user();
        $roleNom = $user->role ? $user->role->nom : 'N/A';
        LogHelper::logAction(
            'Création de compte utilisateur',
            'Le ' . $creator->role->nom . ' ' . $creator->nom . ' ' . $creator->prenom .
            ' (ID: ' . $creator->id . ') a créé le compte de ' . $roleNom .
            ' : ' . $user->prenom . ' ' . $user->nom .
            ' (ID: ' . $user->id . ', Email: ' . $user->email . ').',
            Auth::id()
        );

        // attacher les sujets si c'est un stagiaire
        if ($user->role_id === $stagiaireRoleId && !empty($sujetIds)) {
            $user->sujets()->attach($sujetIds);
        }

        // redirection selon le rôle
        $role = $user->role ? $user->role->nom : null;
        if (in_array($role, ['Administrateur','Superviseur'])) {
            return redirect()->route('admin.index')->with('success', 'Admin/Superviseur créé avec succès.');
        } else {
            return redirect()->route('admin.users.stagiaires')->with('success', 'Stagiaire créé avec succès.');
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
        
        $groupes = Groupe::all();
        $promotions = Promotion::where('status', 'active')->get();
        $sujets = Sujet::with('promotion')->get();

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
                // Si l'utilisateur n'est pas un stagiaire, assurez-vous que ces champs ne sont pas traités
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
            'fichiersTeleverses', // Fichiers téléversés PAR ce stagiaire
            // Note: `fichiersRecus` n'est pas une relation explicite dans votre modèle User fourni,
            // mais `fichiersPossedes` (via `id_stagiaire`) couvre généralement les fichiers associés à ce stagiaire,
            // qu'ils soient téléversés par lui ou pour lui. Si vous avez besoin de distinguer les fichiers
            // téléversés par un superviseur POUR ce stagiaire, il faudrait une relation spécifique ou filtrer `fichiersPossedes`
            // par `id_superviseur_televerseur`. Pour l'instant, je m'appuie sur `fichiersPossedes` et `fichiersTeleverses`.
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