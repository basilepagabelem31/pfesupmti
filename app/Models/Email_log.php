<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email_log extends Model
{

    protected $fillable = ['subject','content','statut','date','email_template_id']; // ou les champs que tu as définis

    public function utilisateurs()
{
    return $this->hasMany(Utilisateur::class);
}


public function emailTemplate()
{
    return $this->belongsTo(Email_template::class);
}
}
