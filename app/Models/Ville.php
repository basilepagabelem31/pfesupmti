<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ville extends Model
{

    protected $fillable = ["code",'nom', 'pays_id'];

    public function users()
{
    return $this->hasMany(User::class);
}




    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }
}
