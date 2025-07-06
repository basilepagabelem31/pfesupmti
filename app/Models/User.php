<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany; // Reste si HasMany est utilisé ailleurs

// Assurez-vous que tous ces modèles sont bien importés !
use App\Models\Pays;
use App\Models\Ville;
use App\Models\Groupe;
use App\Models\Role;
use App\Models\EmailLog;
use App\Models\Sujet;
use App\Models\DemandeCoequipier;
use App\Models\Fichier; // Ceci est crucial pour la relation fichiersPossedes
use App\Models\Promotion;
use App\Models\Note; // Ceci est crucial pour la relation notes
use App\Models\Statut;
use App\Models\Absence;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $table = 'users';

    protected static function booted()
    {
        static::creating(function ($user) {
            $user->code = Str::upper(Str::random(10));
        });
    }

    protected $fillable = [
        'nom', 'prenom', 'email', 'password', 'cin', 'code',
        'telephone', 'adresse', 'universite', 'faculte', 'titre_formation',
        'pays_id', 'ville_id', 'groupe_id', 'role_id', 'statut_id','promotion_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // --- Relations existantes ---
    public function pays() { return $this->belongsTo(Pays::class); }
    public function ville() { return $this->belongsTo(Ville::class); }
    public function groupe() { return $this->belongsTo(Groupe::class); }
    public function role() { return $this->belongsTo(Role::class, 'role_id'); }
    public function emailLog() { return $this->belongsTo(EmailLog::class); }
    public function sujets() { return $this->belongsToMany(Sujet::class, 'sujet_user'); }

    /**
     * Obtenez les demandes de coéquipiers envoyées par cet utilisateur.
     */
    public function demandesEnvoyees()
    {
        return $this->hasMany(DemandeCoequipier::class, 'id_stagiaire_demandeur', 'id');
    }

    /**
     * Obtenez les demandes de coéquipiers reçues par cet utilisateur.
     */
    public function demandesRecues()
    {
        return $this->hasMany(DemandeCoequipier::class, 'id_stagiaire_receveur', 'id');
    }

    /**
     * Obtenez les coéquipiers de cet utilisateur.
     * Cette relation gère les paires où l'utilisateur est le 'stagiaire_1'.
     */
    public function coequipiersAsStagiaire1()
    {
        return $this->belongsToMany(User::class, 'coequipiers', 'id_stagiaire_1', 'id_stagiaire_2')
                     ->withPivot('date_association')
                     ->as('coequipier_data');
    }

    /**
     * Obtenez les coéquipiers de cet utilisateur.
     * Cette relation gère les paires où l'utilisateur est le 'stagiaire_2'.
     */
    public function coequipiersAsStagiaire2()
    {
        return $this->belongsToMany(User::class, 'coequipiers', 'id_stagiaire_2', 'id_stagiaire_1')
                     ->withPivot('date_association')
                     ->as('coequipier_data');
    }

    /**
     * Obtenez tous les coéquipiers de cet utilisateur (en combinant les deux côtés de la relation).
     * Utilisez cette méthode pour récupérer tous les coéquipiers sans vous soucier de leur position (1 ou 2) dans la table pivot.
     */
    public function getAllCoequipiers()
    {
        return $this->coequipiersAsStagiaire1->merge($this->coequipiersAsStagiaire2);
    }

    // --- RELATIONS POUR LES FICHIERS ---
    /**
     * Relation : Un utilisateur peut posséder plusieurs fichiers (en tant que stagiaire propriétaire).
     */
    public function fichiersPossedes()
    {
        return $this->hasMany(Fichier::class, 'id_stagiaire');
    }

    /**
     * Relation : Un utilisateur peut avoir téléversé plusieurs fichiers (en tant que téléverseur).
     */
    public function fichiersTeleverses()
    {
        return $this->hasMany(Fichier::class, 'id_superviseur_televerseur');
    }

    // --- FIN DES RELATIONS POUR LES FICHIERS ---

    // Méthodes pour vérifier le rôle de l'utilisateur
    public function hasRole($role)
    {
        return $this->role && $this->role->nom == $role;
    }

    
    /**
     * Vérifie si l'utilisateur est un administrateur.
     * Renommée de isSuperAdmin() pour correspondre au rôle 'Administrateur'.
     */
    public function isAdministrateur()
    {
        return $this->hasRole('Administrateur');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function isSuperviseur()
    {
        return $this->hasRole('Superviseur');
    }

    public function isStagiaire()
    {
        return $this->hasRole('Stagiaire');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'stagiaire_id');
    }

    // GESTION DES REUNIONS ET ABSENCES
    public function statut()
    {
        return $this->belongsTo(Statut::class, 'statut_id');
    }

    public function isActive(): bool
    {
        return $this->statut && $this->statut->nom === 'Actif';
    }

    public function absences()
    {
        return $this->hasMany(Absence::class, 'stagiaire_id');
    }
}
