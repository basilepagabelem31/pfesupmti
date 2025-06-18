<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;

class EmailLogController extends Controller
{
    public function index()
    {
        // Récupérer les logs d'emails avec pagination
        $emailLogs = EmailLog::with(['absence.stagiaire', 'absence.reunion'])->latest()->paginate(15);

        return view('email_logs.index', compact('emailLogs'));
    }
}