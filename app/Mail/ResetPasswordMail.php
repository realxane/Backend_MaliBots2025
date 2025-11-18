<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function build()
    {
        return $this->subject('Réinitialisation de votre mot de passe - Anw Ka Dembe')
                    ->view('emails.password_reset')
                    ->with(['url' => $this->url]);
    }
}
