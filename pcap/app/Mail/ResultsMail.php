<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResultsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $results;

    public function __construct($results)
    {
        $this->results = $results;
    }

    public function build()
    {
        return $this->view('emails.results')
                    ->subject('Twoje wyniki samooceny');
    }
}
