<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PruebasMailable extends Mailable
{
    use Queueable, SerializesModels;
    
    public $mensaje;
    public $enlace, $tipo;
    /**
     * Create a new message instance.
     */
    public function __construct($mensaje,$enlace, $tipo)
    {
        $this->mensaje=$mensaje;
        $this->enlace=$enlace;
        $this->tipo=$tipo;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        return $this->view('emails.PruebasEmail')
        ->with([
            'mensaje' => $this->mensaje,
            'enlacePrueba' => $this->enlace,
            'tipo' => $this->tipo,
        ]);
    }

 
}
