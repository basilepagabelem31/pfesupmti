<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email_template extends Model
{
    protected $fillable = ['subject','content','description']; // ou les champs que tu as définis
    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    } 

}
