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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReunionController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', today()->toDateString());
        $query = Reunion::with('groupe')->orderBy('date', 'desc');

        if ($request->filled('date')) {
            $query->whereDate('date', $date);
        }

        $reunions = $query->paginate(10);
        $groupes = Groupe::all();

        return view('reunions.index', compact('reunions', 'groupes', 'date'));
    }

    public function store(Request $request)
    {
       
        $request->validate([
            'groupe_id' => 'required|exists:groupes,id',
            'date' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'note' => 'nullable|string|max:255',
        ]);

        $reunion = Reunion::create([
            'groupe_id' => $request->input('groupe_id'),
            'date' => $request->input('date'),
            'heure_debut' => $request->input('heure_debut'),
            'heure_fin' => $request->input('heure_fin'),
            'note' => $request->input('note'),
            'status' => false,
        ]);

        return redirect()->route('reunions.index')->with('success', 'Réunion créée avec succès.');
    }

    public function show(string $id)
    {
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

        return view('reunions.feuille_presence', compact('reunion', 'presences'));
    }

    public function updatePresence(Request $request, $reunionId, $stagiaireId)
    {
        // On récupère l'absence existante si elle existe
        $absence = Absence::where('reunion_id', $reunionId)
            ->where('stagiaire_id', $stagiaireId)
            ->first();

        // On prépare les valeurs à mettre à jour
        $newStatut = $request->input('statut', $absence ? $absence->statut : null);
        $newNote = $request->input('note', $absence ? $absence->note : null);

        $absence = Absence::updateOrCreate(
            [
                'reunion_id' => $reunionId,
                'stagiaire_id' => $stagiaireId,
            ],
            [
                'statut' => $newStatut,
                'note' => $newNote,
                'valide_par' => Auth::user()->id, // par defaut le superviseur 
            ]
        );

        $absence->load('valideur');

        return response()->json([
            'success' => true,
            'statut' => $absence->statut,
            'note' => $absence->note,
            'valideur' => $absence->valideur ? $absence->valideur->nom : '',
        ]);
    }

    public function cloturer(string $id)
{
    $reunion = Reunion::with('absences.stagiaire')->findOrFail($id);
    $reunion->status = true; // Marquer la réunion comme clôturée
    $reunion->save();

    $emailService = new \App\Services\AbsenceEmailService();

    foreach ($reunion->absences as $absence) {
        $stagiaire = $absence->stagiaire;
        // On ne traite que les stagiaires actifs et absents à cette réunion
        if ($absence->statut !== 'Absent' || !$stagiaire || !$stagiaire->isActive()) continue;

        // Appel d'une méthode qui retourne un entier (à adapter selon ta logique)
        $consecutive = $this->countConsecutiveAbsences($stagiaire->id);

        // Cas d'abandon (3 absences consécutives ou plus, et pas déjà "abandonné")
           if ($consecutive >= 3 && $stagiaire->statut_id != 2) {
                $stagiaire->statut_id = 2; // statut "abandonné"
                $stagiaire->save();
                $emailService->sendAbandonEmail($stagiaire, $reunion);
                continue; // PAS d'email d'absence_triple !
            }

         // Sinon, email d'absence classique (simple ou double uniquement)
        $emailService->sendAbsenceEmail($stagiaire, $reunion, $consecutive);
    }

    return back()->with('success', 'Réunion clôturée et emails envoyés si nécessaire.');
}

/**
 * Retourne le nombre d'absences consécutives pour un stagiaire (à placer dans ce controller)
 */
protected function countConsecutiveAbsences($stagiaire_id)
{
    $absences = Absence::where('stagiaire_id', $stagiaire_id)
        ->orderBy('created_at', 'desc')
        ->get();

    $consecutive = 0;
    foreach ($absences as $absence) {
        if ($absence->statut === 'Absent') {
            $consecutive++;
        } else {
            break;
        }
    }
    return $consecutive;
}
}