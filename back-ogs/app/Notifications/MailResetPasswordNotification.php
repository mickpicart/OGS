<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword;

class MailResetPasswordNotification extends ResetPassword
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(String $token)
    {
        parent::__construct($token);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */

    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Customize toMail method with french message for reset password email sent to user
        $link = url("http://localhost:8080/reset-password/".$this->token);
        return (new MailMessage)
            ->greeting("Bonjour,")
            ->subject("Mise à jour de votre mot de passe")
            ->line("Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour ce compte.")
            ->action('Réinitialisation du mot de passe', $link)
            ->line("Ne tardez pas, car ce lien expire dans ".config('auth.passwords.users.expire')." minutes !")
            ->line("Si vous n'êtes pas à l'origine de cette demande, aucune action de votre part n'est requise.")
            ->salutation("Cordialement, OGS");
    }

    /**
    * Get the array representation of the notification.
    *
    * @param  mixed  $notifiable
    * @return array
    */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
