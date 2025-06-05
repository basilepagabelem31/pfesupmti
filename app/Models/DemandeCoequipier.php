<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeCoequipier extends Model
{
    use HasFactory;

    // Spécifie le nom de la table si elle ne suit pas les conventions de Laravel
    protected $table = 'demande_coequipiers';

    // Spécifie le nom de la clé primaire si elle n'est pas 'id'
    protected $primaryKey = 'id_demande';

    protected $fillable = [
        'id_stagiaire_demandeur',
        'id_stagiaire_receveur',
        'statut_demande',
        'date_demande',
    ];

    // Indique à Eloquent que 'date_demande' est une date/heure et doit être castée
    protected $casts = [
        'date_demande' => 'datetime',
    ];

    /**
     * Obtenez le stagiaire qui a envoyé cette demande.
     */
    public function demandeur()
    {
        return $this->belongsTo(User::class, 'id_stagiaire_demandeur', 'id');
    }

    /**
     * Obtenez le stagiaire qui a reçu cette demande.
     */
    public function receveur()
    {
        return $this->belongsTo(User::class, 'id_stagiaire_receveur', 'id');
    }
}