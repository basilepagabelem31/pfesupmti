<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use App\Notifications\NoteAdded;
use App\Notifications\NoteUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Vérifie si l'utilisateur est le stagiaire concerné ou un de ses coéquipiers
        $isStagiaire = $user->id == $stagiaire->id;
        $isCoequipier = $stagiaire->getAllCoequipiers()->pluck('id')->contains($user->id);

        //  laisser l'accès aux admins/superviseurs
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

        // Note principale
        $note = Note::create([
            'valeur' => $request->valeur,
            'visibilite' => $request->visibilite,
            'date_note' => now(),
            'stagiaire_id' => $request->stagiaire_id,
            'donneur_id' => $user->id,
        ]);
        // Notification principale
        $note->stagiaire->notify(new NoteAdded($note));

        // Propagation aux coéquipiers si demandé
        if ($request->filled('propager')) {
            $stagiaire = User::find($request->stagiaire_id);
            foreach ($stagiaire->getAllCoequipiers() as $coequipier) {
                $copie = Note::create([
                    'valeur' => $request->valeur,
                    'visibilite' => $request->visibilite,
                    'date_note' => now(),
                    'stagiaire_id' => $coequipier->id,
                    'donneur_id' => $user->id,
                ]);
                $coequipier->notify(new NoteAdded($copie));
            }
        }

        return back()->with('success', 'Note ajoutée avec succès !');
    }

    // Edition d'une note
    public function edit(string $id)
    {
        $note = Note::findOrFail($id);

        // Seul le donneur de la note peut modifier
        if (Auth::id() !== $note->donneur_id) {
            abort(403);
        }
        return view('notes.edit', compact('note'));
    }

    // Mise à jour d'une note
    public function update(Request $request, string $id)
    {
        $note = Note::findOrFail($id);

        // Seul le donneur de la note peut modifier
        if (Auth::id() !== $note->donneur_id) {
            abort(403);
        }

        $request->validate([
            'valeur' => 'required|string',
            'visibilite' => 'required|in:all,donneur,donneur + stagiaire,superviseurs- stagiaire',
        ]);

        // On recherche toutes les notes équivalentes (stagiaire + coéquipiers)
        Note::where('donneur_id', $note->donneur_id)
        ->where('date_note', $note->date_note)
        ->where('valeur', $note->valeur)
        ->where('visibilite', $note->visibilite)
        ->update([
            'valeur' => $request->valeur,
            'visibilite' => $request->visibilite,
        ]);
        // Récupère toutes les notes concernées après update (pour notification)
        $notes = Note::where('donneur_id', $note->donneur_id)
            ->where('date_note', $note->date_note)
            ->where('valeur', $request->valeur)
            ->where('visibilite', $request->visibilite)
            ->get();

        // Envoie une notification à chaque stagiaire concerné
        foreach ($notes as $n) {
            $n->stagiaire->notify(new NoteUpdated($n));
        }

    return redirect()->route('notes.fiche_stagiaire', $note->stagiaire_id)->with('success', 'Note modifiée chez le stagiaire et ses coéquipiers !');
    }

    // Suppression d'une note
    public function destroy(string $id)
    {
        $note = Note::findOrFail($id);

        // Seul le donneur de la note peut supprimer
        if (Auth::id() !== $note->donneur_id) {
            abort(403);
        }

        // Récupérer tous les id des stagiaires concernés (stagiaire + coéquipiers)
        $stagiaire = $note->stagiaire;
        $coequipiers = $stagiaire->getAllCoequipiers();
        $ids = $coequipiers->pluck('id')->toArray();
        $ids[] = $stagiaire->id;

        // Supprime toutes les notes du groupe même si valeur/visibilité ont changé
        Note::where('donneur_id', $note->donneur_id)
            ->where('date_note', $note->date_note)
            ->whereIn('stagiaire_id', $ids)
            ->delete();

        return redirect()->route('notes.fiche_stagiaire', $note->stagiaire_id)
            ->with('success', 'Note supprimée avec succès chez le stagiaire et ses coéquipiers !');
    }
}