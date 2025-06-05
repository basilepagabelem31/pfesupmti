<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fichier extends Model
{
    use HasFactory;

    protected $table = 'fichiers'; // Assurez-vous que le nom de la table est correct

    protected $fillable = [
        'nom_fichier',
        'description',
        'url_fichier',
        'id_stagiaire', // Clé étrangère vers le stagiaire propriétaire
        'id_superviseur_televerseur', // Clé étrangère vers l'utilisateur qui a téléversé
        'peut_modifier',
        'peut_supprimer',
        'type_fichier', // 'convention', 'rapport', 'attestation', etc.
        'sujet_id', // Si le fichier est lié à un sujet
    ];

    protected $casts = [
        'peut_modifier' => 'boolean',
        'peut_supprimer' => 'boolean',
        'created_at' => 'datetime', // Pour que Carbon gère facilement la date de téléversement
        'updated_at' => 'datetime',
    ];

    /**
     * Relation : Un fichier appartient à un stagiaire (utilisateur).
     */
    public function stagiaire()
    {
        return $this->belongsTo(User::class, 'id_stagiaire');
    }

    /**
     * Relation : Un fichier a été téléversé par un utilisateur (superviseur ou stagiaire lui-même).
     */
    public function televerseur()
    {
        return $this->belongsTo(User::class, 'id_superviseur_televerseur');
    }

    /**
     * Relation : Un fichier peut être associé à un sujet.
     */
    public function sujet()
    {
        return $this->belongsTo(Sujet::class); // Assurez-vous d'avoir un modèle Sujet
    }
}