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
        $user = Auth::user();
        $sujets = Sujet::all(); // Récupère tous les sujets disponibles pour la modale d'édition

        // Base query for files, eager load relations
        $query = Fichier::with('stagiaire', 'televerseur', 'sujet')->latest();

        $currentStagiaire = null;
        $currentTeleverseur = null;
        $stagiairesFilterList = collect(); // Initialise pour la vue superviseur/admin
        $televerseursFilterList = collect(); // Initialise pour la vue superviseur/admin

        if ($user->isStagiaire()) {
            // Un stagiaire ne voit que ses propres fichiers
            $query->where('id_stagiaire', $user->id);
            $currentStagiaire = $user; // Le stagiaire actuel est l'utilisateur connecté

            // Pour la vue stagiaire, la liste des téléverseurs inclura ceux qui ont téléversé
            // des fichiers pour ce stagiaire, y compris le stagiaire lui-même.
            $televerseursFilterList = User::whereHas('fichiersTeleverses', function($q) use ($user) {
                $q->where('id_stagiaire', $user->id);
            })->orWhere('id', $user->id)->get();
            // Le filtre "Téléversé par" ne sera pas affiché pour les stagiaires dans la vue.
            // Cependant, la liste est passée pour la colonne "Téléversé par" du tableau.

            $fichiers = $query->get();

            // Retourne la vue spécifique pour les stagiaires
            return view('fichiers.index_stagiaire', compact('fichiers', 'sujets', 'currentStagiaire', 'televerseursFilterList'));

        } elseif ($user->isSuperviseur() || $user->isAdministrateur()) {
            // Superviseur/Admin peuvent voir tous les fichiers ou filtrer par stagiaire ou téléverseur

            // Peuple la liste déroulante des stagiaires pour le filtre
            $stagiairesFilterList = User::whereHas('role', function ($q) {
                $q->where('nom', 'Stagiaire');
            })->get();

            // Peuple la liste déroulante des téléverseurs pour le filtre (tous les utilisateurs qui ont téléversé des fichiers)
            $televerseursFilterList = User::whereHas('fichiersTeleverses')->get();


            // Applique le filtre par stagiaire si présent dans la requête
            if ($request->filled('stagiaire')) {
                $stagiaireId = $request->input('stagiaire');
                $stagiaireFiltered = User::find($stagiaireId);
                if ($stagiaireFiltered && $stagiaireFiltered->isStagiaire()) {
                    $query->where('id_stagiaire', $stagiaireFiltered->id);
                    $currentStagiaire = $stagiaireFiltered;
                }
            }

            // Applique le filtre par téléverseur si présent dans la requête
            if ($request->filled('televerseur')) {
                $televerseurId = $request->input('televerseur');
                $televerseurFiltered = User::find($televerseurId);
                if ($televerseurFiltered) {
                    $query->where('id_superviseur_televerseur', $televerseurFiltered->id);
                    $currentTeleverseur = $televerseurFiltered;
                }
            }

            $fichiers = $query->get();

            // Retourne la vue spécifique pour les superviseurs/administrateurs
            return view('fichiers.index_superviseur', compact('fichiers', 'stagiairesFilterList', 'currentStagiaire', 'sujets', 'televerseursFilterList', 'currentTeleverseur'));
        }

        abort(403, 'Accès non autorisé.');
    }

    /**
     * Affiche le formulaire de téléversement de fichier.
     * Peut être pour un stagiaire (il téléverse pour lui-même) ou un superviseur (il téléverse pour un stagiaire).
     * @param User|null $stagiaire
     * @return \Illuminate\View\View
     */
    public function create(User $stagiaire = null)
    {
        $user = Auth::user();
        $stagiaires = null;
        $sujets = Sujet::all(); // Récupère tous les sujets disponibles

        if ($user->isSuperviseur() || $user->isAdministrateur()) {
            // Le superviseur/admin peut choisir pour quel stagiaire téléverser
            $stagiaires = User::whereHas('role', function ($query) {
                $query->where('nom', 'Stagiaire');
            })->get();
        } elseif ($user->isStagiaire()) {
            // Le stagiaire téléverse pour lui-même, pas besoin de choisir de stagiaire
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
            // Validation des données
            $rules = [
                'nom_fichier' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'fichier' => 'required|file|max:10240', // Max 10MB (10240 KB)
                'type_fichier' => 'required|string|in:convention,rapport,attestation,autre', // Types définis par CDCH
                'sujet_id' => 'nullable|exists:sujets,id', // Le sujet est optionnel
            ];

            // Si c'est un superviseur/admin qui téléverse, il doit choisir un stagiaire
            if ($user->isSuperviseur() || $user->isAdministrateur()) {
                $rules['id_stagiaire'] = 'required|exists:users,id';
            }

            $validatedData = $request->validate($rules);

            // Déterminer l'ID du stagiaire propriétaire du fichier
            $idStagiaireProprietaire = $user->isStagiaire() ? $user->id : $validatedData['id_stagiaire'];

            // Gérer le téléversement du fichier
            // Laravel stocke le fichier avec un nom unique et son extension d'origine par défaut
            $path = $request->file('fichier')->store('public/fichiers');
            $urlFichier = Storage::url($path); // Génère une URL publique

            Fichier::create([
                'nom_fichier' => $validatedData['nom_fichier'],
                'description' => $validatedData['description'],
                'url_fichier' => $urlFichier,
                'id_stagiaire' => $idStagiaireProprietaire,
                'id_superviseur_televerseur' => $user->id, // Toujours l'utilisateur connecté
                // CORRECTION ICI: Si c'est un stagiaire, les permissions sont toujours true.
                // Si c'est un superviseur/admin, prendre la valeur booléenne de la requête,
                // qui est false si la case est décochée et donc absente de la requête.
                'peut_modifier' => $user->isStagiaire() ? true : $request->boolean('peut_modifier', false),
                'peut_supprimer' => $user->isStagiaire() ? true : $request->boolean('peut_supprimer', false),
                'type_fichier' => $validatedData['type_fichier'],
                'sujet_id' => $validatedData['sujet_id'],
            ]);

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
        $user = Auth::user();

        // Le stagiaire peut modifier s'il est le propriétaire et que la permission 'peut_modifier' est true
        // Le superviseur/admin peut modifier tous les fichiers
        if ($user->isStagiaire() && ($fichier->id_stagiaire !== $user->id || !$fichier->peut_modifier)) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce fichier.');
        } elseif (!$user->isSuperviseur() && !$user->isAdministrateur() && !$user->isStagiaire()) { // Fallback si le rôle n'est pas défini
            abort(403, 'Accès non autorisé.');
        }

        // Récupérer les sujets pour le formulaire
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

        // Vérification des permissions de modification (similaire à la méthode edit)
        // Les superviseurs/administrateurs peuvent toujours modifier.
        // Les stagiaires peuvent modifier si c'est leur fichier et qu'ils ont la permission.
        if ($user->isStagiaire() && ($fichier->id_stagiaire !== $user->id || !$fichier->peut_modifier)) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce fichier.');
        } elseif (!$user->isSuperviseur() && !$user->isAdministrateur() && !$user->isStagiaire()) {
            abort(403, 'Accès non autorisé.');
        }

        $stagiaireRoleId = Role::where('nom', 'Stagiaire')->value('id');

        try {
            $rules = [
                'nom_fichier' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'fichier' => 'nullable|file|max:10240', // Fichier optionnel lors de la mise à jour
                'type_fichier' => 'required|string|in:convention,rapport,attestation,autre',
                'sujet_id' => 'nullable|exists:sujets,id',
            ];

            // Le superviseur/admin peut modifier les permissions
            if ($user->isSuperviseur() || $user->isAdministrateur()) {
                $rules['peut_modifier'] = 'boolean';
                $rules['peut_supprimer'] = 'boolean';
            }

            $validatedData = $request->validate($rules);

            $dataToUpdate = [
                'nom_fichier' => $validatedData['nom_fichier'],
                'description' => $validatedData['description'],
                'type_fichier' => $validatedData['type_fichier'],
                'sujet_id' => $validatedData['sujet_id'],
            ];

            // Si un nouveau fichier est téléversé, mettez à jour le chemin
            if ($request->hasFile('fichier')) {
                // Supprimez l'ancien fichier s'il existe et si ce n'est pas une URL temporaire/erreur
                if ($fichier->url_fichier && Storage::exists(str_replace('/storage/', 'public/', $fichier->url_fichier))) {
                    Storage::delete(str_replace('/storage/', 'public/', $fichier->url_fichier));
                }
                $path = $request->file('fichier')->store('public/fichiers');
                $dataToUpdate['url_fichier'] = Storage::url($path);
            }

            // Mettez à jour les permissions si l'utilisateur est superviseur/admin
            // CORRECTION ICI: Utiliser 'boolean' sans valeur par défaut pour prendre la valeur réelle.
            if ($user->isSuperviseur() || $user->isAdministrateur()) {
                $dataToUpdate['peut_modifier'] = $request->boolean('peut_modifier', false); // Ajouté 'false' comme défaut
                $dataToUpdate['peut_supprimer'] = $request->boolean('peut_supprimer', false); // Ajouté 'false' comme défaut
            } else {
                 // Si c'est un stagiaire, il ne peut pas modifier ses propres permissions, elles sont fixées à 'true' pour ses propres fichiers
                 // C'est pourquoi ces champs sont cachés dans la vue 'edit' pour les stagiaires.
                 // Ne pas modifier les permissions si ce n'est pas un superviseur/admin.
                 // Ou si le stagiaire téléverse ses propres fichiers, les permissions sont toujours true.
                 // On ne devrait pas arriver ici si un stagiaire essaie de modifier les permissions,
                 // car les checkboxes ne sont pas affichées pour eux.
            }

            $fichier->update($dataToUpdate);

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

        // Vérification des permissions de suppression
        // Les superviseurs/administrateurs peuvent toujours supprimer.
        // Les stagiaires peuvent supprimer si c'est leur fichier et qu'ils ont la permission.
        if ($user->isStagiaire() && ($fichier->id_stagiaire !== $user->id || !$fichier->peut_supprimer)) {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer ce fichier.');
        } elseif (!$user->isSuperviseur() && !$user->isAdministrateur() && !$user->isStagiaire()) {
            abort(403, 'Accès non autorisé.');
        }

        try {
            // Supprimer le fichier du stockage
            if ($fichier->url_fichier && Storage::exists(str_replace('/storage/', 'public/', $fichier->url_fichier))) {
                Storage::delete(str_replace('/storage/', 'public/', $fichier->url_fichier));
            }

            // Supprimer l'entrée de la base de données
            $fichier->delete();

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
        // Vérification des permissions de téléchargement
        // Un stagiaire peut télécharger son propre fichier. Superviseur/Admin peuvent télécharger n'importe quel fichier.
        if ($user->id !== $fichier->id_stagiaire && !$user->isSuperviseur() && !$user->isAdministrateur()) {
            abort(403, 'Accès non autorisé au téléchargement de ce fichier.');
        }

        // Le chemin du fichier dans le système de stockage (transforme /storage/ vers public/)
        $filePath = str_replace('/storage/', 'public/', $fichier->url_fichier);

        if (Storage::exists($filePath)) {
            // Obtenir l'extension du fichier stocké (ex: 'pdf', 'jpg')
            $extension = pathinfo(Storage::path($filePath), PATHINFO_EXTENSION);
            
            // Construire le nom de fichier pour le téléchargement.
            // On prend le nom_fichier fourni par l'utilisateur et on ajoute l'extension réelle.
            // On s'assure que le nom ne contient pas déjà une extension pour éviter les doublons (ex: "rapport.pdf.pdf")
            $downloadFileName = preg_replace('/\.[^.]+$/', '', $fichier->nom_fichier); // Retire toute extension existante
            $downloadFileName .= '.' . $extension; // Ajoute l'extension correcte

            // Retourne le fichier pour téléchargement avec le nom corrigé
            return Storage::download($filePath, $downloadFileName);
        }

        abort(404, 'Fichier non trouvé.');
    }
}
