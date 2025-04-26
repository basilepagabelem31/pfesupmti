<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
    protected $fillable = [
        'nom', 'prenom', 'email', 'password', 'cin', 'code',
        'telephone', 'adresse', 'universite', 'faculte', 'titre_formation',
        'pays_id', 'ville_id', 'groupe_id', 'role_id', 'statut_id', 'email_log_id',
    ];

    // Relation avec Pays
    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    // Relation avec Ville
    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }

    // Relation avec Groupe
    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }

    // Relation avec Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Relation avec Statut
    public function statut()
    {
        return $this->belongsTo(Statut::class);
    }

    // Relation avec EmailLog
    public function emailLog()
    {
        return $this->belongsTo(Email_log::class);
    }
}
