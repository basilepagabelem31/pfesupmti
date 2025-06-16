<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email_settings extends Model
{
     protected $table = 'email_settings'; 
   protected $fillable=['protocole','host','port','username','password','from_address','from_name','encryption'];
}
