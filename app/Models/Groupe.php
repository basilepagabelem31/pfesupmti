<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{

    protected $fillable = ['code','nom','description','date']; // ou les champs que tu as définis

    public function utilisateurs()
{
    return $this->hasMany(Utilisateur::class);
}

}
