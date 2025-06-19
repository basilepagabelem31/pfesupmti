<?php

namespace App\Services;

use App\Models\Email_settings;
use App\Models\Email_template;
use App\Models\EmailLog;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class AbsenceEmailService 
{
    public function sendAbsenceEmail($stagiaire,$reunion,$consecutive)
    {
            // 1. Déterminer le type de template
            if ($consecutive == 1) $type = 'absence_simple';
            elseif ($consecutive == 2) $type = 'absence_double';
            //elseif ($consecutive >= 3) $type = 'absence_triple';
            else return;

            $template=Email_template::where('type',$type)->first();
            // 2. Config SMTP dynamique
            $settings = Email_settings::first();
            if($settings){
                Config::set('mail.mailers.smtp.host', $settings->host);
                Config::set('mail.mailers.smtp.port', $settings->port);
                Config::set('mail.mailers.smtp.username', $settings->username);
                Config::set('mail.mailers.smtp.password', Crypt::decryptString($settings->password));
                Config::set('mail.from.address', $settings->from_address);
                Config::set('mail.from.name', $settings->from_name);
                Config::set('mail.mailers.smtp.encryption', $settings->encryption);

            }

            // 3. Variables dynamiques
            $vars = [
                '{{nom}}' => $stagiaire->nom,
                '{{prenom}}' => $stagiaire->prenom,
                '{{email}}' => $stagiaire->email,
                '{{date_reunion}}' => $reunion->date->format('d/m/Y'),
                '{{heure_debut}}' => $reunion->heure_debut,
                '{{heure_fin}}' => $reunion->heure_fin,
            ];
            $subject = strtr($template->subject, $vars);
            $content = strtr($template->content, $vars);
            // 4. Envoi et log
            try {
                Mail::send([], [], function ($message) use ($stagiaire, $subject, $content) {
                    $message->to($stagiaire->email)
                            ->subject($subject)
                             ->html($content);
                });

                EmailLog::create([
                    'user_id' => $stagiaire->id,
                    'email_template_id' => $template->id,
                    'to_email' => $stagiaire->email,
                    'subject' => $subject,
                    'body' => $content,
                    'status' => 'sent',
                ]);
            } catch (\Exception $e) {
                EmailLog::create([
                    'user_id' => $stagiaire->id,
                    'email_template_id' => $template->id,
                    'to_email' => $stagiaire->email,
                    'subject' => $subject,
                    'body' => $content,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
    } 
    
    // AJOUT : Fonction pour email abandon en cas de trois absences successive 
    public function sendAbandonEmail($stagiaire, $reunion)
    {
        $template = Email_template::where('type', 'abandon')->first();
        if (!$template) return;

        $settings = Email_settings::first();
        if($settings){
            Config::set('mail.mailers.smtp.host', $settings->host);
            Config::set('mail.mailers.smtp.port', $settings->port);
            Config::set('mail.mailers.smtp.username', $settings->username);
            Config::set('mail.mailers.smtp.password', Crypt::decryptString($settings->password));
            Config::set('mail.from.address', $settings->from_address);
            Config::set('mail.from.name', $settings->from_name);
            Config::set('mail.mailers.smtp.encryption', $settings->encryption);
        }

        $vars = [
            '{{nom}}' => $stagiaire->nom,
            '{{prenom}}' => $stagiaire->prenom,
            '{{email}}' => $stagiaire->email,
            '{{date_reunion}}' => $reunion->date->format('d/m/Y'),
            '{{heure_debut}}' => $reunion->heure_debut,
            '{{heure_fin}}' => $reunion->heure_fin,
        ];
        $subject = strtr($template->subject, $vars);
        $content = strtr($template->content, $vars);

        try {
            Mail::send([], [], function ($message) use ($stagiaire, $subject, $content) {
                $message->to($stagiaire->email)
                        ->subject($subject)
                        ->html($content);
            });

            EmailLog::create([
                'user_id' => $stagiaire->id,
                'email_template_id' => $template->id,
                'to_email' => $stagiaire->email,
                'subject' => $subject,
                'body' => $content,
                'status' => 'sent',
            ]);
        } catch (\Exception $e) {
            EmailLog::create([
                'user_id' => $stagiaire->id,
                'email_template_id' => $template->id,
                'to_email' => $stagiaire->email,
                'subject' => $subject,
                'body' => $content,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
