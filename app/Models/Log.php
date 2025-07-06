<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    use HasFactory;

    // Spécifiez le nom de la table si votre modèle ne suit pas la convention de nommage de Laravel (ex: 'logs' pour le modèle 'Log')
    protected $table = 'logs';

    // Les attributs qui peuvent être massivement assignés
    protected $fillable = [
        'user_id',
        'action',
        'log_message',
        'object_snapshot',
    ];

    // Les attributs qui doivent être castés à des types de données spécifiques
    protected $casts = [
        'object_snapshot' => 'array', // Pour stocker des JSON
    ];

    /**
     * Obtenir l'utilisateur qui a effectué ce log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}