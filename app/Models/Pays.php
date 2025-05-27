<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pays extends Model
{
    public function users()
{
    return $this->hasMany(User::class);
}




protected $fillable = ['code','nom']; // ou les champs que tu as définis

public function villes()
{
    return $this->hasMany(Ville::class);
}
}
