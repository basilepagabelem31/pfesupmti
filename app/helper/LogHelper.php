<?php

namespace App\helper; 

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LogHelper
{
    /**
     * Enregistre une entrée de log dans la base de données.
     *
     * @param string $titre Le titre ou la description de l'action.
     * @param string $object Le détail de l'objet ou de l'événement. (non nullable selon votre migration)
     * @param int|null $userId L'ID de l'utilisateur qui a effectué l'action. Par défaut, l'utilisateur authentifié.
     * @return Log
     */
    public static function logAction(string $titre, string $object, ?int $userId = null): Log
    {
        // Tente de récupérer l'ID de l'utilisateur authentifié si non fourni
        if (is_null($userId) && Auth::check()) {
            $userId = Auth::id();
        }

        return Log::create([
            'user_id' => $userId,
            'titre' => $titre,
            'object' => $object,
            'date' => now(), 
        ]);
    }
}