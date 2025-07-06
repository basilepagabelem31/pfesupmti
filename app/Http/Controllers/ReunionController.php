<?php

namespace App\Http\Controllers;

use App\Jobs\SendAbsenceNotification;
use App\Mail\AbsenceNotificationMail;
use App\Models\Absence;
use App\Models\EmailLog;
use App\Models\Groupe;
use App\Models\Reunion;
use App\Services\AbsenceEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log as LaravelLog; // Alias pour éviter le conflit avec notre modèle App\Models\Log
use Illuminate\Support\Facades\Mail;
use App\Models\Log; // Importez votre modèle Log
use Illuminate\Validation\ValidationException; // Pour capturer les exceptions de validation

class ReunionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $date = $request->input('date', today()->toDateString());
        $query = Reunion::with('groupe')->orderBy('date', 'desc');

        if ($request->filled('date')) {
            $query->whereDate('date', $date);
        }

        $reunions = $query->paginate(10);
        $groupes = Groupe::all();

        // Enregistrement du log : Consultation de la liste des réunions
        Log::create([
            'user_id' => $user->id,
            'action' => 'view_reunions_list',
            'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a consulté la liste des réunions. Filtrée par date: {$date}.",
            'object_snapshot' => [
                'filtered_date' => $date,
                'reunions_count' => $reunions->total(), // Utiliser total() pour la pagination
            ],
        ]);

        return view('reunions.index', compact('reunions', 'groupes', 'date'));
    }

    public function store(Request $request)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $newReunion = null; // Déclarer pour la portée du try/catch

        try {
            $validated = $request->validate([
                'groupe_id' => 'required|exists:groupes,id',
                'date' => 'required|date',
                'heure_debut' => 'required|date_format:H:i',
                'heure_fin' => 'required|date_format:H:i|after:heure_debut',
                'note' => 'nullable|string|max:255',
            ]);

            $newReunion = Reunion::create([
                'groupe_id' => $validated['groupe_id'],
                'date' => $validated['date'],
                'heure_debut' => $validated['heure_debut'],
                'heure_fin' => $validated['heure_fin'],
                'note' => $validated['note'],
                'status' => false, // Par défaut, non clôturée
            ]);

            // Enregistrement du log : Création de réunion réussie
            Log::create([
                'user_id' => $user->id,
                'action' => 'reunion_created_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a créé une nouvelle réunion pour le groupe ID: {$newReunion->groupe_id} le {$newReunion->date} de {$newReunion->heure_debut} à {$newReunion->heure_fin}.",
                'object_snapshot' => $newReunion->toArray(),
            ]);

            return redirect()->route('reunions.index')->with('success', 'Réunion créée avec succès.');

        } catch (ValidationException $e) {
            // Enregistrement du log : Échec de validation lors de la création de réunion
            Log::create([
                'user_id' => $user->id,
                'action' => 'reunion_create_validation_failed',
                'log_message' => "Échec de validation lors de la tentative de création d'une réunion par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'input' => $request->all(),
                    'errors' => $e->errors(),
                ],
            ]);
            return back()->withInput()->withErrors($e->errors())->with('error', 'Veuillez corriger les erreurs dans le formulaire.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la création de réunion
            Log::create([
                'user_id' => $user->id,
                'action' => 'reunion_create_critical_error',
                'log_message' => "Erreur critique inattendue lors de la création d'une réunion par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'input' => $request->all(),
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création de la réunion : ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $reunion = null; // Déclarer pour la portée du try/catch

        try {
            $reunion = Reunion::with('groupe.stagiaires', 'absences.stagiaire')->findOrFail($id);

            $stagiaires = $reunion->groupe->stagiaires;

            $presences = [];
            foreach ($stagiaires as $stagiaire) {
                $absence = $reunion->absences->firstWhere('stagiaire_id', $stagiaire->id);
                $presences[] = [
                    'stagiaire' => $stagiaire,
                    'absence' => $absence,
                ];
            }

            // Enregistrement du log : Consultation de la feuille de présence d'une réunion
            Log::create([
                'user_id' => $user->id,
                'action' => 'view_reunion_presence_sheet',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a consulté la feuille de présence de la réunion ID: {$reunion->id} (Groupe: {$reunion->groupe->nom}).",
                'object_snapshot' => [
                    'reunion_id' => $reunion->id,
                    'groupe_id' => $reunion->groupe_id,
                    'date' => $reunion->date,
                ],
            ]);

            return view('reunions.feuille_presence', compact('reunion', 'presences'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Enregistrement du log : Réunion non trouvée pour affichage
            Log::create([
                'user_id' => $user->id,
                'action' => 'reunion_show_not_found',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a tenté de consulter une réunion inexistante (ID: {$id}).",
                'object_snapshot' => [
                    'reunion_id_attempted' => $id,
                    'error' => $e->getMessage(),
                ],
            ]);
            abort(404, 'Réunion introuvable.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de l'affichage de la feuille de présence
            Log::create([
                'user_id' => $user->id,
                'action' => 'reunion_show_critical_error',
                'log_message' => "Erreur critique inattendue lors de l'affichage de la feuille de présence pour la réunion ID: {$id} par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'reunion_id_attempted' => $id,
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            abort(500, 'Erreur interne du serveur.');
        }
    }

    public function updatePresence(Request $request, $reunionId, $stagiaireId)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $reunion = null; // Pour la portée
        $stagiaire = null; // Pour la portée

        try {
            $reunion = Reunion::findOrFail($reunionId);
            $stagiaire = $reunion->groupe->stagiaires()->findOrFail($stagiaireId); // S'assurer que le stagiaire appartient au groupe de la réunion

            $oldAbsence = Absence::where('reunion_id', $reunionId)
                                 ->where('stagiaire_id', $stagiaireId)
                                 ->first();

            $newStatut = $request->input('statut', $oldAbsence ? $oldAbsence->statut : null);
            $newNote = $request->input('note', $oldAbsence ? $oldAbsence->note : null);

            $absence = Absence::updateOrCreate(
                [
                    'reunion_id' => $reunionId,
                    'stagiaire_id' => $stagiaireId,
                ],
                [
                    'statut' => $newStatut,
                    'note' => $newNote,
                    'valide_par' => $user->id, // par defaut le superviseur
                ]
            );

            $absence->load('valideur');

            // Enregistrement du log : Mise à jour de présence/absence réussie
            Log::create([
                'user_id' => $user->id,
                'action' => 'reunion_presence_updated_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a mis à jour la présence/absence du stagiaire '{$stagiaire->nom} {$stagiaire->prenom}' (ID: {$stagiaire->id}) pour la réunion ID: {$reunion->id}. Statut: '{$absence->statut}', Note: '{$absence->note}'.",
                'object_snapshot' => [
                    'reunion_id' => $reunion->id,
                    'stagiaire_id' => $stagiaire->id,
                    'old_statut' => $oldAbsence ? $oldAbsence->statut : 'N/A',
                    'new_statut' => $absence->statut,
                    'old_note' => $oldAbsence ? $oldAbsence->note : 'N/A',
                    'new_note' => $absence->note,
                    'valide_par' => $absence->valide_par,
                ],
            ]);

            return response()->json([
                'success' => true,
                'statut' => $absence->statut,
                'note' => $absence->note,
                'valideur' => $absence->valideur ? $absence->valideur->nom : '',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Enregistrement du log : Réunion ou Stagiaire non trouvé pour mise à jour présence
            Log::create([
                'user_id' => $user->id,
                'action' => 'reunion_presence_update_not_found',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a tenté de mettre à jour la présence pour une réunion (ID: {$reunionId}) ou un stagiaire (ID: {$stagiaireId}) inexistant.",
                'object_snapshot' => [
                    'reunion_id_attempted' => $reunionId,
                    'stagiaire_id_attempted' => $stagiaireId,
                    'error' => $e->getMessage(),
                ],
            ]);
            return response()->json(['success' => false, 'message' => 'Réunion ou stagiaire introuvable.'], 404);
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la mise à jour de présence
            Log::create([
                'user_id' => $user->id,
                'action' => 'reunion_presence_update_critical_error',
                'log_message' => "Erreur critique inattendue lors de la mise à jour de la présence pour la réunion ID: {$reunionId}, Stagiaire ID: {$stagiaireId} par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'reunion_id_attempted' => $reunionId,
                    'stagiaire_id_attempted' => $stagiaireId,
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return response()->json(['success' => false, 'message' => 'Erreur interne du serveur lors de la mise à jour de présence.'], 500);
        }
    }

    public function cloturer(string $id)
    {
        $user = Auth::user(); // Obtenez l'utilisateur connecté
        $reunion = null; // Pour la portée du try/catch
        $cloturedAbsencesCount = 0;
        $abandonnedStagiaires = [];

        try {
            $reunion = Reunion::with('absences.stagiaire')->findOrFail($id);
            $reunion->status = true; // Marquer la réunion comme clôturée
            $reunion->save();

            $emailService = new \App\Services\AbsenceEmailService();
            $logMessages = [];

            foreach ($reunion->absences as $absence) {
                $stagiaire = $absence->stagiaire;
                // On ne traite que les stagiaires actifs et absents à cette réunion
                if ($absence->statut !== 'Absent' || !$stagiaire || !$stagiaire->isActive()) {
                    continue;
                }

                $cloturedAbsencesCount++;

                // Appel d'une méthode qui retourne un entier (à adapter selon ta logique)
                $consecutive = $this->countConsecutiveAbsences($stagiaire->id);

                // Cas d'abandon (3 absences consécutives ou plus, et pas déjà "abandonné")
                if ($consecutive >= 3 && $stagiaire->statut_id != 2) {
                    $oldStagiaireStatusId = $stagiaire->statut_id;
                    $stagiaire->statut_id = 2; // statut "abandonné" (Assumer que 2 est l'ID pour "abandonné")
                    $stagiaire->save();
                    $emailService->sendAbandonEmail($stagiaire, $reunion);
                    $abandonnedStagiaires[] = $stagiaire->nom . ' ' . $stagiaire->prenom . ' (ID: ' . $stagiaire->id . ')';
                    $logMessages[] = "Stagiaire '{$stagiaire->nom} {$stagiaire->prenom}' (ID: {$stagiaire->id}) a été marqué comme 'abandonné' (Statut ID {$oldStagiaireStatusId} -> 2). Email d'abandon envoyé.";
                    continue; // PAS d'email d'absence_triple !
                }

                // Sinon, email d'absence classique (simple ou double uniquement)
                $emailService->sendAbsenceEmail($stagiaire, $reunion, $consecutive);
                $logMessages[] = "Email d'absence (consec: {$consecutive}) envoyé au stagiaire '{$stagiaire->nom} {$stagiaire->prenom}' (ID: {$stagiaire->id}).";
            }

            // Enregistrement du log : Clôture de réunion réussie
            Log::create([
                'user_id' => $user->id,
                'action' => 'reunion_clotured_success',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a clôturé la réunion ID: {$reunion->id} (Groupe: {$reunion->groupe->nom}). {$cloturedAbsencesCount} absences traitées. " . implode('; ', $logMessages),
                'object_snapshot' => [
                    'reunion_id' => $reunion->id,
                    'reunion_date' => $reunion->date,
                    'absences_processed_count' => $cloturedAbsencesCount,
                    'stagiaires_abandonned' => $abandonnedStagiaires,
                    'log_details' => $logMessages,
                ],
            ]);

            return redirect()->route('reunions.show', $reunion->id)->with('success', 'Réunion clôturée et emails envoyés si nécessaire.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Enregistrement du log : Réunion non trouvée pour clôture
            Log::create([
                'user_id' => $user->id,
                'action' => 'reunion_cloture_not_found',
                'log_message' => "L'utilisateur '{$user->nom} {$user->prenom}' (ID: {$user->id}, Rôle: {$user->role->nom}) a tenté de clôturer une réunion inexistante (ID: {$id}).",
                'object_snapshot' => [
                    'reunion_id_attempted' => $id,
                    'error' => $e->getMessage(),
                ],
            ]);
            abort(404, 'Réunion introuvable.');
        } catch (\Exception $e) {
            // Enregistrement du log : Erreur inattendue lors de la clôture de réunion
            Log::create([
                'user_id' => $user->id,
                'action' => 'reunion_cloture_critical_error',
                'log_message' => "Erreur critique inattendue lors de la clôture de la réunion ID: {$id} par '{$user->nom} {$user->prenom}' (ID: {$user->id}).",
                'object_snapshot' => [
                    'reunion_id_attempted' => $id,
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                ],
            ]);
            return back()->with('error', 'Une erreur est survenue lors de la clôture de la réunion : ' . $e->getMessage());
        }
    }

    /**
     * Retourne le nombre d'absences consécutives pour un stagiaire (à placer dans ce controller)
     */
    protected function countConsecutiveAbsences($stagiaire_id)
    {
        // Cette méthode ne nécessite pas de log direct car elle est une fonction utilitaire appelée par cloturer()
        // et les résultats seront inclus dans le log de cloturer().
        $absences = Absence::where('stagiaire_id', $stagiaire_id)
            ->whereHas('reunion', function($query) {
                $query->where('status', true); // Ne compter que les absences de réunions clôturées
            })
            ->orderBy('reunion_id', 'desc') // Ordonner par ID de réunion descendant pour la chronologie
            ->get();

        $consecutive = 0;
        foreach ($absences as $absence) {
            if ($absence->statut === 'Absent') {
                $consecutive++;
            } else {
                // S'il y a une présence, la séquence d'absences consécutives est brisée
                break;
            }
        }
        return $consecutive;
    }
}