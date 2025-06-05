<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Si vous utilisez des modèles pour des tables pivots avec des colonnes supplémentaires
// et que vous voulez tirer parti des fonctionnalités Eloquent pour le pivot,
// vous pouvez étendre Illuminate\Database\Eloquent\Relations\Pivot
// Mais pour des relations simples avec belongsToMany, Model suffit.
// use Illuminate\Database\Eloquent\Relations\Pivot;

class Coequipier extends Model
{
    use HasFactory;

    protected $table = 'coequipiers';

    // Indique à Eloquent que la table n'a pas de clé primaire auto-incrémentée par défaut
    public $incrementing = false;

    // Indique les noms des colonnes de la clé primaire composite
    protected $primaryKey = ['id_stagiaire_1', 'id_stagiaire_2'];

    protected $fillable = [
        'id_stagiaire_1',
        'id_stagiaire_2',
        'date_association',
    ];

    // Indique à Eloquent que 'date_association' est une date
    protected $casts = [
        'date_association' => 'date',
    ];

    /**
     * Obtenez le premier stagiaire de la paire.
     */
    public function stagiaire1()
    {
        return $this->belongsTo(User::class, 'id_stagiaire_1', 'id');
    }

    /**
     * Obtenez le deuxième stagiaire de la paire.
     */
    public function stagiaire2()
    {
        return $this->belongsTo(User::class, 'id_stagiaire_2', 'id');
    }
}