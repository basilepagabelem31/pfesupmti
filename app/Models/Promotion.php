<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Promotion extends Model
{
    //
    protected $fillable=['titre','status'];

    public function sujets()
    {
        return $this->hasMany(Sujet::class);
    }


public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
