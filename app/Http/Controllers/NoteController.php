<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use App\Notifications\NoteAdded;
use App\Notifications\NoteUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\helper\LogHelper;

class NoteController extends Controller
{
    // Liste des stagiaires avec résumé des notes
    public function listeStagiaires()
    {
        $stagiaires = User::whereHas('role', function($q){
            $q->where('nom', 'Stagiaire');
        })->withCount('notes')->get();

        return view('notes.liste_stagiaire', compact('stagiaires'));
    }

    // Affiche les notes d'un stagiaire
    public function ficheStagiaire($id)
    {
        $stagiaire = User::findOrFail($id);
        $user = Auth::user();

        $isStagiaire = $user->id == $stagiaire->id;
        $isCoequipier = $stagiaire->getAllCoequipiers()->pluck('id')->contains($user->id);

        $isAdmin = $user->role->nom === 'Administrateur';
        $isSuperviseur = $user->role->nom === 'Superviseur';

        if (!($isStagiaire || $isCoequipier || $isAdmin || $isSuperviseur)) {
            abort(403, 'Accès interdit');
        }

        $notes = Note::where('stagiaire_id', $stagiaire->id)->orderByDesc('date_note')->get();

        return view('notes.fiche_stagiaire', compact('stagiaire', 'notes'));
    }

    // Ajout d'une note (avec propagation si demandé)
    public function store(Request $request)
    {
        $request->validate([
            'valeur' => 'required|string',
            'visibilite' => 'required|in:all,donneur,donneur + stagiaire,superviseurs- stagiaire',
            'stagiaire_id' => 'required|exists:users,id',
            'propager' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $stagiaireCible = User::find($request->stagiaire_id); // Le stagiaire original de la note

        // Note principale
        $note = Note::create([
            'valeur' => $request->valeur,
            'visibilite' => $request->visibilite,
            'date_note' => now(),
            'stagiaire_id' => $request->stagiaire_id,
            'donneur_id' => $user->id,
        ]);
        $note->stagiaire->notify(new NoteAdded($note));

        $coequipiersAffectes = []; // Pour le log
        // Propagation aux coéquipiers si demandé
        if ($request->filled('propager')) {
            foreach ($stagiaireCible->getAllCoequipiers() as $coequipier) {
                $copie = Note::create([
                    'valeur' => $request->valeur,
                    'visibilite' => $request->visibilite,
                    'date_note' => now(),
                    'stagiaire_id' => $coequipier->id,
                    'donneur_id' => $user->id,
                ]);
                $coequipier->notify(new NoteAdded($copie));
                $coequipiersAffectes[] = $coequipier->prenom . ' ' . $coequipier->nom . ' (ID: ' . $coequipier->id . ')';
            }
        }

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR L'AJOUT
        $donneurInfo = $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ')';
        $cibleInfo = $stagiaireCible->prenom . ' ' . $stagiaireCible->nom . ' (ID: ' . $stagiaireCible->id . ')';

        $message = 'Le ' . $donneurInfo . ' a ajouté une note pour le stagiaire ' . $cibleInfo . '. Contenu: "' . Str::limit($request->valeur, 100) . '". Visibilité: ' . $request->visibilite . '.';
        if (!empty($coequipiersAffectes)) {
            $message .= ' Propagée aux coéquipiers: ' . implode(', ', $coequipiersAffectes) . '.';
        }

        LogHelper::logAction(
            'Ajout de note',
            $message,
            Auth::id()
        );

        return back()->with('success', 'Note ajoutée avec succès !');
    }

    // Edition d'une note
    public function edit(string $id)
    {
        $note = Note::findOrFail($id);

        if (Auth::id() !== $note->donneur_id) {
            abort(403);
        }
        return view('notes.edit', compact('note'));
    }

    // Mise à jour d'une note
    public function update(Request $request, string $id)
    {
        $note = Note::findOrFail($id);

        if (Auth::id() !== $note->donneur_id) {
            abort(403);
        }

        $request->validate([
            'valeur' => 'required|string',
            'visibilite' => 'required|in:all,donneur,donneur + stagiaire,superviseurs- stagiaire',
        ]);

        // Capture les anciennes valeurs pour le log
        $oldValeur = $note->valeur;
        $oldVisibilite = $note->visibilite;

        // On recherche toutes les notes équivalentes (stagiaire + coéquipiers)
        // en utilisant les critères de recherche basés sur la note originale
        $notesToUpdate = Note::where('donneur_id', $note->donneur_id)
            ->where('date_note', $note->date_note)
            ->where('valeur', $note->valeur) // Ancienne valeur pour trouver les notes "groupées"
            ->where('visibilite', $note->visibilite); // Ancienne visibilité

        $notesUpdatedCollection = $notesToUpdate->get(); // Récupérer la collection avant la mise à jour pour les notifications et logs

        $notesToUpdate->update([
            'valeur' => $request->valeur,
            'visibilite' => $request->visibilite,
        ]);

        // Envoie une notification à chaque stagiaire concerné
        foreach ($notesUpdatedCollection as $n) {
            // Mettre à jour l'instance $n pour qu'elle reflète les nouvelles valeurs avant de notifier
            $n->valeur = $request->valeur;
            $n->visibilite = $request->visibilite;
            $n->stagiaire->notify(new NoteUpdated($n));
        }

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA MODIFICATION
        $user = Auth::user();
        $donneurInfo = $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ')';
        $cibleInfo = $note->stagiaire->prenom . ' ' . $note->stagiaire->nom . ' (ID: ' . $note->stagiaire->id . ')';

        $changes = [];
        if ($oldValeur !== $request->valeur) {
            $changes[] = 'Valeur: "' . Str::limit($oldValeur, 50) . '" -> "' . Str::limit($request->valeur, 50) . '"';
        }
        if ($oldVisibilite !== $request->visibilite) {
            $changes[] = 'Visibilité: "' . $oldVisibilite . '" -> "' . $request->visibilite . '"';
        }

        $affectedStagiaires = $notesUpdatedCollection->pluck('stagiaire.prenom', 'stagiaire.nom')->map(function($prenom, $nom){
            return $prenom . ' ' . $nom;
        })->unique()->implode(', ');


        $message = 'Le ' . $donneurInfo . ' a modifié une note. Note originale pour le stagiaire ' . $cibleInfo . '. ';
        if (!empty($changes)) {
            $message .= 'Changements: ' . implode(', ', $changes) . '. ';
        } else {
            $message .= 'Aucun changement significatif détecté (peut-être seulement des détails internes). ';
        }
        $message .= 'Notes affectées pour les stagiaires: ' . $affectedStagiaires . '.';

        LogHelper::logAction(
            'Modification de note',
            $message,
            Auth::id()
        );

        return redirect()->route('notes.fiche_stagiaire', $note->stagiaire_id)->with('success', 'Note modifiée chez le stagiaire et ses coéquipiers !');
    }

    // Suppression d'une note
    public function destroy(string $id)
    {
        $note = Note::findOrFail($id);

        if (Auth::id() !== $note->donneur_id) {
            abort(403);
        }

        // Récupérer les informations pour le log avant la suppression
        $donneurInfo = Auth::user()->role->nom . ' ' . Auth::user()->prenom . ' ' . Auth::user()->nom . ' (ID: ' . Auth::id() . ')';
        $originalStagiaireInfo = $note->stagiaire->prenom . ' ' . $note->stagiaire->nom . ' (ID: ' . $note->stagiaire->id . ')';
        $noteContenu = Str::limit($note->valeur, 100);
        $noteVisibilite = $note->visibilite;

        // Récupérer tous les id des stagiaires concernés (stagiaire + coéquipiers)
        $stagiaire = $note->stagiaire;
        $coequipiers = $stagiaire->getAllCoequipiers();
        $ids = $coequipiers->pluck('id')->toArray();
        $ids[] = $stagiaire->id;

        // Pour le log, lister les stagiaires dont les notes vont être supprimées
        $affectedStagiairesNames = User::whereIn('id', $ids)->get()->pluck('full_name')->implode(', ');


        // Supprime toutes les notes du groupe même si valeur/visibilité ont changé
        Note::where('donneur_id', $note->donneur_id)
            ->where('date_note', $note->date_note)
            ->whereIn('stagiaire_id', $ids)
            ->delete();

        // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA SUPPRESSION
        $message = 'Le ' . $donneurInfo . ' a supprimé une note (contenu initial: "' . $noteContenu . '", visibilité initiale: "' . $noteVisibilite . '"). Note originale pour le stagiaire ' . $originalStagiaireInfo . '. Notes supprimées pour les stagiaires: ' . $affectedStagiairesNames . '.';

        LogHelper::logAction(
            'Suppression de note',
            $message,
            Auth::id()
        );

        return redirect()->route('notes.fiche_stagiaire', $note->stagiaire_id)
            ->with('success', 'Note supprimée avec succès chez le stagiaire et ses coéquipiers !');
    }
}