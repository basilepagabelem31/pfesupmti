<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sujet extends Model
{
    //
    public function InternSujetPivot()
    {
        return $this->belongsTo(InternSujetPivot::class);
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
