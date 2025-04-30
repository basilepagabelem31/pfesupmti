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
        $admins= User::all();
        $roles=Role::all();//recupere les roles
        $statuts=Statut::all();//recupere les statuts
        $villes=Ville::all();
        $pays=Pays::all();
        return view('admin.index',compact('admins','roles','statuts','pays','villes'));
    }

    public function store(Request $request)
    {
        $request->validate([
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
            "statut_id" =>"required|exists:statuts,id"
        ]);

        $admin= User::create([
            "nom" =>$request->nom ,
            "prenom" =>$request->prenom ,
            "password" =>$request->password ,
            "email" =>$request->email ,
            "telephone" =>$request->telephone ,
            "cin" =>$request->cin ,
            "adresse" =>$request->adresse ,
            "pays_id" =>$request->pays_id ,
            "ville_id" =>$request->ville_id ,
            "role_id" =>$request->role_id ,
            "statut_id" =>$request->statut_id         
        ]);
        return redirect()->route('admin.index')->with('success', 'Admin a été bien creer.');

       
    }

    public function edit($id)
    {
        $editAdmin =User::findorFail($id);
        $admins= User::all();
        $roles=Role::all();
        $pays=Pays::all();
        $villes=Ville::all();
        $statuts=Statut::all();
        return view("admin.index",compact(['editAdmin','admins','roles','pays','villes','statuts']));
        
    }

    public function update(Request $request, $id){
       
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
            "statut_id" =>"required|exists:statuts,id"

        ]);
        
        $admin = User::findOrFail($id);
    
        $admin->update($data);
    
        return redirect()->route('admin.index')->with('success', 'Admin mis à jour.');
    }

    public function delete($id)
    {
        $admin=User::findorFail($id);
        $admin->delete();
        return redirect()->route('admin.index')->with('success', 'Admin supprimer avec succès.');

      
    }

}
