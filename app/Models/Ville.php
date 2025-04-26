<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ville extends Model
{

    protected $fillable = ["code",'nom', 'pays_id'];

    public function utilisateurs()
{
    return $this->hasMany(Utilisateur::class);
}




    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }
}
