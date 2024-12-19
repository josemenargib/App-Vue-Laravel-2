<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactosFormMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $mensaje, $email, $nombres, $apellidos;

    /**
     * Create a new message instance.
     */
    public function __construct($mensaje, $email, $nombres, $apellidos)
    {
        $this->mensaje=$mensaje;
        $this->email = $email;
        $this->nombres=$nombres;
        $this->apellidos = $apellidos;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
{
    return new Envelope(
        from: new Address(env('MAIL_FROM_ADDRESS'), 'Web HAMILO - Contactos'),
        replyTo: [
            new Address($this->email, $this->email),
        ],
        subject: 'Web HAMILO - Contactos',
    );
}

    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->view('emails.Contactos')
        ->with([
            'mensaje' => $this->mensaje,
            'email' => $this->email,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
        ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}