<?php

namespace App\Http\Controllers;

use App\Models\DemandeCoequipier;
use App\Models\User; // Pour trouver les stagiaires
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Pour l'utilisateur connecté
use Illuminate\Support\Facades\DB; // Pour les transactions (optionnel mais recommandé)

class DemandeCoequipierController extends Controller
{
    /**
     * Affiche les demandes de coéquipiers envoyées et reçues par l'utilisateur connecté.
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Récupérer les demandes envoyées
        $demandesEnvoyees = $user->demandesEnvoyees()->with('receveur')->get();

        // Récupérer les demandes reçues
        $demandesRecues = $user->demandesRecues()->with('demandeur')->get();

        return view('demande_coequipiers.index', compact('demandesEnvoyees', 'demandesRecues'));
    }

    /**
     * Affiche le formulaire pour envoyer une demande de coéquipier.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Récupérer tous les utilisateurs qui ne sont pas l'utilisateur connecté
        // et qui sont des stagiaires (si vous avez un système de rôles)
        $stagiaires = User::where('id', '!=', Auth::id())
                          ->whereHas('role', function ($query) {
                              $query->where('nom', 'Stagiaire');
                          })
                          ->get();

        return view('demande_coequipiers.create', compact('stagiaires'));
    }

    /**
     * Envoie une nouvelle demande de coéquipier.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_stagiaire_receveur' => 'required|exists:users,id',
        ]);

        $demandeurId = Auth::id();
        $receveurId = $request->input('id_stagiaire_receveur');

        // Empêcher l'utilisateur de s'envoyer une demande à lui-même
        if ($demandeurId == $receveurId) {
            return back()->with('error', 'Vous ne pouvez pas vous envoyer une demande à vous-même.');
        }

        // Vérifier si une demande est déjà en cours ou acceptée entre ces deux utilisateurs
        $existingDemande = DemandeCoequipier::where(function ($query) use ($demandeurId, $receveurId) {
                                                $query->where('id_stagiaire_demandeur', $demandeurId)
                                                      ->where('id_stagiaire_receveur', $receveurId);
                                            })->orWhere(function ($query) use ($demandeurId, $receveurId) {
                                                $query->where('id_stagiaire_demandeur', $receveurId)
                                                      ->where('id_stagiaire_receveur', $demandeurId);
                                            })
                                            ->whereIn('statut_demande', ['en_attente', 'acceptée'])
                                            ->first();

        if ($existingDemande) {
            if ($existingDemande->statut_demande === 'en_attente') {
                return back()->with('info', 'Une demande est déjà en attente avec cet utilisateur.');
            } else {
                return back()->with('info', 'Vous êtes déjà coéquipier avec cet utilisateur.');
            }
        }

        DemandeCoequipier::create([
            'id_stagiaire_demandeur' => $demandeurId,
            'id_stagiaire_receveur' => $receveurId,
            'date_demande' => now(), // Utilisez la date/heure actuelle
            'statut_demande' => 'en_attente', // Par défaut
        ]);

        return redirect()->route('demande_coequipiers.index')->with('success', 'Demande de coéquipier envoyée avec succès !');
    }

    /**
     * Accepte une demande de coéquipier.
     * @param DemandeCoequipier $demande_coequipier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accept(DemandeCoequipier $demande_coequipier)
    {
        // S'assurer que l'utilisateur connecté est bien le receveur de la demande et que la demande est en attente
        if ($demande_coequipier->id_stagiaire_receveur !== Auth::id() || $demande_coequipier->statut_demande !== 'en_attente') {
            return back()->with('error', 'Action non autorisée ou demande déjà traitée.');
        }

        DB::transaction(function () use ($demande_coequipier) {
            // Mettre à jour le statut de la demande
            $demande_coequipier->update(['statut_demande' => 'acceptée']);

            // Ajouter l'entrée dans la table 'coequipiers'
            // Assurez-vous que l'ordre des IDs est cohérent si vous avez une logique spécifique,
            // sinon, l'ordre n'a pas d'importance pour la clé primaire composite (id_stagiaire_1, id_stagiaire_2)
            // (mais l'unicité est basée sur l'ordre des colonnes dans la clé primaire).
            // Pour être sûr d'éviter les doublons (1,2) et (2,1), on peut normaliser l'ordre.
            $stagiaire1Id = min($demande_coequipier->id_stagiaire_demandeur, $demande_coequipier->id_stagiaire_receveur);
            $stagiaire2Id = max($demande_coequipier->id_stagiaire_demandeur, $demande_coequipier->id_stagiaire_receveur);

            Auth::user()->coequipiersAsStagiaire1()->attach($stagiaire2Id, ['date_association' => now()->toDateString()]);

            // Ou si vous utilisez le modèle Coequipier directement:
            /*
            Coequipier::create([
                'id_stagiaire_1' => $stagiaire1Id,
                'id_stagiaire_2' => $stagiaire2Id,
                'date_association' => now()->toDateString(),
            ]);
            */
        });

        return redirect()->route('demande_coequipiers.index')->with('success', 'Demande acceptée ! Vous êtes maintenant coéquipiers.');
    }

    /**
     * Refuse une demande de coéquipier.
     * @param DemandeCoequipier $demande_coequipier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refuse(DemandeCoequipier $demande_coequipier)
    {
        // S'assurer que l'utilisateur connecté est bien le receveur de la demande et que la demande est en attente
        if ($demande_coequipier->id_stagiaire_receveur !== Auth::id() || $demande_coequipier->statut_demande !== 'en_attente') {
            return back()->with('error', 'Action non autorisée ou demande déjà traitée.');
        }

        $demande_coequipier->update(['statut_demande' => 'refusée']);

        return redirect()->route('demande_coequipiers.index')->with('success', 'Demande refusée.');
    }

    /**
     * Annule une demande de coéquipier (par le demandeur).
     * @param DemandeCoequipier $demande_coequipier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(DemandeCoequipier $demande_coequipier)
    {
        // S'assurer que l'utilisateur connecté est bien le demandeur et que la demande est en attente
        if ($demande_coequipier->id_stagiaire_demandeur !== Auth::id() || $demande_coequipier->statut_demande !== 'en_attente') {
            return back()->with('error', 'Action non autorisée ou demande déjà traitée.');
        }

        $demande_coequipier->delete(); // Supprime la demande

        return redirect()->route('demande_coequipiers.index')->with('success', 'Demande annulée.');
    }
}