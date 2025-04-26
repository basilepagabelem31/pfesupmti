<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{


    protected $fillable = ['code','nom','description']; // ou les champs que tu as définis


    public function utilisateurs()
{
    return $this->hasMany(Utilisateur::class);
}

}
