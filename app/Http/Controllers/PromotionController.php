<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\helper\LogHelper; 

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
        // Cette méthode n'a pas besoin de log car elle affiche un formulaire
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:225',
        ]);

        $user = Auth::user();

        // Archive toutes les autres promotions actives
        $oldActivePromotions = Promotion::where('status','active')->get();
        Promotion::where('status','active')->update(['status'=> 'archive']);

        if ($oldActivePromotions->isNotEmpty()) {
            $archivedTitles = $oldActivePromotions->pluck('titre')->implode(', ');
            LogHelper::logAction(
                'Archivage de promotions',
                'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a archivé les promotions actives existantes : ' . $archivedTitles . ', avant de créer une nouvelle promotion active.',
                $user->id
            );
        }

        // Crée la nouvelle promotion
        $promotion = Promotion::create([
            'titre'=> $validated['titre'],
            'status'=> 'active',
        ]);

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA CREATION
        LogHelper::logAction(
            'Création de promotion',
            'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a créé la promotion "' . $promotion->titre . '" (ID: ' . $promotion->id . ') avec le statut "active".',
            $user->id
        );

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
        $validated = $request->validate([
            'titre'=> 'required|string|max:225',
            'status'=> 'required|in:active,archive'
        ]);

        $user = Auth::user();
        $oldPromotionData = $promotion->toArray(); // Capture les données avant la mise à jour

        // Si la promotion est définie sur 'active', archive les autres promotions actives
        if ($validated['status'] === 'active') {
            $oldActivePromotions = Promotion::where('status','active')->where('id','!=',$promotion->id)->get();
            Promotion::where('status','active')->where('id','!=',$promotion->id)->update(['status'=>'archive']);

            if ($oldActivePromotions->isNotEmpty()) {
                $archivedTitles = $oldActivePromotions->pluck('titre')->implode(', ');
                LogHelper::logAction(
                    'Archivage de promotions',
                    'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a archivé les promotions existantes : ' . $archivedTitles . ', car la promotion "' . $promotion->titre . '" (ID: ' . $promotion->id . ') a été définie comme active.',
                    $user->id
                );
            }
        }

        $promotion->update($validated);

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA MODIFICATION
        $changes = [];
        foreach ($validated as $key => $value) {
            if (isset($oldPromotionData[$key]) && $oldPromotionData[$key] != $value) {
                $changes[] = ucfirst(str_replace('_', ' ', $key)) . ": '" . $oldPromotionData[$key] . "' -> '" . $value . "'";
            }
        }

        $message = 'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a modifié la promotion "' . $promotion->titre . '" (ID: ' . $promotion->id . '). ';
        if (!empty($changes)) {
            $message .= 'Changements: ' . implode(', ', $changes) . '.';
        } else {
            $message .= 'Aucun changement significatif détecté.';
        }

        LogHelper::logAction(
            'Modification de promotion',
            $message,
            $user->id
        );

        return redirect()->route('promotions.index')->with('success','Promotion modifier avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        $user = Auth::user();

        // Capture les informations de la promotion avant la suppression
        $deletedPromotionTitle = $promotion->titre;
        $deletedPromotionId = $promotion->id;

        $promotion->delete();

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA SUPPRESSION
        LogHelper::logAction(
            'Suppression de promotion',
            'Le ' . $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ') a supprimé la promotion "' . $deletedPromotionTitle . '" (ID: ' . $deletedPromotionId . ').',
            $user->id
        );

        return redirect()->route('promotions.index')->with('success','Promotion supprimée avec succès !');
    }
}