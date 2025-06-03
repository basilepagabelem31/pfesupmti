<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    //
    protected $fillable=['titre','status'];

    public function sujets()
    {
        return $this->hasMany(Sujet::class);
    }
}
