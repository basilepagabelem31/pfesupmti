<?php

namespace App\Http\Controllers;

use App\Models\Pays;
use App\Models\Role;
use App\Models\Statut;
use App\Models\User;
use App\Models\Ville;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    //
    public function create()
    {
        return view("admin.create");
    }
    public function index(){
        //filtre les utilisateurs par role au lieu de charger tous les utilisateurs $admins= User::all();
        $admins= User::with(['pays','ville','role','statut'])
        ->whereHas('role',function($q){
            $q->whereIn('nom',['Administrateur','Superviseur']);
        })->paginate(10);
        $roles=Role::all();//recupere les roles
        $statuts=Statut::all();//recupere les statuts
        $pays=Pays::all();
        //charger pays et ville 
        $paysVilles=Pays::with([
            'villes' => fn($q) =>$q->select('id','nom','pays_id')->orderBy('nom')
        ])->orderBy('nom')->get(['id','nom']);
        $stagiaireId = Role::where('nom','Stagiaire')->value('id');
        return view('admin.index',compact('admins','roles','statuts','pays','paysVilles','stagiaireId'));
    }
        public function indexStagiaire()
        {
            // on ne récupère que les utilisateurs ayant le rôle "Stagiaire"
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
            'universite' => 'required_if:role_id,3',
            'faculte' => 'required_if:role_id,3',
            'titre_formation' => 'required_if:role_id,3',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $admin = User::create($validated);

        //redirection en fonction du role pour l'affichage 

        $role = $admin->role ? $admin->role->nom : null ;
        if ($role === 'Administrateur'|| $role === 'Superviseur'){
             return redirect()->route('admin.index')->with('success', 'Admin/Superviseur a été bien creer.');

        }else {
         return redirect()->route('admin.index_stagiaire')->with('success', 'Stagiaire a été bien creer.');

        }
        

       
    }

    public function edit($id)
    {
        $editAdmin =User::findorFail($id);
        $admins= User::with(['pays','ville','role','statut'])
        ->whereHas('role',function($q){
            $q->whereIn('nom',['Administrateur','Superviseur','Stagiaire']);
        })->paginate(10);
        $roles=Role::all();
        $pays=Pays::all();
        $villes=Ville::all();
        $statuts=Statut::all();

        return view("admin.index",compact(['editAdmin','admins','roles','pays','villes','statuts']));
        
    }

    public function update(Request $request, $id){

        $admin = User::findOrFail($id);

        $data=$request->validate([
            "nom"=> "required|string",
            "prenom" => "required|string",
            "password" => "nullable",
            "email" => "required|email|unique:users,email," . $id,
            "telephone" => "required|string",
            "cin" => "required|string|unique:users,cin," . $id,
            "adresse"=> "required|string",
            "pays_id" =>"required|exists:pays,id",
            "ville_id" =>"required|exists:villes,id",
            "role_id" => "required|exists:roles,id",
            "statut_id" =>"required|exists:statuts,id",
            'universite' => 'required_if:role_id,3',
            'faculte' => 'required_if:role_id,3',
            'titre_formation' => 'required_if:role_id,3',

        ]);
        
       if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
    
        $admin->update($data);

        $role = $admin->role ? $admin->role->nom : null ;
        if ($role === 'Administrateur'|| $role === 'Superviseur'){
             return redirect()->route('admin.index')->with('success', 'Admin/Superviseur a été bien creer.');

        }else {
         return redirect()->route('admin.index_stagiaire')->with('success', 'Stagiaire a été bien creer.');

        }
        
    }

    public function delete($id)
    {
        $admin=User::findorFail($id);
        $admin->delete();
        return redirect()->route('admin.index')->with('success', 'Admin supprimer avec succès.'); 
    }
   

    public function getStagiaireRoleId()
{
    // Si tu veux le premier id du rôle "Stagiaire"
    return Role::where('nom', 'Stagiaire')->value('id');
}
}
