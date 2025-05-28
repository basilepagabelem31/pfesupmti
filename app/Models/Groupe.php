<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{

   protected $fillable = ['code', 'nom', 'jour', 'heure_debut', 'heure_fin', 'description'];


    public function users()
{
    return $this->hasMany(User::class);
}


    public function reunions()
    {
        return $this->hasMany(Reunion::class);
    }

    public function sujets()
    {
        return $this->hasMany(Sujet::class);
    }
}
