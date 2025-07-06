<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Log; // N'oubliez pas d'importer votre modèle Log
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Pour récupérer l'utilisateur connecté
use Illuminate\Validation\ValidationException; // Pour capturer les exceptions de validation

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $promotions = Promotion::orderByDesc('created_at')->get();

        // Enregistrement du log : Consultation de la liste des promotions
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_promotions_list',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a consulté la liste des promotions. Nombre de promotions: {$promotions->count()}.",
            'object_snapshot' => [
                'promotions_count' => $promotions->count(),
            ],
        ]);

        return view('promotions.index', compact('promotions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté

        // Enregistrement du log : Accès au formulaire de création de promotion
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_promotion_create_form',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a accédé au formulaire de création d'une nouvelle promotion.",
            'object_snapshot' => [],
        ]);

        // Si vous avez un formulaire de création distinct, retournez-le ici
        // return view('promotions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $newPromotion = null; // Déclarer pour la portée du try/catch

        try {
            $validated = $request->validate([
                'titre' => 'required|string|max:225',
            ]);

            // Archive toutes les autres promotions actives
            $activePromotionsBeforeUpdate = Promotion::where('status', 'active')->get();
            $archivedCount = 0;
            if ($activePromotionsBeforeUpdate->isNotEmpty()) {
                $archivedCount = Promotion::where('status', 'active')->update(['status' => 'archive']);
            }

            // Crée la nouvelle promotion
            $newPromotion = Promotion::create([
                'titre' => $validated['titre'],
                'status' => 'active',
            ]);

            // Enregistrement du log : Création de promotion réussie
            Log::create([
                'user_id' => $user->id,
                'action' => 'promotion_created_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a créé la promotion '{$newPromotion->titre}' (ID: {$newPromotion->id}). {$archivedCount} promotions précédentes ont été archivées.",
                'object_snapshot' => $newPromotion->toArray(),
            ]);

            return redirect()->route('promotions.index')->with('success', 'Promotion créée avec succès !');

        } catch (ValidationException $e) {
            // Enregistrement du log : Échec de validation lors de la création de promotion
            Log::create([
                'user_id' => $user->id,
                'action' => 'promotion_create_validation_failed',
                'log_message' => "Échec de validation lors de la tentative de création d'une promotion par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'input' => $request->all(),
                    'errors' => $e->errors(),
                ],
            ]);
            return back()->withInput()->withErrors($e->errors())->with('error', 'Veuillez corriger les erreurs dans le formulaire.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la création de promotion
            Log::create([
                'user_id' => $user->id,
                'action' => 'promotion_create_critical_error',
                'log_message' => "Erreur critique inattendue lors de la création d'une promotion par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'input' => $request->all(),
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création de la promotion : ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promotion $promotion)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté

        // Enregistrement du log : Accès au formulaire d'édition de promotion
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_promotion_edit_form',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a accédé au formulaire d'édition de la promotion '{$promotion->titre}' (ID: {$promotion->id}).",
            'object_snapshot' => $promotion->toArray(),
        ]);

        return view('promotions.edit', compact('promotion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promotion $promotion)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $oldPromotionData = $promotion->toArray(); // Capture l'état avant la mise à jour

        try {
            $validated = $request->validate([
                'titre' => 'required|string|max:225',
                'status' => 'required|in:active,archive'
            ]);

            $archivedCount = 0;
            // Si on veut activer la promotion, on archive les autres promotions actives
            if ($validated['status'] === 'active') {
                $archivedCount = Promotion::where('status', 'active')
                                          ->where('id', '!=', $promotion->id) 
                                          ->update(['status' => 'archive']);
            }

            $promotion->update($validated);

            // Enregistrement du log : Mise à jour de promotion réussie
            Log::create([
                'user_id' => $user->id,
                'action' => 'promotion_updated_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a mis à jour la promotion '{$promotion->titre}' (ID: {$promotion->id}). " . ($archivedCount > 0 ? "{$archivedCount} autres promotions ont été archivées." : ""),
                'object_snapshot' => [
                    'promotion_id' => $promotion->id,
                    'old_data' => $oldPromotionData,
                    'new_data' => $promotion->toArray(),
                    'other_promotions_archived_count' => $archivedCount,
                ],
            ]);

            return redirect()->route('promotions.index')->with('success', 'Promotion modifiée avec succès !');

        } catch (ValidationException $e) {
            // Enregistrement du log : Échec de validation lors de la mise à jour de promotion
            Log::create([
                'user_id' => $user->id,
                'action' => 'promotion_update_validation_failed',
                'log_message' => "Échec de validation lors de la tentative de mise à jour de la promotion '{$promotion->titre}' (ID: {$promotion->id}) par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'promotion_id' => $promotion->id,
                    'input' => $request->all(),
                    'errors' => $e->errors(),
                ],
            ]);
            return back()->withInput()->withErrors($e->errors())->with('error', 'Veuillez corriger les erreurs dans le formulaire.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la mise à jour de promotion
            Log::create([
                'user_id' => $user->id,
                'action' => 'promotion_update_critical_error',
                'log_message' => "Erreur critique inattendue lors de la mise à jour de la promotion '{$promotion->titre}' (ID: {$promotion->id}) par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'promotion_id' => $promotion->id,
                    'input' => $request->all(),
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la modification de la promotion : ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $promotionData = $promotion->toArray(); // Capture les données avant suppression

        try {
            // Vérifier si la promotion a des utilisateurs (stagiaires) associés
            if ($promotion->users()->count() > 0) { // CHANGEMENT ICI : de stagiaires() à users()
                // Enregistrement du log : Tentative de suppression de promotion avec utilisateurs
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'promotion_delete_failed_has_users', // Mise à jour de l'action pour le log
                    'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a tenté de supprimer la promotion '{$promotionData['titre']}' (ID: {$promotionData['id']}), mais elle contient des utilisateurs (stagiaires).",
                    'object_snapshot' => $promotionData,
                ]);
                return back()->with('error', 'Impossible de supprimer cette promotion car elle contient des stagiaires. Veuillez d\'abord réaffecter ou supprimer les stagiaires.');
            }

            $promotion->delete();

            // Enregistrement du log : Suppression de promotion réussie
            Log::create([
                'user_id' => $user->id,
                'action' => 'promotion_deleted_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a supprimé la promotion '{$promotionData['titre']}' (ID: {$promotionData['id']}).",
                'object_snapshot' => $promotionData,
            ]);

            return redirect()->route('promotions.index')->with('success', 'Promotion supprimée avec succès !');

        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la suppression de promotion
            Log::create([
                'user_id' => $user->id,
                'action' => 'promotion_delete_critical_error',
                'log_message' => "Erreur critique inattendue lors de la suppression de la promotion '{$promotionData['titre']}' (ID: {$promotionData['id']}) par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'promotion_id' => $promotionData['id'],
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->with('error', 'Une erreur est survenue lors de la suppression de la promotion : ' . $e->getMessage());
        }
    }
}
