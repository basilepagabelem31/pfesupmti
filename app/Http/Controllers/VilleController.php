<?php

namespace App\Http\Controllers;

use App\Models\Ville;
use App\Models\Pays;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache; // Assurez-vous que le cache est configuré

class VilleController extends Controller
{
    /**
     * Récupère les villes associées à un pays donné.
     * Utilisée par les requêtes AJAX pour remplir les selects dynamiques.
     *
     * @param int $pays_id L'ID du pays.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVilles($pays_id)
    {
        try {
            // Clé de cache unique pour ce pays_id
            $cacheKey = "villes.pays.{$pays_id}";

            // Tente de récupérer les villes du cache, sinon les charge de la DB et les met en cache pour 24 heures (1440 minutes)
            $villes = Cache::remember($cacheKey, 1440, function() use ($pays_id) {
                return Ville::where('pays_id', $pays_id)
                            ->orderBy('nom')
                            ->get(['id', 'nom']); // S'assurer de ne sélectionner que 'id' et 'nom'
            });

            return response()->json($villes);

        } catch (\Exception $e) {
            // En cas d'erreur (ex: problème de base de données, cache non configuré),
            // retourner un statut 500 avec un message détaillé.
            // Cela sera visible dans la console du navigateur (onglet "Network" et "Console").
            return response()->json(['error' => 'Erreur serveur lors du chargement des villes: ' . $e->getMessage()], 500);
        }
    }

    // Afficher toutes les villes (méthode existante)
    public function index(Request $request)
    {
        $query = Ville::with('pays');

        if ($search = $request->input('search')) {
            $query->where('nom', 'like', "%$search%");
        }

        if ($pays_id = $request->input('pays_id')) {
            $query->where('pays_id', $pays_id);
        }

        $villes = $query->paginate(10)->withQueryString();
        $pays = Pays::orderBy('nom')->get();

        return view('villes.index', compact('villes', 'pays'));
    }

    // Afficher le formulaire d'ajout d'une ville (méthode existante)
    public function create()
    {
        $pays = Pays::all();
        return view('villes.create', compact('pays'));
    }

    // Enregistrer une nouvelle ville (méthode existante)
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'pays_id' => 'required|exists:pays,id',
            'codeville' => 'nullable|string|max:10',
        ]);

        $code = $request->code ?? strtoupper(substr($request->nom, 0, 3));

        Ville::create([
            'code' => $code,
            'nom' => $request->nom,
            'pays_id' => $request->pays_id,
        ]);

        return redirect()->route('villes.index')->with('success', 'Ville ajoutée avec succès!');
    }

    // Afficher le formulaire de modification (méthode existante)
    public function edit($id)
    {
        $ville = Ville::findOrFail($id);
        $pays = Pays::all();
        return view('villes.edit', compact('ville', 'pays'));
    }

    // Mettre à jour une ville (méthode existante)
    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'pays_id' => 'required|exists:pays,id',
            'code' => 'nullable|string|max:10',
        ]);

        $ville = Ville::findOrFail($id);

        $code = $request->code ?? strtoupper(substr($request->nom, 0, 3));

        $ville->update([
            'code' => $code,
            'nom' => $request->nom,
            'pays_id' => $request->pays_id,
        ]);

        return redirect()->route('villes.index')->with('success', 'Ville mise à jour avec succès!');
    }

    // Supprimer une ville (méthode existante)
    public function destroy($id)
    {
        $ville = Ville::findOrFail($id);
        $ville->delete();

        return redirect()->route('villes.index')->with('success', 'Ville supprimée avec succès!');
    }
}