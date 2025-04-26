<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternSujetPivot extends Model
{
    //
    protected $table = 'intern_sujet_pivots';

    public function sujets()
    {
        return $this->hasMany(Sujet::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

}
