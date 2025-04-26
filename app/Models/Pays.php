<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pays extends Model
{
    public function utilisateurs()
{
    return $this->hasMany(Utilisateur::class);
}




protected $fillable = ['code','nom']; // ou les champs que tu as définis

public function villes()
{
    return $this->hasMany(Ville::class);
}
}
