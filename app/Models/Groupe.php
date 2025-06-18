<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    use HasFactory;

    // Assurez-vous que ces champs correspondent EXACTEMENT à vos colonnes de migration.
    // J'ai mis à jour 'date' en 'jour', 'heure_debut', 'heure_fin'
    protected $fillable = [
        'code',
        'nom',
        'description',
            // Le champ 'heure_fin' de votre migration
    ];

    /**
     * Obtenez les stagiaires (utilisateurs avec le rôle 'Stagiaire') associés à ce groupe.
     * Cette relation est utilisée pour vérifier si un groupe contient des stagiaires avant la suppression.
     */
    public function stagiaires()
    {
        // Tente de trouver l'ID du rôle 'Stagiaire'.
        // C'est une bonne pratique de ne pas hardcoder l'ID ici.
        // Assurez-vous que votre table 'roles' contient bien un rôle nommé 'Stagiaire'.
        $stagiaireRole = Role::where('nom', 'Stagiaire')->first();

        // Si le rôle 'Stagiaire' n'est pas trouvé, la relation retournera une requête qui ne matchera rien
        // ou vous pouvez gérer cette erreur de manière plus robuste si nécessaire.
        if ($stagiaireRole) {
            return $this->hasMany(User::class, 'groupe_id')->where('role_id', $stagiaireRole->id);
        }

        // Retourne une requête qui ne matchera aucun utilisateur si le rôle 'Stagiaire' n'est pas trouvé
        // Cela évitera des erreurs si le rôle n'existe pas encore ou a un nom différent.
        return $this->hasMany(User::class, 'groupe_id')->whereNull('role_id');
    }

    /**
     * Obtenez les réunions associées à ce groupe.
     */
    public function reunions()
    {
        return $this->hasMany(Reunion::class);
    }

    /**
     * Obtenez les sujets associés à ce groupe.
     * (Vérifiez la relation : si c'est Many-to-Many via 'intern_sujet_pivots', ce devrait être belongsToMany)
     */
    public function sujets()
    {
        // Si un sujet est directement lié à un groupe (One-to-Many : un sujet appartient à un groupe),
        // alors hasMany est correct.
        // Si un sujet peut être associé à PLUSIEURS groupes (Many-to-Many),
        // alors il faudrait une relation belongsToMany via une table pivot.
        // D'après votre migration 'sujets' qui a un 'groupe_id', c'est bien un hasMany ici.
        return $this->hasMany(Sujet::class);
    }
}