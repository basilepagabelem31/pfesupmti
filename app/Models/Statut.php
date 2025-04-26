<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statut extends Model
{

    protected $fillable = ['nom','description']; // ou les champs que tu as définis


    public function utilisateurs()
{
    return $this->hasMany(Utilisateur::class);
}

}
