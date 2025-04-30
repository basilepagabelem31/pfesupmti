<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;


class User extends Authenticatable
{
    use  HasFactory, Notifiable;


    protected static function booted()
    {
        static::creating(function ($admin){
            //generation d'un code aleatoire 
            $admin->code= Str::upper(Str::random(10));
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom', 'prenom', 'email', 'password', 'cin', 'code',
        'telephone', 'adresse', 'universite', 'faculte', 'titre_formation',
        'pays_id', 'ville_id', 'groupe_id', 'role_id', 'statut_id', 'email_log_id',
    ];

    // Relation avec Pays
    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    // Relation avec Ville
    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }

    // Relation avec Groupe
    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }

    // Relation avec Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Relation avec Statut
    public function statut()
    {
        return $this->belongsTo(Statut::class);
    }

    // Relation avec EmailLog
    public function emailLog()
    {
        return $this->belongsTo(Email_log::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function InternSujetPivot()
    {
        return $this -> belongsTo(InternSujetPivot::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
