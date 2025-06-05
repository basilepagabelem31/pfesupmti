<?php

namespace App\Models;

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

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function fichier()
    {
        return $this->belongsTo(Fichier::class);
    }

    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }
}
