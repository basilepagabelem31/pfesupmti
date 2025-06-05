<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sujet extends Model
{

    protected $fillable=['titre','description','promotion_id','groupe_id'];
    //
   // public function InternSujetPivot()
    //{
     //   return $this->belongsTo(InternSujetPivot::class);
    //}
    /**
    *User::class : c’est le modèle des stagiaires.
    *'intern_sujet_pivot' : c’est le nom de ta table pivot (adapte si le nom exact diffère).
    *'sujet_id' : clé étrangère côté sujet dans la table pivot.
    *'user_id' : clé étrangère côté user (stagiaire) dans la table pivot.
     */
    public function stagiaires()
    {
        return $this->belongsToMany(User::class, 'intern_sujet_pivots', 'sujet_id', 'intern_id');
    }

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