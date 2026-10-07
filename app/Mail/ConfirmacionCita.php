<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class ConfirmacionCita extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre_cliente;
    public $fecha;
    public $hora;
    public $servicios;
    public $estilista;
    public $confirmUrl;

    public function __construct($nombre_cliente, $fecha, $hora, $servicios, $estilista, $confirmUrl)
    {
        $this->nombre_cliente = $nombre_cliente;
        $this->fecha = $fecha;
        $this->hora = $hora;
        $this->servicios = $servicios;
        $this->estilista = $estilista;
        $this->confirmUrl = $confirmUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('aulaclick593@gmail.com', 'StyleNow - Reservas'),
            subject: '📅 Detalles y Confirmación de tu Cita - StyleNow',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.confirmacion-cita',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}