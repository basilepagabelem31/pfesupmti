<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reunion extends Model
{
    use HasFactory;

    protected $fillable=['date','heure_debut','heure_fin','status','note','groupe_id'];

    protected $casts=[
        'date' => 'date',
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
        'status' => 'boolean',
    ];

    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }
}

