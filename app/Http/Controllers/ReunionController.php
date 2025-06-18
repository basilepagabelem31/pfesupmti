<?php

namespace App\Http\Controllers;

use App\Jobs\SendAbsenceNotification;
use App\Mail\AbsenceNotificationMail;
use App\Models\Absence;
use App\Models\EmailLog;
use App\Models\Groupe;
use App\Models\Reunion;
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

        $reunions = $query->get();
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
        $absence = Absence::updateOrCreate(
            [
                'reunion_id' => $reunionId,
                'stagiaire_id' => $stagiaireId,
            ],
            [
                'statut' => $request->input('statut'),
                'note' => $request->input('note'),
                'valide_par' => Auth::user()->id,//par defaut le superviseur 
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
    $reunion->status = true;//pour dire que la reunion est cloture 
    $reunion->save();

    $emailsSent = 0;
    $emailErrors = [];

    foreach ($reunion->absences as $absence) {
        if ($absence->statut === 'Absent' && $absence->stagiaire->isActive()) {
            try {
                Mail::to($absence->stagiaire->email)
                    ->send(new AbsenceNotificationMail($absence));

                EmailLog::create([
                    'to_email' => $absence->stagiaire->email,
                    'subject' => 'Notification d\'absence',
                    'body' => view('emails.absence_notification', ['absence' => $absence])->render(),
                    'status' => 'sent',
                    'absence_id' => $absence->id,
                ]);

                $emailsSent++;
            } catch (\Exception $e) {
                EmailLog::create([
                    'to_email' => $absence->stagiaire->email,
                    'subject' => 'Notification d\'absence',
                    'body' => view('emails.absence_notification', ['absence' => $absence])->render(),
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'absence_id' => $absence->id,
                ]);

                $emailErrors[] = $absence->stagiaire->email;
            }
        }
    }

    $message = "Réunion clôturée avec succès. ";
    if ($emailsSent > 0) {
        $message .= "$emailsSent email(s) envoyé(s) aux absents.";
    }
    if (count($emailErrors) > 0) {
        $message .= " Échec d'envoi pour " . count($emailErrors) . " email(s).";
    }

    return back()->with('success', $message);
}
}