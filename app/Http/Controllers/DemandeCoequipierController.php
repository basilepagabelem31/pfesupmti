<?php

namespace App\Http\Controllers;

use App\Models\DemandeCoequipier;
use App\Models\User; // Pour trouver les stagiaires
use App\Models\Coequipier; // Importez le modèle Coequipier
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Pour l'utilisateur connecté
use Illuminate\Support\Facades\DB; // Pour les transactions
use Illuminate\Support\Facades\Log; // Pour le débogage

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

        // Collections par défaut, seront peuplées selon le rôle
        $demandesEnvoyees = collect();
        $demandesRecues = collect();
        $stagiairesForNewRequest = collect(); // Liste des stagiaires pour l'envoi de demandes (pour le rôle 'Stagiaire')
        $allDemandes = collect(); // Toutes les demandes (pour les rôles 'Superviseur' et 'Administrateur')

        if ($user->isStagiaire()) {
            // Pour un stagiaire, on affiche ses propres demandes envoyées et reçues
            $demandesEnvoyees = $user->demandesEnvoyees()->with('receveur')->get();
            $demandesRecues = $user->demandesRecues()->with('demandeur')->get();

            // On lui fournit également la liste des autres stagiaires pour qu'il puisse envoyer de nouvelles demandes
            $stagiairesForNewRequest = User::where('id', '!=', Auth::id())
                                           ->whereHas('role', function ($query) {
                                               $query->where('nom', 'Stagiaire'); // Assurez-vous que 'Stagiaire' est le nom de votre rôle
                                           })
                                           ->get();
            
            return view('demande_coequipiers.index', compact('demandesEnvoyees', 'demandesRecues', 'stagiairesForNewRequest'));

        } elseif ($user->isSuperviseur() || $user->isAdministrateur()) {
            // Pour un superviseur ou un administrateur, on affiche TOUTES les demandes du système
            // Ils n'envoient pas de demandes eux-mêmes, donc les listes envoyées/reçues individuelles ne sont pas pertinentes.
            $allDemandes = DemandeCoequipier::with(['demandeur', 'receveur'])
                                             ->orderBy('date_demande', 'desc') // Optionnel: trier par date
                                             ->get();
            
            return view('demande_coequipiers.index', compact('allDemandes'));
        }

        // Cas par défaut ou utilisateur non géré spécifiquement (peut être redirigé ou afficher un message d'erreur)
        return back()->with('error', 'Accès non autorisé à cette page.');
    }

    /**
     * Affiche le formulaire pour envoyer une demande de coéquipier.
     * Cette méthode peut devenir obsolète si la création se fait via une modale sur la page d'index.
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
        // Seuls les stagiaires peuvent envoyer des demandes
        if (!Auth::user()->isStagiaire()) {
            return back()->with('error', 'Seuls les stagiaires peuvent envoyer des demandes de coéquipier.');
        }

        $request->validate([
            'id_stagiaire_receveur' => 'required|exists:users,id',
        ]);

        $demandeurId = Auth::id();
        $receveurId = $request->input('id_stagiaire_receveur');

        // Règle 1: Empêcher l'utilisateur de s'envoyer une demande à lui-même
        if ($demandeurId == $receveurId) {
            return back()->with('error', 'Vous ne pouvez pas vous envoyer une demande à vous-même.');
        }

        // Règle 2: Vérifier si les deux utilisateurs sont déjà coéquipiers
        // On normalise l'ordre des IDs pour la recherche dans la table 'coequipiers'
        $stagiaire1 = min($demandeurId, $receveurId);
        $stagiaire2 = max($demandeurId, $receveurId);

        $areAlreadyTeammates = Coequipier::where(function($query) use ($stagiaire1, $stagiaire2) {
            $query->where('id_stagiaire_1', $stagiaire1)
                  ->where('id_stagiaire_2', $stagiaire2);
        })->first();

        if ($areAlreadyTeammates) {
            return back()->with('info', 'Vous êtes déjà coéquipier avec cet utilisateur.');
        }

        // Règle 3: Vérifier si une demande est déjà en attente entre ces deux utilisateurs spécifiques (demandeur et receveur)
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
        
        // Règle 4: Limitation: un stagiaire ne peut recevoir qu’une seule demande simultanée de coéquipier.
        // Vérifie si le receveur a déjà une demande 'en_attente' de *n'importe quel* autre stagiaire
        $receveurHasPendingRequest = DemandeCoequipier::where('id_stagiaire_receveur', $receveurId)
                                                      ->where('statut_demande', 'en_attente')
                                                      ->where('id_stagiaire_demandeur', '!=', $demandeurId) // Ne pas compter la demande que celui-ci pourrait avoir envoyée et annulée
                                                      ->first();
        
        if ($receveurHasPendingRequest) {
            return back()->with('error', 'Cet utilisateur a déjà une demande de coéquipier en attente et ne peut pas en recevoir de nouvelle pour le moment.');
        }


        // Si toutes les validations passent, créer la demande
        $newDemande = DemandeCoequipier::create([
            'id_stagiaire_demandeur' => $demandeurId,
            'id_stagiaire_receveur' => $receveurId,
            'date_demande' => now(),
            'statut_demande' => 'en_attente',
        ]);

        // LOG DE DÉBOGAGE : Vérifiez l'ID de la demande juste après sa création
        Log::info('Demande créée avec ID: ' . $newDemande->id . ' de ' . $demandeurId . ' à ' . $receveurId);

        return redirect()->route('demande_coequipiers.index')->with('success', 'Demande de coéquipier envoyée avec succès !');
    }

    /**
     * Accepte une demande de coéquipier.
     * @param DemandeCoequipier $demande_coequipier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accept(DemandeCoequipier $demande_coequipier)
    {
        // Seuls les stagiaires peuvent accepter des demandes
        if (!Auth::user()->isStagiaire()) {
            return back()->with('error', 'Seuls les stagiaires peuvent accepter des demandes de coéquipier.');
        }
        // S'assurer que l'utilisateur connecté est bien le receveur de la demande et que la demande est en attente
        if ($demande_coequipier->id_stagiaire_receveur !== Auth::id() || $demande_coequipier->statut_demande !== 'en_attente') {
            return back()->with('error', 'Action non autorisée ou demande déjà traitée.');
        }

        DB::transaction(function () use ($demande_coequipier) {
            // Mettre à jour le statut de la demande
            $demande_coequipier->update(['statut_demande' => 'acceptée']);

            // Normaliser l'ordre des IDs pour la table 'coequipiers'
            $stagiaire1Id = min($demande_coequipier->id_stagiaire_demandeur, $demande_coequipier->id_stagiaire_receveur);
            $stagiaire2Id = max($demande_coequipier->id_stagiaire_demandeur, $demande_coequipier->id_stagiaire_receveur);

            // Vérifier si l'association existe déjà pour éviter les doublons
            $existingCoequipier = Coequipier::where(function($query) use ($stagiaire1Id, $stagiaire2Id) {
                $query->where('id_stagiaire_1', $stagiaire1Id)
                      ->where('id_stagiaire_2', $stagiaire2Id);
            })->orWhere(function($query) use ($stagiaire1Id, $stagiaire2Id) { // Vérifier l'ordre inverse aussi
                $query->where('id_stagiaire_1', $stagiaire2Id)
                      ->where('id_stagiaire_2', $stagiaire1Id);
            })->first();
            
            if (!$existingCoequipier) {
                // Ajouter l'entrée dans la table 'coequipiers'
                Coequipier::create([
                    'id_stagiaire_1' => $stagiaire1Id,
                    'id_stagiaire_2' => $stagiaire2Id,
                    'date_association' => now()->toDateString(),
                ]);
            } else {
                Log::warning("Tentative d'ajouter un coéquipier déjà existant : " . $stagiaire1Id . ' et ' . $stagiaire2Id);
            }

            // Ici, vous pourriez ajouter une logique pour mettre à jour les groupes/sujets des stagiaires
            // s'ils sont censés être synchronisés après l'acceptation d'une demande de coéquipier.
            // Par exemple:
            // $demandeur = User::find($demande_coequipier->id_stagiaire_demandeur);
            // $receveur = User::find($demande_coequipier->id_stagiaire_receveur);
            // if ($demandeur && $receveur && $demandeur->id_groupe !== $receveur->id_groupe) {
            //     // Décidez quel groupe prime ou comment les aligner
            //     $demandeur->update(['id_groupe' => $receveur->id_groupe]);
            // }
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
        // Seuls les stagiaires peuvent refuser des demandes
        if (!Auth::user()->isStagiaire()) {
            return back()->with('error', 'Seuls les stagiaires peuvent refuser des demandes de coéquipier.');
        }
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
        // Seuls les stagiaires peuvent annuler des demandes
        if (!Auth::user()->isStagiaire()) {
            return back()->with('error', 'Seuls les stagiaires peuvent annuler des demandes de coéquipier.');
        }
        // S'assurer que l'utilisateur connecté est bien le demandeur et que la demande est en attente
        if ($demande_coequipier->id_stagiaire_demandeur !== Auth::id() || $demande_coequipier->statut_demande !== 'en_attente') {
            return back()->with('error', 'Action non autorisée ou demande déjà traitée.');
        }

        $demande_coequipier->delete(); // Supprime la demande

        return redirect()->route('demande_coequipiers.index')->with('success', 'Demande annulée.');
    }
}
