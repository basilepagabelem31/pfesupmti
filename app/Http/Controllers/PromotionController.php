<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promotions=Promotion::orderByDesc('created_at')->get();
        return view('promotions.index',compact('promotions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $validated= $request->validate([
        'titre' => 'required|string|max:225',
       ]);
       //archive toutes les autres promotions :oncherche les status active et on les modifie pour 
       //qu'elles soient tous archive 
       Promotion::where('status','active')->update(['status'=> 'archive']);
       //apres on va creer la nouvelle promotion 
       Promotion::create([
        'titre'=> $validated['titre'],
        'status'=> 'active',
       ]);

       return redirect()->route('promotions.index')->with('success','Promotion créer avec succès !');
    }

   

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promotion $promotion)
    {
       return view('promotions.edit',compact('promotion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promotion $promotion)
    {
        $validated= $request->validate([
            'titre'=> 'required|string|max:225',
            'status'=> 'required|in:active,archive'//le in :active,archive signifie que le champ peut prendre que ses deux valeurs 
        ]);

        //si on veut active , on archive les autres promotions 
        if($validated['status']==='active'){
            Promotion::where('status','active')->where('id','!=','$promotion->id')->update(['status'=>'archive']);
        }
        $promotion->update($validated);
        return redirect()->route('promotions.index')->with('success','Promotion modifier avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return redirect()->route('promotions.index')->with('success','Promotion supprime avec succès !');
    }
}
