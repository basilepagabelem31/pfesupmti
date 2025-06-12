<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    //
    use HasFactory;

    protected $fillable=['statut','note','stagiaire_id','reunion_id','valide_par'];

    public function reunion()
    {
        return $this->belongsTo(Reunion::class);
    }

    public function stagiaire()
    {
        return $this->belongsTo(User::class,'stagiaire_id');
    }

   // Relation vers le validateur (utilisateur ayant validé l'absence)
    public function valideur()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }
}
