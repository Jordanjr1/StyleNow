<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class RecordatorioCita extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre_cliente;
    public $fecha;
    public $hora;
    public $servicios;
    public $estilista;

    public function __construct($nombre_cliente, $fecha, $hora, $servicios, $estilista)
    {
        $this->nombre_cliente = $nombre_cliente;
        $this->fecha = $fecha;
        $this->hora = $hora;
        $this->servicios = $servicios;
        $this->estilista = $estilista;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('aulaclick593@gmail.com', 'StyleNow - Recordatorios'),
            subject: '⏰ Recordatorio: Mañana es tu cita en StyleNow',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recordatorio-cita',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}