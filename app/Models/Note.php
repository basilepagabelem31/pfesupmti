<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
  use HasFactory ;

  protected $fillable=['valeur','visibilite','date_note','stagiaire_id','donneur_id'];

  protected $dates=['date_note'];

  public function stagiaire()
    {
        return $this->belongsTo(User::class, 'stagiaire_id');
    }

    public function donneur()
    {
        return $this->belongsTo(User::class, 'donneur_id');
    }
}
