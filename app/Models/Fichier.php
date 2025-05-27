<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fichier extends Model
{
    //

    public function sujets()
    {
        return $this->hasMany(Sujet::class);
    }
}
