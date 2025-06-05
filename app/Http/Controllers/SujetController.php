<?php

namespace App\Http\Controllers;

use App\Models\Groupe;
use App\Models\Promotion;
use App\Models\Sujet;
use App\Models\User;
use Illuminate\Http\Request;

class SujetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sujets=Sujet::with(['promotion','groupe'])->orderByDesc('created_at')->get();
        $promotions=Promotion::where('status','active')->get();
        $groupes= Groupe::all();
        $stagiaires = User::where('role_id',3)->get();
        return view('sujets.index',compact('sujets','promotions','groupes','stagiaires'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated=$request->validate([
            'titre' => 'required|string|max:225', 
            'description' => 'required|string',
            'promotion_id' => 'required|exists:promotions,id',
            'groupe_id' => 'required|exists:groupes,id'
        ]);
        //verifie si la pomotion est bien active
        $promotion=Promotion::find($validated['promotion_id']);
        if($promotion->status !== 'active'){
            return redirect()->route('sujets.index')->with('error','Impossible d\'associer à une promotion archivée.');
        }

        Sujet::create($validated);
        return redirect()->route('sujets.index')->with('success','Sujet ajouté avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sujet $sujet)
    {
        $validated=$request->validate([
             'titre' => 'required|string|max:225',
            'description' => 'required|string',
            'promotion_id' => 'required|exists:promotions,id',
            'groupe_id' => 'required|exists:groupes,id' 
        ]);

        $promotion = Promotion::find($validated['promotion_id']);
        if ($promotion->status !== 'active') {
            return redirect()->route('sujets.index')->with('error', 'Impossible d\'associer à une promotion archivée.');
        }
        $sujet->update($validated);
        return redirect()->route('sujets.index')->with('success','Sujet modifié avec succès.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sujet $sujet)
    {
        //on va utilise la relation dans sujet avec User 
        if($sujet->stagiaires()->count()> 0)
        {
            return redirect()->route('sujets.index')->with('error','Suppression impossible : des stagiaires sont déjà inscrits à ce sujet.');
        }
        $sujet->delete();
        return redirect()->route('sujets.index')->with('success','Sujet supprimé avec succès.');
    }

    //inscrire un stagire a un sujet 

    public function inscrire(Request $request, Sujet $sujet)
    {
        $request->validate([
            'stagiaire_id'=> 'required|exists:users,id'
        ]);

        //on evite la double inscription 
        //on prend tous les stagiaires qui ont un sujet, on fait un filtre pour voire si 
        //c'est le stagiaire qu'on a 
        //le exists() retourne true et avec ! la requete on renvoie false 
        //donc si on  vérifie si le stagiaire n'est pas déjà lié au sujet.
        //S'il ne l’est pas encore, il l’attache (l’associe) au sujet.
        
        if(!$sujet->stagiaires()->where('intern_id',$request->stagiaire_id)->exists()){
            $sujet->stagiaires()->attach($request->stagiaire_id);
        }
        return redirect()->route('sujets.index')->with('success','Stagiaire inscrit au sujet avec succès.');
    }

    //desinscrire un stagiaire a un sujet 
    public function desinscrire(Sujet $sujet, $stagiaire_id)
    {
        $sujet->stagiaires()->detach($stagiaire_id);
        return redirect()->route('sujets.index')->with('success','Stagiaire désinscrit avec succès.');

    }
}
