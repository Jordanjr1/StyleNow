<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RespuestaEmpleado extends Notification
{
    use Queueable;

    public $empleadoNombre;
    public $citaId;

    public function __construct($empleadoNombre, $citaId)
    {
        $this->empleadoNombre = $empleadoNombre;
        $this->citaId = $citaId;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'titulo' => '¡Tienes una respuesta!',
            'mensaje' => $this->empleadoNombre . ' ha respondido a tu calificación.',
            'url' => '/cliente/historial#card-cita-' . $this->citaId,
            'icono' => '💬'
        ];
    }
}