<?php

namespace App\Http\Controllers;

use App\Models\Fichier;
use App\Models\User; // Pour récupérer les stagiaires si le superviseur téléverse pour eux
use App\Models\Sujet; // Si les fichiers peuvent être liés à des sujets
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Pour gérer le stockage des fichiers

class FichierController extends Controller
{
    /**
     * Affiche la liste des fichiers pour un stagiaire spécifique (pour superviseur/admin)
     * ou les propres fichiers du stagiaire connecté.
     * @param int|null $stagiaireId L'ID du stagiaire pour filtrer les fichiers.
     * @return \Illuminate\View\View
     */
    public function index(Request $request, User $stagiaire = null)
    {
        $user = Auth::user();

        if ($user->isStagiaire()) { 
            // Un stagiaire ne voit que ses propres fichiers
            $fichiers = $user->fichiersPossedes()->with('televerseur', 'sujet')->latest()->get();
            return view('fichiers.index_stagiaire', compact('fichiers'));
        } elseif ($user->isSuperviseur() || $user->isAdministrateur()) {
            // Superviseur/Admin peuvent voir tous les fichiers ou filtrer par stagiaire
            if ($stagiaire && $stagiaire->isStagiaire()) {
                $fichiers = $stagiaire->fichiersPossedes()->with('televerseur', 'sujet')->latest()->get();
                $currentStagiaire = $stagiaire; // Pour afficher le nom du stagiaire
            } else {
                $fichiers = Fichier::with('stagiaire', 'televerseur', 'sujet')->latest()->get();
                $currentStagiaire = null;
            }
            $stagiaires = User::whereHas('role', function ($query) {
                $query->where('id', 3); // L'ID du rôle Stagiaire
            })->get(); // Pour le filtre des superviseurs/admins

            return view('fichiers.index_superviseur', compact('fichiers', 'stagiaires', 'currentStagiaire'));
        }

        abort(403, 'Accès non autorisé.');
    }

    /**
     * Affiche le formulaire de téléversement de fichier.
     * Peut être pour un stagiaire (il téléverse pour lui-même) ou un superviseur (il téléverse pour un stagiaire).
     * @return \Illuminate\View\View
     */
    public function create(User $stagiaire = null)
    {
        $user = Auth::user();
        $stagiaires = null;
        $sujets = null; // Si vous avez un modèle Sujet et une table de sujets

        if ($user->isSuperviseur() || $user->isAdministrateur()) {
            // Le superviseur/admin peut choisir pour quel stagiaire téléverser
            $stagiaires = User::whereHas('role', function ($query) {
                $query->where('id', 3); // L'ID du rôle Stagiaire
            })->get();
            $sujets = Sujet::all(); // Récupère tous les sujets disponibles
        } elseif ($user->isStagiaire()) {
            // Le stagiaire téléverse pour lui-même, pas besoin de choisir de stagiaire
            $stagiaire = $user;
            $sujets = Sujet::all(); // Le stagiaire peut lier son fichier à un sujet
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

        $request->validate($rules);

        // Déterminer l'ID du stagiaire propriétaire du fichier
        $idStagiaireProprietaire = $user->isStagiaire() ? $user->id : $request->input('id_stagiaire');

        // Gérer le téléversement du fichier
        $path = $request->file('fichier')->store('public/fichiers'); // Stocke dans storage/app/public/fichiers
        $urlFichier = Storage::url($path); // Génère une URL publique

        Fichier::create([
            'nom_fichier' => $request->input('nom_fichier'),
            'description' => $request->input('description'),
            'url_fichier' => $urlFichier,
            'id_stagiaire' => $idStagiaireProprietaire,
            'id_superviseur_televerseur' => $user->id, // Toujours l'utilisateur connecté
            'peut_modifier' => $user->isStagiaire() ? true : $request->boolean('peut_modifier', true), // Stagiaire par défaut true, superviseur peut définir
            'peut_supprimer' => $user->isStagiaire() ? true : $request->boolean('peut_supprimer', true), // Stagiaire par défaut true, superviseur peut définir
            'type_fichier' => $request->input('type_fichier'),
            'sujet_id' => $request->input('sujet_id'),
        ]);

        return redirect()->route('fichiers.index')->with('success', 'Fichier téléversé avec succès !');
    }

    /**
     * Affiche le formulaire d'édition d'un fichier.
     * @param Fichier $fichier
     * @return \Illuminate\View\View
     */
    public function edit(Fichier $fichier)
    {
        $user = Auth::user();

        // Le stagiaire peut modifier s'il a la permission 'peut_modifier'
        // Le superviseur/admin peut modifier tous les fichiers
        if ($user->isStagiaire() && ($fichier->id_stagiaire !== $user->id || !$fichier->peut_modifier)) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce fichier.');
        } elseif (!$user->isSuperviseur() && !$user->isAdministrateur() && !$user->isStagiaire()) {
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

        // Vérification des permissions de modification
        if ($user->isStagiaire() && ($fichier->id_stagiaire !== $user->id || !$fichier->peut_modifier)) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce fichier.');
        } elseif (!$user->isSuperviseur() && !$user->isAdministrateur() && !$user->isStagiaire()) {
            abort(403, 'Accès non autorisé.');
        }

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

        $request->validate($rules);

        $data = [
            'nom_fichier' => $request->input('nom_fichier'),
            'description' => $request->input('description'),
            'type_fichier' => $request->input('type_fichier'),
            'sujet_id' => $request->input('sujet_id'),
        ];

        // Si un nouveau fichier est téléversé, mettez à jour le chemin
        if ($request->hasFile('fichier')) {
            // Supprimez l'ancien fichier si nécessaire
            Storage::delete(str_replace('/storage/', 'public/', $fichier->url_fichier));
            $path = $request->file('fichier')->store('public/fichiers');
            $data['url_fichier'] = Storage::url($path);
        }

        // Mettez à jour les permissions si l'utilisateur est superviseur/admin
        if ($user->isSuperviseur() || $user->isAdministrateur()) {
            $data['peut_modifier'] = $request->boolean('peut_modifier');
            $data['peut_supprimer'] = $request->boolean('peut_supprimer');
        }

        $fichier->update($data);

        return redirect()->route('fichiers.index')->with('success', 'Fichier mis à jour avec succès !');
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
        if ($user->isStagiaire() && ($fichier->id_stagiaire !== $user->id || !$fichier->peut_supprimer)) {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer ce fichier.');
        } elseif (!$user->isSuperviseur() && !$user->isAdministrateur() && !$user->isStagiaire()) {
            abort(403, 'Accès non autorisé.');
        }

        // Supprimer le fichier du stockage
        Storage::delete(str_replace('/storage/', 'public/', $fichier->url_fichier));

        // Supprimer l'entrée de la base de données
        $fichier->delete();

        return redirect()->route('fichiers.index')->with('success', 'Fichier supprimé avec succès !');
    }

    /**
     * Permet de télécharger un fichier.
     * @param Fichier $fichier
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(Fichier $fichier)
    {
        // Optionnel: Ajouter une logique de vérification de permission avant le téléchargement
        // Par exemple, seuls le propriétaire ou les superviseurs peuvent télécharger.
        $user = Auth::user();
        if ($user->id !== $fichier->id_stagiaire && !$user->isSuperviseur() && !$user->isAdministrateur()) {
            abort(403, 'Accès non autorisé au téléchargement de ce fichier.');
        }

        // Le chemin du fichier dans le système de stockage
        $filePath = str_replace('/storage/', 'public/', $fichier->url_fichier);

        // Vérifier si le fichier existe
        if (Storage::exists($filePath)) {
            // Le nom du fichier à télécharger (utiliser le nom_fichier original)
            return Storage::download($filePath, $fichier->nom_fichier);
        }

        abort(404, 'Fichier non trouvé.');
    }
}