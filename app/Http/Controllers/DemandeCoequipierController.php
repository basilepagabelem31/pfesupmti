<?php

namespace App\Http\Controllers;

use App\Models\DemandeCoequipier;
use App\Models\User; // Pour trouver les stagiaires
use App\Models\Coequipier; // Importez le modèle Coequipier
use App\Models\Log; // Importez le modèle Log personnalisé
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Pour l'utilisateur connecté
use Illuminate\Support\Facades\DB; // Pour les transactions
use Illuminate\Validation\ValidationException; // Pour les exceptions de validation

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

            // Enregistrement du log : Stagiaire consulte ses demandes de coéquipier
            Log::create([
                'user_id' => $user->id,
                'action' => 'view_teammate_requests_stagiaire',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a consulté ses demandes de coéquipier (envoyées: {$demandesEnvoyees->count()}, reçues: {$demandesRecues->count()}).",
                'object_snapshot' => [
                    'demandes_envoyees_count' => $demandesEnvoyees->count(),
                    'demandes_recues_count' => $demandesRecues->count(),
                ],
            ]);

            return view('demande_coequipiers.index', compact('demandesEnvoyees', 'demandesRecues', 'stagiairesForNewRequest'));

        } elseif ($user->isSuperviseur() || $user->isAdministrateur()) {
            // Pour un superviseur ou un administrateur, on affiche TOUTES les demandes du système
            $allDemandes = DemandeCoequipier::with(['demandeur', 'receveur'])
                                             ->orderBy('date_demande', 'desc') // Optionnel: trier par date
                                             ->get();

            // Enregistrement du log : Admin/Superviseur consulte toutes les demandes de coéquipier
            Log::create([
                'user_id' => $user->id,
                'action' => 'view_all_teammate_requests_admin_superviseur',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a consulté toutes les demandes de coéquipier ({$allDemandes->count()} au total).",
                'object_snapshot' => [
                    'all_demandes_count' => $allDemandes->count(),
                ],
            ]);

            return view('demande_coequipiers.index', compact('allDemandes'));
        }

        // Enregistrement du log : Accès non autorisé à la page des demandes de coéquipier
        Log::create([
            'user_id' => $user->id,
            'action' => 'unauthorized_access_teammate_requests',
            'log_message' => "Tentative d'accès non autorisée à la page des demandes de coéquipier par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
            'object_snapshot' => ['user_role' => $user->role->nom],
        ]);
        return back()->with('error', 'Accès non autorisé à cette page.');
    }

    /**
     * Affiche le formulaire pour envoyer une demande de coéquipier.
     * Cette méthode peut devenir obsolète si la création se fait via une modale sur la page d'index.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $user = Auth::user();

        // Enregistrement du log : Accès au formulaire de création de demande de coéquipier
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_create_teammate_request_form',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a accédé au formulaire d'envoi de demande de coéquipier.",
            'object_snapshot' => ['user_role' => $user->role->nom],
        ]);

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
        $user = Auth::user();
        $demandeurId = $user->id;
        $receveurId = $request->input('id_stagiaire_receveur');
        $receveur = null; // Sera rempli si l'ID est valide

        try {
            // Seuls les stagiaires peuvent envoyer des demandes
            if (!$user->isStagiaire()) {
                // Enregistrement du log : Tentative d'envoi de demande par non-stagiaire
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'teammate_request_store_unauthorized_role',
                    'log_message' => "Tentative d'envoi de demande de coéquipier non autorisée par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                    'object_snapshot' => ['user_role' => $user->role->nom],
                ]);
                return back()->with('error', 'Seuls les stagiaires peuvent envoyer des demandes de coéquipier.');
            }

            $validatedData = $request->validate([
                'id_stagiaire_receveur' => 'required|exists:users,id',
            ]);

            $receveur = User::find($receveurId); // Récupère l'objet receveur pour le log

            // Règle 1: Empêcher l'utilisateur de s'envoyer une demande à lui-même
            if ($demandeurId == $receveurId) {
                // Enregistrement du log : Tentative d'envoi de demande à soi-même
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'teammate_request_store_self_request',
                    'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a tenté de s'envoyer une demande de coéquipier à lui-même.",
                    'object_snapshot' => ['demandeur_id' => $demandeurId],
                ]);
                return back()->with('error', 'Vous ne pouvez pas vous envoyer une demande à vous-même.');
            }

            // Règle 2: Vérifier si les deux utilisateurs sont déjà coéquipiers
            $stagiaire1 = min($demandeurId, $receveurId);
            $stagiaire2 = max($demandeurId, $receveurId);

            $areAlreadyTeammates = Coequipier::where(function($query) use ($stagiaire1, $stagiaire2) {
                $query->where('id_stagiaire_1', $stagiaire1)
                      ->where('id_stagiaire_2', $stagiaire2);
            })->first();

            if ($areAlreadyTeammates) {
                // Enregistrement du log : Tentative d'envoi de demande à un coéquipier existant
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'teammate_request_store_already_teammates',
                    'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a tenté d'envoyer une demande à '{$receveur->nom} {$receveur->prenom}', mais ils sont déjà coéquipiers.",
                    'object_snapshot' => [
                        'demandeur_id' => $demandeurId,
                        'receveur_id' => $receveurId,
                    ],
                ]);
                return back()->with('info', 'Vous êtes déjà coéquipier avec cet utilisateur.');
            }

            // Règle 3: Vérifier si une demande est déjà en attente entre ces deux utilisateurs spécifiques
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
                // Enregistrement du log : Tentative d'envoi de demande, une demande est déjà en attente
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'teammate_request_store_already_pending',
                    'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a tenté d'envoyer une demande à '{$receveur->nom} {$receveur->prenom}', mais une demande est déjà en attente.",
                    'object_snapshot' => [
                        'demandeur_id' => $demandeurId,
                        'receveur_id' => $receveurId,
                        'existing_request_id' => $existingPendingRequestBetweenThem->id,
                    ],
                ]);
                return back()->with('info', 'Une demande est déjà en attente entre vous et cet utilisateur.');
            }

            // Règle 4: Limitation: un stagiaire ne peut recevoir qu’une seule demande simultanée de coéquipier.
            $receveurHasPendingRequest = DemandeCoequipier::where('id_stagiaire_receveur', $receveurId)
                                                          ->where('statut_demande', 'en_attente')
                                                          ->where('id_stagiaire_demandeur', '!=', $demandeurId)
                                                          ->first();
            
            if ($receveurHasPendingRequest) {
                // Enregistrement du log : Tentative d'envoi de demande, le receveur a déjà une demande en attente
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'teammate_request_store_receveur_busy',
                    'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a tenté d'envoyer une demande à '{$receveur->nom} {$receveur->prenom}', mais cet utilisateur a déjà une demande en attente.",
                    'object_snapshot' => [
                        'demandeur_id' => $demandeurId,
                        'receveur_id' => $receveurId,
                        'existing_request_id_receveur' => $receveurHasPendingRequest->id,
                    ],
                ]);
                return back()->with('error', 'Cet utilisateur a déjà une demande de coéquipier en attente et ne peut pas en recevoir de nouvelle pour le moment.');
            }

            // Si toutes les validations passent, créer la demande
            $newDemande = DemandeCoequipier::create([
                'id_stagiaire_demandeur' => $demandeurId,
                'id_stagiaire_receveur' => $receveurId,
                'date_demande' => now(),
                'statut_demande' => 'en_attente',
            ]);

            // Enregistrement du log : Demande de coéquipier envoyée avec succès
            Log::create([
                'user_id' => $user->id,
                'action' => 'teammate_request_sent_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a envoyé une demande de coéquipier à '{$receveur->nom} {$receveur->prenom}' (ID demande: {$newDemande->id}).",
                'object_snapshot' => $newDemande->toArray(),
            ]);

            return redirect()->route('demande_coequipiers.index')->with('success', 'Demande de coéquipier envoyée avec succès !');

        } catch (ValidationException $e) {
            // Enregistrement du log : Erreur de validation lors de l'envoi de demande
            Log::create([
                'user_id' => $user->id,
                'action' => 'teammate_request_store_validation_error',
                'log_message' => "Erreur de validation lors de l'envoi d'une demande de coéquipier par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'input' => $request->all(),
                    'errors' => $e->errors(),
                ],
            ]);
            return back()->withInput()->withErrors($e->errors())->with('error', 'Veuillez corriger les erreurs dans le formulaire.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de l'envoi de demande
            Log::create([
                'user_id' => $user->id,
                'action' => 'teammate_request_store_critical_error',
                'log_message' => "Erreur critique inattendue lors de l'envoi d'une demande de coéquipier par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'demandeur_id' => $demandeurId,
                    'receveur_id_attempted' => $receveurId,
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->withInput()->with('error', 'Une erreur est survenue lors de l\'envoi de la demande : ' . $e->getMessage());
        }
    }

    /**
     * Accepte une demande de coéquipier.
     * @param DemandeCoequipier $demande_coequipier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accept(DemandeCoequipier $demande_coequipier)
    {
        $user = Auth::user();
        $demandeur = $demande_coequipier->demandeur; // Pour les logs

        try {
            // Seuls les stagiaires peuvent accepter des demandes
            if (!$user->isStagiaire()) {
                // Enregistrement du log : Tentative d'acceptation de demande par non-stagiaire
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'teammate_request_accept_unauthorized_role',
                    'log_message' => "Tentative d'acceptation de demande de coéquipier non autorisée par '{$user->nom} {$user->prenom}' ({$user->role->nom}). Demande ID: {$demande_coequipier->id}.",
                    'object_snapshot' => $demande_coequipier->toArray(),
                ]);
                return back()->with('error', 'Seuls les stagiaires peuvent accepter des demandes de coéquipier.');
            }
            // S'assurer que l'utilisateur connecté est bien le receveur de la demande et que la demande est en attente
            if ($demande_coequipier->id_stagiaire_receveur !== $user->id || $demande_coequipier->statut_demande !== 'en_attente') {
                // Enregistrement du log : Tentative d'acceptation de demande non autorisée ou déjà traitée
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'teammate_request_accept_unauthorized_or_processed',
                    'log_message' => "Tentative d'acceptation d'une demande de coéquipier non autorisée ou déjà traitée par '{$user->nom} {$user->prenom}' ({$user->role->nom}). Demande ID: {$demande_coequipier->id}. Statut actuel: {$demande_coequipier->statut_demande}.",
                    'object_snapshot' => $demande_coequipier->toArray(),
                ]);
                return back()->with('error', 'Action non autorisée ou demande déjà traitée.');
            }

            DB::transaction(function () use ($demande_coequipier, $user, $demandeur) {
                // Mettre à jour le statut de la demande
                $demande_coequipier->update(['statut_demande' => 'acceptée']);

                // Normaliser l'ordre des IDs pour la table 'coequipiers'
                $stagiaire1Id = min($demande_coequipier->id_stagiaire_demandeur, $demande_coequipier->id_stagiaire_receveur);
                $stagiaire2Id = max($demande_coequipier->id_stagiaire_demandeur, $demande_coequipier->id_stagiaire_receveur);

                // Vérifier si l'association existe déjà pour éviter les doublons
                $existingCoequipier = Coequipier::where(function($query) use ($stagiaire1Id, $stagiaire2Id) {
                    $query->where('id_stagiaire_1', $stagiaire1Id)
                          ->where('id_stagiaire_2', $stagiaire2Id);
                })->first(); // Un seul where est suffisant si les IDs sont normalisés

                if (!$existingCoequipier) {
                    // Ajouter l'entrée dans la table 'coequipiers'
                    Coequipier::create([
                        'id_stagiaire_1' => $stagiaire1Id,
                        'id_stagiaire_2' => $stagiaire2Id,
                        'date_association' => now()->toDateString(),
                    ]);
                    // Enregistrement du log : Nouvelle association de coéquipiers créée
                    Log::create([
                        'user_id' => $user->id,
                        'action' => 'coequipier_association_created',
                        'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a accepté la demande de '{$demandeur->nom} {$demandeur->prenom}', créant une association de coéquipiers (IDs: {$stagiaire1Id}, {$stagiaire2Id}).",
                        'object_snapshot' => [
                            'demande_id' => $demande_coequipier->id,
                            'stagiaire1_id' => $stagiaire1Id,
                            'stagiaire2_id' => $stagiaire2Id,
                        ],
                    ]);
                } else {
                    // Enregistrement du log : Tentative d'ajouter un coéquipier déjà existant (avertissement)
                    Log::create([
                        'user_id' => $user->id,
                        'action' => 'coequipier_association_already_exists_warning',
                        'log_message' => "Avertissement: L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a accepté une demande (ID: {$demande_coequipier->id}), mais l'association de coéquipiers (IDs: {$stagiaire1Id}, {$stagiaire2Id}) existait déjà.",
                        'object_snapshot' => [
                            'demande_id' => $demande_coequipier->id,
                            'stagiaire1_id' => $stagiaire1Id,
                            'stagiaire2_id' => $stagiaire2Id,
                            'existing_coequipier_id' => $existingCoequipier->id,
                        ],
                    ]);
                }
            });

            // Enregistrement du log : Demande de coéquipier acceptée avec succès
            Log::create([
                'user_id' => $user->id,
                'action' => 'teammate_request_accepted_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a accepté la demande de coéquipier de '{$demandeur->nom} {$demandeur->prenom}' (ID demande: {$demande_coequipier->id}).",
                'object_snapshot' => $demande_coequipier->toArray(),
            ]);

            return redirect()->route('demande_coequipiers.index')->with('success', 'Demande acceptée ! Vous êtes maintenant coéquipiers.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur critique lors de l'acceptation de demande
            Log::create([
                'user_id' => $user->id,
                'action' => 'teammate_request_accept_critical_error',
                'log_message' => "Erreur critique inattendue lors de l'acceptation de la demande de coéquipier (ID: {$demande_coequipier->id}) par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'demande_id' => $demande_coequipier->id,
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->with('error', 'Une erreur est survenue lors de l\'acceptation de la demande : ' . $e->getMessage());
        }
    }

    /**
     * Refuse une demande de coéquipier.
     * @param DemandeCoequipier $demande_coequipier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refuse(DemandeCoequipier $demande_coequipier)
    {
        $user = Auth::user();
        $demandeur = $demande_coequipier->demandeur; // Pour les logs

        try {
            // Seuls les stagiaires peuvent refuser des demandes
            if (!$user->isStagiaire()) {
                // Enregistrement du log : Tentative de refus de demande par non-stagiaire
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'teammate_request_refuse_unauthorized_role',
                    'log_message' => "Tentative de refus de demande de coéquipier non autorisée par '{$user->nom} {$user->prenom}' ({$user->role->nom}). Demande ID: {$demande_coequipier->id}.",
                    'object_snapshot' => $demande_coequipier->toArray(),
                ]);
                return back()->with('error', 'Seuls les stagiaires peuvent refuser des demandes de coéquipier.');
            }
            // S'assurer que l'utilisateur connecté est bien le receveur de la demande et que la demande est en attente
            if ($demande_coequipier->id_stagiaire_receveur !== $user->id || $demande_coequipier->statut_demande !== 'en_attente') {
                // Enregistrement du log : Tentative de refus de demande non autorisée ou déjà traitée
                Log::create([
                    'user_id' => $user->id,
                    'action' => 'teammate_request_refuse_unauthorized_or_processed',
                    'log_message' => "Tentative de refus d'une demande de coéquipier non autorisée ou déjà traitée par '{$user->nom} {$user->prenom}' ({$user->role->nom}). Demande ID: {$demande_coequipier->id}. Statut actuel: {$demande_coequipier->statut_demande}.",
                    'object_snapshot' => $demande_coequipier->toArray(),
                ]);
                return back()->with('error', 'Action non autorisée ou demande déjà traitée.');
            }

            $demande_coequipier->update(['statut_demande' => 'refusée']);

            // Enregistrement du log : Demande de coéquipier refusée avec succès
            Log::create([
                'user_id' => $user->id,
                'action' => 'teammate_request_refused_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a refusé la demande de coéquipier de '{$demandeur->nom} {$demandeur->prenom}' (ID demande: {$demande_coequipier->id}).",
                'object_snapshot' => $demande_coequipier->toArray(),
            ]);

            return redirect()->route('demande_coequipiers.index')->with('success', 'Demande refusée.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur critique lors du refus de demande
            Log::create([
                'user_id' => $user->id,
                'action' => 'teammate_request_refuse_critical_error',
                'log_message' => "Erreur critique inattendue lors du refus de la demande de coéquipier (ID: {$demande_coequipier->id}) par '{$user->nom} {$user->prenom}' ({$user->role->nom}).",
                'object_snapshot' => [
                    'demande_id' => $demande_coequipier->id,
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->with('error', 'Une erreur est survenue lors du refus de la demande : ' . $e->getMessage());
        }
    }

    /**
     * Annule une demande de coéquipier (par le demandeur).
     * @param DemandeCoequipier $demande_coequipier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(DemandeCoequipier $demande_coequipier)
{
    $user = Auth::user();
    $receveur = $demande_coequipier->receveur; // Pour les logs
    
    // Capture l'ID de la demande AVANT toute modification ou suppression
    $demandeId = $demande_coequipier->id;
    // Capture un instantané de la demande AVANT la suppression pour le log
    $demandeSnapshot = $demande_coequipier->toArray();

    try {
        // Seuls les stagiaires peuvent annuler des demandes
        if (!$user->isStagiaire()) {
            Log::create([
                'user_id' => $user->id,
                'action' => 'teammate_request_cancel_unauthorized_role',
                'log_message' => "Tentative d'annulation de demande de coéquipier non autorisée par '{$user->nom} {$user->prenom}' ({$user->role->nom}). Demande ID: {$demandeId}.", // Utilisez $demandeId ici
                'object_snapshot' => $demandeSnapshot,
            ]);
            return back()->with('error', 'Seuls les stagiaires peuvent annuler des demandes de coéquipier.');
        }

        // S'assurer que l'utilisateur connecté est bien le demandeur et que la demande est en attente
        if ($demande_coequipier->id_stagiaire_demandeur !== $user->id || $demande_coequipier->statut_demande !== 'en_attente') {
            Log::create([
                'user_id' => $user->id,
                'action' => 'teammate_request_cancel_unauthorized_or_processed',
                'log_message' => "Tentative d'annulation d'une demande de coéquipier non autorisée ou déjà traitée par '{$user->nom} {$user->prenom}' ({$user->role->nom}). Demande ID: {$demandeId}. Statut actuel: {$demande_coequipier->statut_demande}.", // Utilisez $demandeId
                'object_snapshot' => $demandeSnapshot,
            ]);
            return back()->with('error', 'Action non autorisée ou demande déjà traitée.');
        }

        $demande_coequipier->delete(); // Supprime la demande

        // Enregistrement du log : Demande de coéquipier annulée avec succès
        Log::create([
            'user_id' => $user->id,
            'action' => 'teammate_request_canceled_success',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' ({$user->role->nom}) a annulé sa demande de coéquipier à '{$receveur->nom} {$receveur->prenom}' (ID demande: {$demandeId}).", // Utilisez $demandeId
            'object_snapshot' => $demandeSnapshot, // L'état de la demande avant suppression
        ]);

        return redirect()->route('demande_coequipiers.index')->with('success', 'Demande annulée.');
    } catch (\Exception $e) {
        // Enregistrement du log : Erreur critique lors de l'annulation de demande
        Log::create([
            'user_id' => $user->id,
            'action' => 'teammate_request_cancel_critical_error',
            'log_message' => "Erreur critique inattendue lors de l'annulation de la demande de coéquipier (ID: {$demandeId}) par '{$user->nom} {$user->prenom}' ({$user->role->nom}).", // Utilisez $demandeId
            'object_snapshot' => [
                'demande_id' => $demandeId,
                'exception_message' => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString(),
            ],
        ]);
        return back()->with('error', 'Une erreur est survenue lors de l\'annulation de la demande : ' . $e->getMessage());
    }
}
}