<?php

namespace App\Http\Controllers;

use App\Models\DemandeCoequipier;
use App\Models\User; 
use App\Models\Coequipier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log; 
use App\helper\LogHelper; 

class DemandeCoequipierController extends Controller
{
    /**
     * Affiche les demandes de coéquipiers envoyées et reçues par l'utilisateur connecté.
     * Adapte l'affichage et les données selon le rôle de l'utilisateur.
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Pas besoin de loguer l'affichage de la page de gestion des demandes.

        $demandesEnvoyees = collect();
        $demandesRecues = collect();
        $stagiairesForNewRequest = collect();
        $allDemandes = collect();

        if ($user->isStagiaire()) {
            $demandesEnvoyees = $user->demandesEnvoyees()->with('receveur')->get();
            $demandesRecues = $user->demandesRecues()->with('demandeur')->get();

            $stagiairesForNewRequest = User::where('id', '!=', Auth::id())
                                           ->whereHas('role', function ($query) {
                                               $query->where('nom', 'Stagiaire');
                                           })
                                           ->get();

            return view('demande_coequipiers.index', compact('demandesEnvoyees', 'demandesRecues', 'stagiairesForNewRequest'));

        } elseif ($user->isSuperviseur() || $user->isAdministrateur()) {
            $allDemandes = DemandeCoequipier::with(['demandeur', 'receveur'])
                                             ->orderBy('date_demande', 'desc')
                                             ->get();

            return view('demande_coequipiers.index', compact('allDemandes'));
        }

        return back()->with('error', 'Accès non autorisé à cette page.');
    }

    /**
     * Affiche le formulaire pour envoyer une demande de coéquipier.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Pas besoin de loguer l'affichage du formulaire.
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
        if (!Auth::user()->isStagiaire()) {
            return back()->with('error', 'Seuls les stagiaires peuvent envoyer des demandes de coéquipier.');
        }

        $request->validate([
            'id_stagiaire_receveur' => 'required|exists:users,id',
        ]);

        $demandeurId = Auth::id();
        $receveurId = $request->input('id_stagiaire_receveur');
        $receveur = User::find($receveurId); // Récupérer le modèle User du receveur pour les détails

        if ($demandeurId == $receveurId) {
            return back()->with('error', 'Vous ne pouvez pas vous envoyer une demande à vous-même.');
        }

        $stagiaire1 = min($demandeurId, $receveurId);
        $stagiaire2 = max($demandeurId, $receveurId);

        $areAlreadyTeammates = Coequipier::where(function($query) use ($stagiaire1, $stagiaire2) {
            $query->where('id_stagiaire_1', $stagiaire1)
                  ->where('id_stagiaire_2', $stagiaire2);
        })->first();

        if ($areAlreadyTeammates) {
            return back()->with('info', 'Vous êtes déjà coéquipier avec cet utilisateur.');
        }

        $existingPendingRequestBetweenThem = DemandeCoequipier::where(function ($query) use ($demandeurId, $receveurId) {
            $query->where('id_stagiaire_demandeur', $demandeurId)
                  ->where('id_stagiaire_receveur', $receveurId);
        })->orWhere(function ($query) use ($demandeurId, $receveurId) {
            $query->where('id_stagiaire_demandeur', $receveurId)
                  ->where('id_stagiaire_receveur', $demandeurId);
        })
        ->where('statut_demande', 'en_attente')
        ->first();

        if ($existingPendingRequestBetweenThem) {
            return back()->with('info', 'Une demande est déjà en attente entre vous et cet utilisateur.');
        }

        $receveurHasPendingRequest = DemandeCoequipier::where('id_stagiaire_receveur', $receveurId)
                                                      ->where('statut_demande', 'en_attente')
                                                      ->where('id_stagiaire_demandeur', '!=', $demandeurId)
                                                      ->first();

        if ($receveurHasPendingRequest) {
            return back()->with('error', 'Cet utilisateur a déjà une demande de coéquipier en attente et ne peut pas en recevoir de nouvelle pour le moment.');
        }

        $newDemande = DemandeCoequipier::create([
            'id_stagiaire_demandeur' => $demandeurId,
            'id_stagiaire_receveur' => $receveurId,
            'date_demande' => now(),
            'statut_demande' => 'en_attente',
        ]);

        // Log de l'envoi de la demande
        LogHelper::logAction(
            'Envoi de demande de coéquipier',
            'Le stagiaire ' . Auth::user()->prenom . ' ' . Auth::user()->nom . ' (ID: ' . Auth::id() . ') a envoyé une demande de coéquipier au stagiaire ' . $receveur->prenom . ' ' . $receveur->nom . ' (ID: ' . $receveurId . ').',
            Auth::id()
        );

        return redirect()->route('demande_coequipiers.index')->with('success', 'Demande de coéquipier envoyée avec succès !');
    }

    /**
     * Accepte une demande de coéquipier.
     * @param DemandeCoequipier $demande_coequipier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accept(DemandeCoequipier $demande_coequipier)
    {
        if (!Auth::user()->isStagiaire()) {
            return back()->with('error', 'Seuls les stagiaires peuvent accepter des demandes de coéquipier.');
        }
        if ($demande_coequipier->id_stagiaire_receveur !== Auth::id() || $demande_coequipier->statut_demande !== 'en_attente') {
            return back()->with('error', 'Action non autorisée ou demande déjà traitée.');
        }

        DB::transaction(function () use ($demande_coequipier) {
            $demande_coequipier->update(['statut_demande' => 'acceptée']);

            $stagiaire1Id = min($demande_coequipier->id_stagiaire_demandeur, $demande_coequipier->id_stagiaire_receveur);
            $stagiaire2Id = max($demande_coequipier->id_stagiaire_demandeur, $demande_coequipier->id_stagiaire_receveur);

            $existingCoequipier = Coequipier::where(function($query) use ($stagiaire1Id, $stagiaire2Id) {
                $query->where('id_stagiaire_1', $stagiaire1Id)
                      ->where('id_stagiaire_2', $stagiaire2Id);
            })->orWhere(function($query) use ($stagiaire1Id, $stagiaire2Id) {
                $query->where('id_stagiaire_1', $stagiaire2Id)
                      ->where('id_stagiaire_2', $stagiaire1Id);
            })->first();

            if (!$existingCoequipier) {
                Coequipier::create([
                    'id_stagiaire_1' => $stagiaire1Id,
                    'id_stagiaire_2' => $stagiaire2Id,
                    'date_association' => now()->toDateString(),
                ]);
            } else {
                Log::warning("Tentative d'ajouter un coéquipier déjà existant : " . $stagiaire1Id . ' et ' . $stagiaire2Id);
            }

            // Log de l'acceptation de la demande
            $demandeur = User::find($demande_coequipier->id_stagiaire_demandeur);
            $accepteur = Auth::user(); // Qui est le receveur de la demande

            LogHelper::logAction(
                'Acceptation de demande de coéquipier',
                'Le stagiaire ' . $accepteur->prenom . ' ' . $accepteur->nom . ' (ID: ' . $accepteur->id . ') a accepté la demande de coéquipier du stagiaire ' . $demandeur->prenom . ' ' . $demandeur->nom . ' (ID: ' . $demandeur->id_stagiaire_demandeur . ').',
                Auth::id()
            );
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
        if (!Auth::user()->isStagiaire()) {
            return back()->with('error', 'Seuls les stagiaires peuvent refuser des demandes de coéquipier.');
        }
        if ($demande_coequipier->id_stagiaire_receveur !== Auth::id() || $demande_coequipier->statut_demande !== 'en_attente') {
            return back()->with('error', 'Action non autorisée ou demande déjà traitée.');
        }

        $demande_coequipier->update(['statut_demande' => 'refusée']);

        // Log du refus de la demande
        $demandeur = User::find($demande_coequipier->id_stagiaire_demandeur);
        $refuseur = Auth::user(); // Qui est le receveur de la demande

        LogHelper::logAction(
            'Refus de demande de coéquipier',
            'Le stagiaire ' . $refuseur->prenom . ' ' . $refuseur->nom . ' (ID: ' . $refuseur->id . ') a refusé la demande de coéquipier du stagiaire ' . $demandeur->prenom . ' ' . $demandeur->nom . ' (ID: ' . $demandeur->id . ').',
            Auth::id()
        );

        return redirect()->route('demande_coequipiers.index')->with('success', 'Demande refusée.');
    }

    /**
     * Annule une demande de coéquipier (par le demandeur).
     * @param DemandeCoequipier $demande_coequipier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(DemandeCoequipier $demande_coequipier)
    {
        if (!Auth::user()->isStagiaire()) {
            return back()->with('error', 'Seuls les stagiaires peuvent annuler des demandes de coéquipier.');
        }
        if ($demande_coequipier->id_stagiaire_demandeur !== Auth::id() || $demande_coequipier->statut_demande !== 'en_attente') {
            return back()->with('error', 'Action non autorisée ou demande déjà traitée.');
        }

        // Récupérer les détails avant la suppression pour le log
        $annuleur = Auth::user();
        $receveur = User::find($demande_coequipier->id_stagiaire_receveur);

        $demande_coequipier->delete(); // Supprime la demande

        // Log de l'annulation de la demande
        LogHelper::logAction(
            'Annulation de demande de coéquipier',
            'Le stagiaire ' . $annuleur->prenom . ' ' . $annuleur->nom . ' (ID: ' . $annuleur->id . ') a annulé sa demande de coéquipier envoyée au stagiaire ' . $receveur->prenom . ' ' . $receveur->nom . ' (ID: ' . $receveur->id . ').',
            Auth::id()
        );

        return redirect()->route('demande_coequipiers.index')->with('success', 'Demande annulée.');
    }
}