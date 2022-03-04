<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ErrorAlert extends Mailable
{
    use Queueable, SerializesModels;
    public $alert;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $alert)
    {
        $this->alert = $alert;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('ogs@bcdmail.fr')
            ->view('emails.alert');
    }
}
