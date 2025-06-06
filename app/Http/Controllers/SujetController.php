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
        $sujets=Sujet::with(['promotion','groupe', 'stagiaires'])->orderByDesc('created_at')->get(); // Ajout de 'stagiaires' pour eager loading
        $promotions=Promotion::where('status','active')->get();
        $groupes= Groupe::all();
        // Pour les stagiaires, vous pouvez récupérer ceux qui ont le rôle 'Stagiaire'
        $stagiaires = User::whereHas('role', function ($query) {
            $query->where('nom', 'Stagiaire'); // Utilisez le nom du rôle
        })->get();

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

        // Correction ici: utilisez 'users.id' ou simplement 'id' pour filtrer le stagiaire
        // ou utilisez syncWithoutDetaching pour éviter les doublons automatiquement.
        // La méthode syncWithoutDetaching est la plus simple pour éviter les doublons lors de l'attachement.
        // Elle va attacher l'ID s'il n'est pas déjà attaché, et ne rien faire s'il l'est.
        $sujet->stagiaires()->syncWithoutDetaching([$request->stagiaire_id]);

        return redirect()->route('sujets.index')->with('success','Stagiaire inscrit au sujet avec succès.');

        /*
        // Si vous voulez un message d'erreur spécifique en cas de double inscription :
        if(!$sujet->stagiaires()->where('users.id', $request->stagiaire_id)->exists()){
            $sujet->stagiaires()->attach($request->stagiaire_id);
            return redirect()->route('sujets.index')->with('success','Stagiaire inscrit au sujet avec succès.');
        } else {
            return redirect()->route('sujets.index')->with('error','Ce stagiaire est déjà inscrit à ce sujet.');
        }
        */
    }

    //desinscrire un stagiaire a un sujet
    public function desinscrire(Sujet $sujet, $stagiaire_id)
    {
        $sujet->stagiaires()->detach($stagiaire_id);
        return redirect()->route('sujets.index')->with('success','Stagiaire désinscrit avec succès.');

    }
}