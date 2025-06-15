<?php

namespace App\Http\Controllers;

use App\Models\Fichier;
use App\Models\User;
use App\Models\Sujet;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\helper\LogHelper; 

class FichierController extends Controller
{
    /**
     * Affiche la liste des fichiers pour un stagiaire spécifique (pour superviseur/admin)
     * ou les propres fichiers du stagiaire connecté.
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Pas besoin de loguer l'affichage de la liste des fichiers.
        $user = Auth::user();
        $sujets = Sujet::all();

        $query = Fichier::with('stagiaire', 'televerseur', 'sujet')->latest();

        $currentStagiaire = null;
        $currentTeleverseur = null;
        $stagiairesFilterList = collect();
        $televerseursFilterList = collect();

        if ($user->isStagiaire()) {
            $query->where('id_stagiaire', $user->id);
            $currentStagiaire = $user;

            $televerseursFilterList = User::whereHas('fichiersTeleverses', function($q) use ($user) {
                $q->where('id_stagiaire', $user->id);
            })->orWhere('id', $user->id)->get();

            $fichiers = $query->get();

            return view('fichiers.index_stagiaire', compact('fichiers', 'sujets', 'currentStagiaire', 'televerseursFilterList'));

        } elseif ($user->isSuperviseur() || $user->isAdministrateur()) {
            $stagiairesFilterList = User::whereHas('role', function ($q) {
                $q->where('nom', 'Stagiaire');
            })->get();

            $televerseursFilterList = User::whereHas('fichiersTeleverses')->get();

            if ($request->filled('stagiaire')) {
                $stagiaireId = $request->input('stagiaire');
                $stagiaireFiltered = User::find($stagiaireId);
                if ($stagiaireFiltered && $stagiaireFiltered->isStagiaire()) {
                    $query->where('id_stagiaire', $stagiaireFiltered->id);
                    $currentStagiaire = $stagiaireFiltered;
                }
            }

            if ($request->filled('televerseur')) {
                $televerseurId = $request->input('televerseur');
                $televerseurFiltered = User::find($televerseurId);
                if ($televerseurFiltered) {
                    $query->where('id_superviseur_televerseur', $televerseurFiltered->id);
                    $currentTeleverseur = $televerseurFiltered;
                }
            }

            $fichiers = $query->get();

            return view('fichiers.index_superviseur', compact('fichiers', 'stagiairesFilterList', 'currentStagiaire', 'sujets', 'televerseursFilterList', 'currentTeleverseur'));
        }

        abort(403, 'Accès non autorisé.');
    }

    /**
     * Affiche le formulaire de téléversement de fichier.
     * @param User|null $stagiaire
     * @return \Illuminate\View\View
     */
    public function create(User $stagiaire = null)
    {
        // Pas besoin de loguer l'affichage du formulaire.
        $user = Auth::user();
        $stagiaires = null;
        $sujets = Sujet::all();

        if ($user->isSuperviseur() || $user->isAdministrateur()) {
            $stagiaires = User::whereHas('role', function ($query) {
                $query->where('nom', 'Stagiaire');
            })->get();
        } elseif ($user->isStagiaire()) {
            $stagiaire = $user;
        } else {
            abort(403, 'Accès non autorisé.');
        }

        return view('fichiers.create', compact('stagiaire', 'stagiaires', 'sujets'));
    }

    /**
     * Gère le téléversement et l'enregistrement d'un nouveau fichier.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $stagiaireRoleId = Role::where('nom', 'Stagiaire')->value('id');

        try {
            $rules = [
                'nom_fichier' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'fichier' => 'required|file|max:10240',
                'type_fichier' => 'required|string|in:convention,rapport,attestation,autre',
                'sujet_id' => 'nullable|exists:sujets,id',
            ];

            if ($user->isSuperviseur() || $user->isAdministrateur()) {
                $rules['id_stagiaire'] = 'required|exists:users,id';
            }

            $validatedData = $request->validate($rules);

            $idStagiaireProprietaire = $user->isStagiaire() ? $user->id : $validatedData['id_stagiaire'];
            $stagiaireProprietaire = User::find($idStagiaireProprietaire); // Récupérer le stagiaire pour le log

            $path = $request->file('fichier')->store('public/fichiers');
            $urlFichier = Storage::url($path);

            $fichier = Fichier::create([
                'nom_fichier' => $validatedData['nom_fichier'],
                'description' => $validatedData['description'],
                'url_fichier' => $urlFichier,
                'id_stagiaire' => $idStagiaireProprietaire,
                'id_superviseur_televerseur' => $user->id,
                'peut_modifier' => $user->isStagiaire() ? true : $request->boolean('peut_modifier', false),
                'peut_supprimer' => $user->isStagiaire() ? true : $request->boolean('peut_supprimer', false),
                'type_fichier' => $validatedData['type_fichier'],
                'sujet_id' => $validatedData['sujet_id'],
            ]);

            // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LE TELEVERSEMENT
            $televerseurInfo = $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ')';
            $stagiaireInfo = $stagiaireProprietaire->prenom . ' ' . $stagiaireProprietaire->nom . ' (ID: ' . $stagiaireProprietaire->id . ')';

            $message = 'Le ' . $televerseurInfo . ' a téléversé un fichier ("' . $fichier->nom_fichier . '", Type: ' . $fichier->type_fichier . ') pour le stagiaire ' . $stagiaireInfo . '.';
            
            LogHelper::logAction(
                'Téléversement de fichier',
                $message,
                Auth::id()
            );

            return redirect()->route('fichiers.index')->with('success', 'Fichier téléversé avec succès !');

        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        }
    }

    /**
     * Affiche le formulaire d'édition d'un fichier.
     * @param Fichier $fichier
     * @return \Illuminate\View\View
     */
    public function edit(Fichier $fichier)
    {
        // Pas besoin de loguer l'affichage du formulaire.
        $user = Auth::user();

        if ($user->isStagiaire() && ($fichier->id_stagiaire !== $user->id || !$fichier->peut_modifier)) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce fichier.');
        } elseif (!$user->isSuperviseur() && !$user->isAdministrateur() && !$user->isStagiaire()) {
            abort(403, 'Accès non autorisé.');
        }

        $sujets = Sujet::all();

        return view('fichiers.edit', compact('fichier', 'sujets'));
    }

    /**
     * Met à jour un fichier existant.
     * @param Request $request
     * @param Fichier $fichier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Fichier $fichier)
    {
        $user = Auth::user();

        if ($user->isStagiaire() && ($fichier->id_stagiaire !== $user->id || !$fichier->peut_modifier)) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce fichier.');
        } elseif (!$user->isSuperviseur() && !$user->isAdministrateur() && !$user->isStagiaire()) {
            abort(403, 'Accès non autorisé.');
        }

        try {
            $rules = [
                'nom_fichier' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'fichier' => 'nullable|file|max:10240',
                'type_fichier' => 'required|string|in:convention,rapport,attestation,autre',
                'sujet_id' => 'nullable|exists:sujets,id',
            ];

            if ($user->isSuperviseur() || $user->isAdministrateur()) {
                $rules['peut_modifier'] = 'boolean';
                $rules['peut_supprimer'] = 'boolean';
            }

            $validatedData = $request->validate($rules);

            // Capture les données originales pour le log
            $oldNom = $fichier->nom_fichier;
            $oldDescription = $fichier->description;
            $oldType = $fichier->type_fichier;
            $oldSujet = $fichier->sujet_id;
            $oldPeutModifier = $fichier->peut_modifier;
            $oldPeutSupprimer = $fichier->peut_supprimer;

            $dataToUpdate = [
                'nom_fichier' => $validatedData['nom_fichier'],
                'description' => $validatedData['description'],
                'type_fichier' => $validatedData['type_fichier'],
                'sujet_id' => $validatedData['sujet_id'],
            ];

            if ($request->hasFile('fichier')) {
                if ($fichier->url_fichier && Storage::exists(str_replace('/storage/', 'public/', $fichier->url_fichier))) {
                    Storage::delete(str_replace('/storage/', 'public/', $fichier->url_fichier));
                }
                $path = $request->file('fichier')->store('public/fichiers');
                $dataToUpdate['url_fichier'] = Storage::url($path);
            }

            if ($user->isSuperviseur() || $user->isAdministrateur()) {
                $dataToUpdate['peut_modifier'] = $request->boolean('peut_modifier', false);
                $dataToUpdate['peut_supprimer'] = $request->boolean('peut_supprimer', false);
            }

            $fichier->update($dataToUpdate);

            // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA MODIFICATION
            $modifierInfo = $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ')';
            $stagiaireProprietaire = $fichier->stagiaire; // Le stagiaire à qui appartient le fichier
            $stagiaireInfo = ($stagiaireProprietaire ? $stagiaireProprietaire->prenom . ' ' . $stagiaireProprietaire->nom . ' (ID: ' . $stagiaireProprietaire->id . ')' : 'N/A');

            $changes = [];
            if ($oldNom !== $fichier->nom_fichier) {
                $changes[] = "Nom: '" . $oldNom . "' -> '" . $fichier->nom_fichier . "'";
            }
            if ($oldDescription !== $fichier->description) {
                $changes[] = "Description: '" . $oldDescription . "' -> '" . $fichier->description . "'";
            }
            if ($oldType !== $fichier->type_fichier) {
                $changes[] = "Type: '" . $oldType . "' -> '" . $fichier->type_fichier . "'";
            }
            if ($oldSujet !== $fichier->sujet_id) {
                $oldSujetNom = Sujet::find($oldSujet)?->nom ?? 'Aucun';
                $newSujetNom = Sujet::find($fichier->sujet_id)?->nom ?? 'Aucun';
                $changes[] = "Sujet: '" . $oldSujetNom . "' -> '" . $newSujetNom . "'";
            }
            if ($request->hasFile('fichier')) {
                $changes[] = "Fichier remplacé.";
            }
            if (($user->isSuperviseur() || $user->isAdministrateur())) {
                if ($oldPeutModifier !== $fichier->peut_modifier) {
                    $changes[] = "Permission Modifier: '" . ($oldPeutModifier ? 'Oui' : 'Non') . "' -> '" . ($fichier->peut_modifier ? 'Oui' : 'Non') . "'";
                }
                if ($oldPeutSupprimer !== $fichier->peut_supprimer) {
                    $changes[] = "Permission Supprimer: '" . ($oldPeutSupprimer ? 'Oui' : 'Non') . "' -> '" . ($fichier->peut_supprimer ? 'Oui' : 'Non') . "'";
                }
            }


            $message = 'Le ' . $modifierInfo . ' a modifié le fichier "' . $fichier->nom_fichier . '" (ID: ' . $fichier->id . ') du stagiaire ' . $stagiaireInfo . '. ';
            if (!empty($changes)) {
                $message .= 'Changements: ' . implode(', ', $changes) . '.';
            } else {
                $message .= 'Aucun changement significatif des métadonnées du fichier.';
            }

            LogHelper::logAction(
                'Modification de fichier',
                $message,
                Auth::id()
            );

            return redirect()->route('fichiers.index')->with('success', 'Fichier mis à jour avec succès !');

        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        }
    }

    /**
     * Supprime un fichier.
     * @param Fichier $fichier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Fichier $fichier)
    {
        $user = Auth::user();

        if ($user->isStagiaire() && ($fichier->id_stagiaire !== $user->id || !$fichier->peut_supprimer)) {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer ce fichier.');
        } elseif (!$user->isSuperviseur() && !$user->isAdministrateur() && !$user->isStagiaire()) {
            abort(403, 'Accès non autorisé.');
        }

        try {
            // Capturer les informations pour le log avant la suppression
            $deletedFileName = $fichier->nom_fichier;
            $deletedFileId = $fichier->id;
            $ownerStagiaire = $fichier->stagiaire;
            $ownerInfo = ($ownerStagiaire ? $ownerStagiaire->prenom . ' ' . $ownerStagiaire->nom . ' (ID: ' . $ownerStagiaire->id . ')' : 'N/A');

            // Supprimer le fichier du stockage
            if ($fichier->url_fichier && Storage::exists(str_replace('/storage/', 'public/', $fichier->url_fichier))) {
                Storage::delete(str_replace('/storage/', 'public/', $fichier->url_fichier));
            }

            // Supprimer l'entrée de la base de données
            $fichier->delete();

            // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LA SUPPRESSION
            $deleterInfo = $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ')';
            $message = 'Le ' . $deleterInfo . ' a supprimé le fichier "' . $deletedFileName . '" (ID: ' . $deletedFileId . ') appartenant au stagiaire ' . $ownerInfo . '.';

            LogHelper::logAction(
                'Suppression de fichier',
                $message,
                Auth::id()
            );

            return redirect()->route('fichiers.index')->with('success', 'Fichier supprimé avec succès !');

        } catch (\Exception $e) {
            \Log::error("Erreur lors de la suppression du fichier {$fichier->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la suppression du fichier.');
        }
    }

    /**
     * Permet de télécharger un fichier.
     * @param Fichier $fichier
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(Fichier $fichier)
    {
        $user = Auth::user();

        if ($user->id !== $fichier->id_stagiaire && !$user->isSuperviseur() && !$user->isAdministrateur()) {
            abort(403, 'Accès non autorisé au téléchargement de ce fichier.');
        }

        $filePath = str_replace('/storage/', 'public/', $fichier->url_fichier);

        if (Storage::exists($filePath)) {
            $extension = pathinfo(Storage::path($filePath), PATHINFO_EXTENSION);
            $downloadFileName = preg_replace('/\.[^.]+$/', '', $fichier->nom_fichier);
            $downloadFileName .= '.' . $extension;

            // <-- ENREGISTREMENT DU LOG SYSTEME ICI POUR LE TELECHARGEMENT
            $downloaderInfo = $user->role->nom . ' ' . $user->prenom . ' ' . $user->nom . ' (ID: ' . $user->id . ')';
            $ownerStagiaire = $fichier->stagiaire;
            $ownerInfo = ($ownerStagiaire ? $ownerStagiaire->prenom . ' ' . $ownerStagiaire->nom . ' (ID: ' . $ownerStagiaire->id . ')' : 'N/A');

            $message = 'Le ' . $downloaderInfo . ' a téléchargé le fichier "' . $fichier->nom_fichier . '" (ID: ' . $fichier->id . ') appartenant au stagiaire ' . $ownerInfo . '.';

            LogHelper::logAction(
                'Téléchargement de fichier',
                $message,
                Auth::id()
            );

            return Storage::download($filePath, $downloadFileName);
        }

        abort(404, 'Fichier non trouvé.');
    }
}