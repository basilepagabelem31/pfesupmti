<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use App\Models\User; // Assurez-vous d'importer le modèle User si nécessaire

class EmailLogController extends Controller
{
    public function index()
    {
        // Charge les relations 'user', 'template' et 'absence.stagiaire' (si le stagiaire est lié via absence)
        // Ceci est crucial pour que les informations du stagiaire soient disponibles dans la vue.
        $logs = EmailLog::with(['user', 'template', 'absence.stagiaire']) // Ajout de 'absence.stagiaire'
                         ->orderBy('created_at', 'desc')
                         ->paginate(5);
        
        return view('admin.email_logs.index', compact('logs'));
    }
}
