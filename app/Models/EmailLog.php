<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Assurez-vous que cette ligne est présente

class EmailLog extends Model
{
    use HasFactory;

    protected $table = 'email_logs';

    // Assurez-vous que 'user_id' est dans le tableau $fillable
    protected $fillable = ['to_email', 'subject', 'body', 'status', 'error_message', 'email_template_id', 'absence_id', 'user_id']; 

    /**
     * Obtenir l'utilisateur auquel appartient ce log d'e-mail.
     */
    public function user(): BelongsTo // Renommé 'user' (singulier) car un log appartient à UN utilisateur
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtenir l'absence associée à ce log d'e-mail.
     */
    public function absence()
    {
        return $this->belongsTo(Absence::class);
    }

    /**
     * Obtenir le template d'e-mail associé à ce log.
     */
    public function template()
    {
        // Si votre modèle est EmailTemplate, assurez-vous de l'importer ou de le référencer correctement.
        // Par exemple: use App\Models\EmailTemplate;
        return $this->belongsTo(Email_Template::class, 'email_template_id');
    }
}