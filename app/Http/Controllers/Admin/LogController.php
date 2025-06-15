<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        // DD 1: Vérifier ce que le contrôleur reçoit du formulaire
        // Si vous tapez "Suppression" dans le champ de recherche, vous devriez voir ['search' => 'Suppression'] ici.
        // dd($request->all());

        $query = Log::query();

        // Filtre par recherche (titre ou objet)
        if ($search = $request->input('search')) {
            // DD 2: Vérifier la valeur de $search et le type
            // dd('Recherche: ' . $search, 'Type: ' . gettype($search));

            $query->where(function($q) use ($search) {
                // C'est cette partie qui ne fonctionne pas
                $q->where('titre', 'like', '%' . $search . '%') // Vérifiez bien 'titre'
                  ->orWhere('object', 'like', '%' . $search . '%');
            });
        }

        // Filtre par date exacte
        if ($startDate = $request->input('start_date')) {
            // Cette partie fonctionne déjà
            $query->whereDate('date', $startDate);
        }

        // DD 3: Pour voir la requête SQL construite par Laravel
        // Ce dd() DOIT être placé juste avant ->paginate()
        // Il vous montrera la requête SQL et les bindings (valeurs des WHERE)
        // dd($query->toSql(), $query->getBindings());

        $logs = $query->orderBy('date', 'desc')->paginate(10);

        return view('admin.logs.index', compact('logs'));
    }
}