<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecordatorioCita;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// EL ROBOT QUE ENVÍA RECORDATORIOS
Schedule::call(function () {
    Log::info('Buscando citas para mañana...');
    Carbon::setLocale('es');
    
    // Obtenemos la fecha de mañana
    $manana = Carbon::tomorrow('America/Guayaquil')->toDateString();

    // Buscamos todas las citas agendadas para MAÑANA
    $citas = DB::table('tbl_cita')
        ->join('tbl_cliente', 'tbl_cita.cit_clienteId', '=', 'tbl_cliente.cli_id')
        ->join('tbl_usuario as cliente', 'tbl_cliente.cli_usuarioId', '=', 'cliente.usr_id')
        ->join('tbl_empleado', 'tbl_cita.cit_empleadoId', '=', 'tbl_empleado.emp_id')
        ->join('tbl_usuario as estilista', 'tbl_empleado.emp_usuarioId', '=', 'estilista.usr_id')
        ->whereDate('cit_fechaCita', $manana)
        ->whereIn('cit_estadoCita', ['Pendiente', 'Confirmada'])
        ->select(
            'cliente.usr_nombre as cliente_nombre',
            'cliente.usr_email as cliente_email',
            'cliente.usr_telefono as cliente_telefono',
            'cit_fechaCita',
            'cit_nombres_servicios',
            DB::raw("CONCAT(estilista.usr_nombre, ' ', estilista.usr_apellido) as estilista_nombre")
        )
        ->get();

    foreach ($citas as $cita) {
        $fechaHora = Carbon::parse($cita->cit_fechaCita);
        $hora = $fechaHora->format('H:i');
        $fechaTexto = $fechaHora->translatedFormat('l, d \d\e F \d\e Y');

        // 1. ENVIAR CORREO
        if (!empty($cita->cliente_email)) {
            try {
                Mail::to($cita->cliente_email)->send(new RecordatorioCita(
                    $cita->cliente_nombre, $fechaTexto, $hora, $cita->cit_nombres_servicios, $cita->estilista_nombre
                ));
            } catch (\Exception $e) {
                Log::error("Error correo recordatorio: " . $e->getMessage());
            }
        }

        // 2. ENVIAR WHATSAPP (Usando la API de UltraMsg)
        if (!empty($cita->cliente_telefono)) {
            $telefonoLimpio = preg_replace('/[^0-9]/', '', $cita->cliente_telefono);
            if (substr($telefonoLimpio, 0, 1) === '0') $telefonoLimpio = '593' . substr($telefonoLimpio, 1);
            elseif (substr($telefonoLimpio, 0, 3) !== '593') $telefonoLimpio = '593' . $telefonoLimpio;

            $mensaje = "*¡Hola {$cita->cliente_nombre}!* 🌟\n";
            $mensaje .= "Te recordamos que *mañana* tienes una cita con nosotros en *StyleNow*.\n\n";
            $mensaje .= "💇‍♀️ *Servicio:* {$cita->cit_nombres_servicios}\n";
            $mensaje .= "👤 *Profesional:* {$cita->estilista_nombre}\n";
            $mensaje .= "⏰ *Hora:* {$hora}\n\n";
            $mensaje .= "¡Te esperamos! Si necesitas cancelar, por favor contáctanos lo antes posible. ✨";

            $apiUrl = env('WHATSAPP_API_URL');
            $token = env('WHATSAPP_TOKEN');

            if ($apiUrl && $token) {
                try {
                    Http::post($apiUrl, [
                        'token' => $token,
                        'to' => '+' . $telefonoLimpio,
                        'body' => $mensaje
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error WhatsApp recordatorio: ' . $e->getMessage());
                }
            }
        }
    }
})->dailyAt('08:00')->timezone('America/Guayaquil');
// Se ejecutará todos los días a las 8:00 AM hora de Ecuador