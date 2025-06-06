<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sujet extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'promotion_id',
        'groupe_id',
    ];

    /**
     * Obtenez la promotion à laquelle ce sujet appartient.
     */
    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    /**
     * Obtenez le groupe auquel ce sujet est associé.
     */
    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }

    /**
     * Obtenez les stagiaires qui sont inscrits à ce sujet via la table pivot 'sujet_user'.
     */
    public function stagiaires()
    {
        // Utilise belongsToMany pour la relation Many-to-Many
        // 'User::class' est le modèle lié
        // 'sujet_user' est le nom de la table pivot
        return $this->belongsToMany(User::class, 'sujet_user')
                    // Ajoute une contrainte pour s'assurer que seuls les utilisateurs
                    // ayant le rôle 'Stagiaire' sont récupérés.
                    ->whereHas('role', function ($query) {
                        $query->where('nom', 'Stagiaire');
                    });
    }
}