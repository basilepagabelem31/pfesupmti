<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    //
    use HasFactory;

    public function reunion()
    {
        return $this->belongsTo(Reunion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
