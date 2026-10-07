<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class RecuperacionPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre_usuario;
    public $passwordTemporal;
    public $loginUrl;
    public $cedula;

    public function __construct($nombre_usuario, $passwordTemporal, $loginUrl, $cedula = null)
    {
        $this->nombre_usuario = $nombre_usuario;
        $this->passwordTemporal = $passwordTemporal;
        $this->loginUrl = $loginUrl;
        $this->cedula = $cedula;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('aulaclick593@gmail.com', 'StyleNow - Sistema de Gestión'),
            replyTo: [new Address('no-reply@stylenow.com', 'StyleNow No-Reply')],
            subject: '🔐 Recuperación de Contraseña - StyleNow',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recuperacion-password',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}