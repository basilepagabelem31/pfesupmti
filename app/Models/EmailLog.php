<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class EmailLog extends Model // Renommé EmailLog
{
    use HasFactory;
    protected $table = 'email_logs'; // Spécifier le nom de la table si le nom du modèle ne suit pas la convention plurielle

    protected $fillable = ['to_email','subject','body','status','error_message','email_template_id']; // Champs ajustés
    public function users() { return $this->hasMany(User::class); } // Si un log peut avoir plusieurs users, ou user_id ici.
                                                                  // L'inverse est plus commun: user a un email_log_id (nullable)
    public function emailTemplate()
    {
        return $this->belongsTo(EmailTemplate::class); // Note: EmailTemplate
    }
}