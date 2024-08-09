<?php

namespace App\Mail;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CommandeStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $commande;
    public $status;

    public function __construct(Commande $commande, $status)
    {
        $this->commande = $commande;
        $this->status = $status;
    }

    public function build()
    {
        return $this->view('emails.commande_status_changed')
                    ->subject('Statut de votre commande mis à jour')
                    ->with([
                        'commande' => $this->commande,
                        'status' => $this->status,
                    ])
                    ->to('ibrohillb@gmail.com');
    }
}
