<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmacionCita;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClienteController extends Controller
{
    private function getClienteId() {
        $user = Auth::user();
        if (!$user) return null;
        $cliente = DB::table('tbl_cliente')->where('cli_usuarioId', $user->usr_id)->first();
        return $cliente ? $cliente->cli_id : null;
    }

    // =========================================================
    // VISTA: DASHBOARD PRINCIPAL (CLIENTE)
    // =========================================================
    public function dashboard()
    {
        $user = Auth::user();
        $cliente = DB::table('tbl_cliente')->where('cli_usuarioId', $user->usr_id)->first();

        if (!$cliente) {
            return redirect('/')->with('error', 'Perfil de cliente no encontrado.');
        }

        Carbon::setLocale('es');
        $hoy = Carbon::now('America/Guayaquil');

        // 1. Estadísticas Generales
        $citasProximas = DB::table('tbl_cita')
            ->join('tbl_empleado', 'tbl_cita.cit_empleadoId', '=', 'tbl_empleado.emp_id')
            ->join('tbl_usuario as estilista', 'tbl_empleado.emp_usuarioId', '=', 'estilista.usr_id')
            ->where('cit_clienteId', $cliente->cli_id)
            ->whereIn('cit_estadoCita', ['Pendiente', 'Confirmada'])
            ->where('cit_fechaCita', '>=', $hoy->toDateString())
            ->select('tbl_cita.*', DB::raw("CONCAT(estilista.usr_nombre, ' ', estilista.usr_apellido) as estilista_nombre"))
            ->orderBy('cit_fechaCita', 'asc')
            ->get();

        $citasCompletadas = DB::table('tbl_cita')
            ->where('cit_clienteId', $cliente->cli_id)
            ->where('cit_estadoCita', 'Completada')
            ->get();

        // 2. Cálculo de Puntos
        $totalGastado = $citasCompletadas->sum('cit_precio');
        $puntosGanados = floor($totalGastado * 10);
        $puntosGastados = DB::table('tbl_canje_puntos')->where('can_clienteId', $cliente->cli_id)->sum('can_puntos_gastados');
        $puntosDisponibles = max(0, $puntosGanados - $puntosGastados);

        // 3. Promociones Activas
        $promocionesActivas = DB::table('tbl_promocion')
            ->where('prm_estado', 1)
            ->whereDate('prm_fechaFin', '>=', $hoy->toDateString())
            ->orderBy('prm_fechaFin', 'asc')
            ->take(3) // Solo mostramos 3 en el dashboard
            ->get();

        return view('cliente.dashboard', compact(
            'citasProximas', 
            'citasCompletadas', 
            'puntosDisponibles', 
            'promocionesActivas'
        ));
    }
    // =========================================================
    // RUTAS DEL CLIENTE: WIZARD DE AGENDAMIENTO
    // =========================================================
    // =========================================================
    // VISTA: WIZARD DE AGENDAR CITA (LÓGICA OPTIMIZADA)
    // =========================================================
    public function agendar(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect('/login');

        // 1. Cargar todas las sucursales activas (Para la pantalla de Bienvenida)
        $sucursales = DB::table('tbl_sucursal')->where('suc_estado', 1)->get();
        $sucursalesAgrupadas = $sucursales->groupBy('suc_direccion'); // Agrupamos por norte, sur, etc.

        // 2. Determinar si el usuario ya hizo clic en una sucursal en la pantalla oscura
        $sucursalId = $request->input('sucursal_id');
        $sucursalSeleccionada = null;

        if ($sucursalId) {
            $sucursalSeleccionada = $sucursales->firstWhere('suc_id', $sucursalId);
        }

        // 3. SI YA ELIGIÓ SUCURSAL -> CORREMOS TU FILTRO MÁGICO ORIGINAL 🔥
        if ($sucursalSeleccionada) {
            
            // Buscar qué especialidades (categorías) dominan los empleados ACTIVOS de ESTA SUCURSAL
            $categoriasDisponibles = DB::table('tbl_empleado_especialidad')
                ->join('tbl_empleado', 'tbl_empleado_especialidad.emp_id', '=', 'tbl_empleado.emp_id')
                ->join('tbl_usuario', 'tbl_empleado.emp_usuarioId', '=', 'tbl_usuario.usr_id')
                ->where('tbl_empleado.emp_sucursalId', $sucursalId)
                ->where('tbl_usuario.usr_estado', 'A') // Solo empleados activos
                ->pluck('tbl_empleado_especialidad.cats_id') // IDs de las categorías
                ->unique()
                ->toArray();

            // Traer SOLO los servicios de esas categorías (Evita vender lo que no podemos hacer)
            $servicios = DB::table('tbl_servicio')
                ->join('tbl_categoriaservicio', 'tbl_servicio.srv_categoriaId', '=', 'tbl_categoriaservicio.cats_id')
                ->select(
                    'tbl_servicio.srv_id', 
                    'tbl_servicio.srv_nombre', 
                    'tbl_servicio.srv_precio', 
                    'tbl_servicio.srv_duracionMinutos as srv_duracion', 
                    'tbl_servicio.srv_categoriaId',
                    'tbl_categoriaservicio.cats_nombre as categoria_nombre'
                )
                ->where('tbl_servicio.srv_estado', 1) 
                ->whereIn('tbl_servicio.srv_categoriaId', $categoriasDisponibles) 
                ->orderBy('tbl_categoriaservicio.cats_nombre', 'asc') 
                ->orderBy('tbl_servicio.srv_nombre', 'asc')
                ->get();

            // Traer SOLO empleados de esta SUCURSAL
            $empleados = DB::table('tbl_empleado')
                ->join('tbl_usuario', 'tbl_empleado.emp_usuarioId', '=', 'tbl_usuario.usr_id')
                ->select('tbl_empleado.emp_id', 'tbl_usuario.usr_nombre', 'tbl_usuario.usr_apellido')
                ->where('tbl_usuario.usr_estado', 'A')
                ->where('tbl_empleado.emp_sucursalId', $sucursalId)
                ->get();

            // Cargar las Especialidades de cada empleado (Para el filtro del Paso 2)
            $especialidades = DB::table('tbl_empleado_especialidad')->get();
            $empCats = [];
            foreach($especialidades as $esp) {
                $empCats[$esp->emp_id][] = $esp->cats_id;
            }
            foreach($empleados as $emp) {
                $emp->categorias = $empCats[$emp->emp_id] ?? [];
            }

        } else {
            // Si NO ha elegido sucursal aún, mandamos las variables vacías 
            // para que la vista sólo muestre el recuadro negro de "Hola, ¿dónde te encuentras?"
            $servicios = collect();
            $empleados = collect();
        }

        return view('cliente.AgendarCitas_usuario', compact(
            'servicios', 'empleados', 'user', 'sucursalId', 
            'sucursalesAgrupadas', 'sucursalSeleccionada'
        ));
    }

    // AJAX: Calcular horas libres (ALGORITMO DE SUPERPOSICIÓN DE TIEMPOS)
    public function horasDisponibles(Request $request)
    {
        $fecha = $request->fecha; 
        $empleadoId = $request->empleado_id;
        $duracionTotal = (int) $request->duracion; // Ej: 75 minutos
        
        if ($duracionTotal <= 0) $duracionTotal = 30;

        // Citas del día de ese estilista
        $citasDelDia = DB::table('tbl_cita')
            ->where('cit_empleadoId', $empleadoId)
            ->whereDate('cit_fechaCita', $fecha)
            ->whereIn('cit_estadoCita', ['Pendiente', 'Confirmada', 'En Progreso'])
            ->select('cit_fechaCita', 'cit_duracionTotal')
            ->get();

        $rangosOcupados = [];
        foreach($citasDelDia as $cita) {
            $inicio = Carbon::parse($cita->cit_fechaCita);
            $fin = $inicio->copy()->addMinutes($cita->cit_duracionTotal ?? 30);
            $rangosOcupados[] = ['inicio' => $inicio, 'fin' => $fin];
        }

        $horasDisponibles = [];
        $horaApertura = Carbon::parse($fecha . ' 09:00:00');
        $horaCierre = Carbon::parse($fecha . ' 19:00:00');
        $ahora = Carbon::now('America/Guayaquil');
        
        $iterador = $horaApertura->copy();

        while ($iterador < $horaCierre) {
            $posibleInicio = $iterador->copy();
            $posibleFin = $posibleInicio->copy()->addMinutes($duracionTotal);

            // Regla A: No exceder horario de cierre
            if ($posibleFin > $horaCierre) {
                $iterador->addMinutes(30);
                continue;
            }

            // Regla B: Choque de horarios (Inicio1 < Fin2 && Fin1 > Inicio2)
            $hayChoque = false;
            foreach ($rangosOcupados as $ocupado) {
                if ($posibleInicio < $ocupado['fin'] && $posibleFin > $ocupado['inicio']) {
                    $hayChoque = true;
                    break;
                }
            }

            // Regla C: No agendar en el pasado si es hoy
            if ($fecha === $ahora->toDateString() && $posibleInicio <= $ahora) {
                $hayChoque = true;
            }

            if (!$hayChoque) {
                $horasDisponibles[] = $posibleInicio->format('H:i');
            }

            $iterador->addMinutes(30);
        }

        return response()->json(['success' => true, 'horas' => $horasDisponibles]);
    }

    // AJAX: Guardar Cita Perfectamente alineada con la BD
    // AJAX: Guardar Cita y Enviar Correo
    public function guardarCita(Request $request)
    {
        $user = Auth::user();
        $cliente = DB::table('tbl_cliente')->where('cli_usuarioId', $user->usr_id)->first();
        if (!$cliente) return response()->json(['success' => false, 'message' => 'No autorizado']);

        try {
            $fechaHora = Carbon::parse($request->fecha . ' ' . $request->hora)->format('Y-m-d H:i:s');
            $nombresServicios = implode(' + ', $request->nombres_servicios);

            // Usamos insertGetId para obtener el ID de la cita para el link del correo
            $citaId = DB::table('tbl_cita')->insertGetId([
                'cit_clienteId' => $cliente->cli_id,
                'cit_empleadoId' => $request->empleado_id,
                'cit_servicioId' => current($request->servicios_ids),
                'cit_servicios_ids' => implode(',', $request->servicios_ids),
                'cit_nombres_servicios' => $nombresServicios,
                'cit_sucursalId' => $request->sucursal_id,
                'cit_fechaCita' => $fechaHora,
                'cit_estadoCita' => 'Pendiente', 
                'cit_duracionTotal' => $request->duracion_total,
                'cit_precio' => $request->precio_total,
                'cit_abono' => $request->abono ?? 0.00,
                'cit_promocionId' => $request->promocion_id ?? null, // <--- AÑADE ESTA LÍNEA AQUÍ
                'cit_comisionGanada' => 0,
                'cit_comentario' => $request->notas,
                'cit_fechaCreacion' => Carbon::now('America/Guayaquil')->format('Y-m-d H:i:s')
            ]);

            // ==========================================
            // LÓGICA DE ENVÍO DE CORREO
            // ==========================================
            Carbon::setLocale('es');
            $fechaFormateada = Carbon::parse($request->fecha)->translatedFormat('l, d \d\e F \d\e Y');
            $urlConfirmacion = route('cita.confirmar.email', ['id' => $citaId]);

            // Mandar el correo en segundo plano
            Mail::to($user->usr_email)->send(new ConfirmacionCita(
                $user->usr_nombre,
                $fechaFormateada,
                $request->hora,
                $nombresServicios,
                $request->empleado_nombre,
                $urlConfirmacion
            ));

            // ==========================================
            // LÓGICA DE ENVÍO DE CORREO
            // ==========================================
            Carbon::setLocale('es');
            $fechaFormateada = Carbon::parse($request->fecha)->translatedFormat('l, d \d\e F \d\e Y');
            $urlConfirmacion = route('cita.confirmar.email', ['id' => $citaId]);

            // Mandar el correo en segundo plano
            Mail::to($user->usr_email)->send(new ConfirmacionCita(
                $user->usr_nombre,
                $fechaFormateada,
                $request->hora,
                $nombresServicios,
                $request->empleado_nombre,
                $urlConfirmacion
            ));

            // ==========================================
            // 🚀 NUEVO: LÓGICA DE ENVÍO DE WHATSAPP
            // ==========================================
            if(!empty($user->usr_telefono)) {
                $this->notificarPorWhatsApp(
                    $user->usr_telefono,
                    $user->usr_nombre,
                    $fechaFormateada,
                    $request->hora,
                    $nombresServicios,
                    $request->empleado_nombre,
                    $urlConfirmacion
                );
            }

            return response()->json(['success' => true]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al guardar la reserva: ' . $e->getMessage()]);
        }
    }

    // Función que procesa el clic del correo electrónico
    public function confirmarCitaEmail($id)
    {
        // Buscar si existe la cita
        $cita = DB::table('tbl_cita')->where('cit_id', $id)->first();

        if ($cita) {
            // Actualizar la base de datos
            DB::table('tbl_cita')->where('cit_id', $id)->update([
                'cit_estadoCita' => 'Confirmada'
            ]);
            
            // Redirigir al login o inicio con un mensaje de éxito
            return redirect('/login')->with('success', '¡Excelente! Tu cita ha sido confirmada exitosamente. Te esperamos.');
        }

        return redirect('/login')->with('error', 'El enlace de confirmación no es válido o la cita ya no existe.');
    }

    // =========================================================
    // LÓGICA DE NOTIFICACIONES POR WHATSAPP
    // =========================================================
    private function notificarPorWhatsApp($telefono, $nombre, $fecha, $hora, $servicios, $estilista, $urlConfirmacion)
    {
        // 1. Limpiar y formatear el teléfono para WhatsApp (Ej: Ecuador +593)
        // Quitamos espacios o guiones
        $telefonoLimpio = preg_replace('/[^0-9]/', '', $telefono);
        
        // Si empieza con 0 (ej: 0991234567), se lo quitamos y le ponemos 593
        if (substr($telefonoLimpio, 0, 1) === '0') {
            $telefonoLimpio = '593' . substr($telefonoLimpio, 1);
        } elseif (substr($telefonoLimpio, 0, 3) !== '593') {
            $telefonoLimpio = '593' . $telefonoLimpio;
        }

        // 2. Diseñar el mensaje con Emojis y Negritas nativas de WhatsApp
        $mensaje = "*¡Hola {$nombre}!* 🌟\n";
        $mensaje .= "Hemos recibido tu reserva en *StyleNow*.\n\n";
        $mensaje .= "💇‍♀️ *Servicio:* {$servicios}\n";
        $mensaje .= "👤 *Profesional:* {$estilista}\n";
        $mensaje .= "📅 *Fecha:* {$fecha}\n";
        $mensaje .= "⏰ *Hora:* {$hora}\n\n";
        $mensaje .= "Para asegurar tu espacio, por favor *confirma tu asistencia* haciendo clic en el siguiente enlace:\n{$urlConfirmacion}\n\n";
        $mensaje .= "¡Te esperamos para dejarte increíble! ✨";

        // 3. Enviar el mensaje a través de la API
        $apiUrl = env('WHATSAPP_API_URL'); 
        $token = env('WHATSAPP_TOKEN');

        // Solo intenta enviarlo si configuraste el .env
        if($apiUrl && $token && strpos($apiUrl, 'TU_INSTANCE_ID') === false) {
            try {
                Http::post($apiUrl, [
                    'token' => $token,
                    'to' => '+' . $telefonoLimpio,
                    'body' => $mensaje
                ]);
            } catch (\Exception $e) {
                // Si WhatsApp falla (ej. sin internet), no queremos que la página del cliente se rompa
                Log::error('Error enviando WhatsApp: ' . $e->getMessage());
            }
        }
    }
    
    // =========================================================
    // VISTA: MIS CITAS (Próximas)
    // =========================================================
    public function misCitas()
    {
        $user = Auth::user();
        $cliente = DB::table('tbl_cliente')->where('cli_usuarioId', $user->usr_id)->first();

        if (!$cliente) {
            return redirect('/')->with('error', 'Perfil de cliente no encontrado.');
        }

        Carbon::setLocale('es');

        // Buscar solo citas futuras o pendientes
        $citasActivas = DB::table('tbl_cita')
            ->join('tbl_empleado', 'tbl_cita.cit_empleadoId', '=', 'tbl_empleado.emp_id')
            ->join('tbl_usuario as estilista', 'tbl_empleado.emp_usuarioId', '=', 'estilista.usr_id')
            ->where('tbl_cita.cit_clienteId', $cliente->cli_id)
            ->whereIn('tbl_cita.cit_estadoCita', ['Pendiente', 'Confirmada'])
            ->select(
                'tbl_cita.*',
                DB::raw("CONCAT(estilista.usr_nombre, ' ', estilista.usr_apellido) as estilista_nombre")
            )
            ->orderBy('tbl_cita.cit_fechaCita', 'asc')
            ->get();

        // Calcular detalles para la vista
        foreach ($citasActivas as $cita) {
            $fechaObj = Carbon::parse($cita->cit_fechaCita);
            
            // Datos para el "Ticket"
            $cita->dia_numero = $fechaObj->format('d');
            $cita->mes_nombre = strtoupper($fechaObj->translatedFormat('M'));
            $cita->dia_nombre = ucfirst($fechaObj->translatedFormat('l'));
            $cita->hora_formato = $fechaObj->format('H:i');
            
            // Lógica de negocio: Solo puede cancelar si faltan más de 2 horas
            $horasRestantes = Carbon::now('America/Guayaquil')->diffInHours($fechaObj, false);
            $cita->se_puede_cancelar = $horasRestantes >= 2;
        }

        return view('cliente.mis_citas', compact('citasActivas'));
    }

    // AJAX: Cancelar Cita
    // =========================================================
    // AJAX: CANCELAR CITA (POLÍTICA DE 3 STRIKES)
    // =========================================================
   public function cancelarCita(Request $request, $id)
    {
        $user = Auth::user();
        $cliente = DB::table('tbl_cliente')->where('cli_usuarioId', $user->usr_id)->first();

        if (!$cliente) {
            return response()->json(['success' => false, 'message' => 'Cliente no encontrado.']);
        }

        // 1. Verificar que la cita le pertenezca y se pueda cancelar
        $cita = DB::table('tbl_cita')->where('cit_id', $id)->where('cit_clienteId', $cliente->cli_id)->first();

        if (!$cita || in_array($cita->cit_estadoCita, ['Cancelada', 'cancelada', 'Completada'])) {
            return response()->json(['success' => false, 'message' => 'No se puede cancelar esta cita.']);
        }

        try {
            // 2. Cancelar la cita actual
            DB::table('tbl_cita')->where('cit_id', $id)->update([
                'cit_estadoCita' => 'Cancelada',
                'cit_fechaCancelacion' => Carbon::now('America/Guayaquil')
            ]);

            // 3. Contar TODAS las citas canceladas de este cliente
            $canceladasCount = DB::table('tbl_cita')
                ->where('cit_clienteId', $cliente->cli_id)
                ->whereIn('cit_estadoCita', ['Cancelada', 'cancelada'])
                ->count();

            // 4. POLÍTICA DE 3 STRIKES
            if ($canceladasCount >= 3) {
                
                // Inactivar la cuenta del usuario
                DB::table('tbl_usuario')->where('usr_id', $user->usr_id)->update([
                    'usr_estado' => 'I'
                ]);

                // --- INICIO DE ENVÍO DE CORREO ---
                try {
                    $datosCorreo = [
                        'clienteNombre' => $user->usr_nombre . ' ' . $user->usr_apellido
                    ];
                    
                    // Asegúrate de tener: use Illuminate\Support\Facades\Mail; al inicio de tu controlador
                    \Illuminate\Support\Facades\Mail::send('emails.cuenta_bloqueada', $datosCorreo, function($message) use ($user) {
                        $message->to($user->usr_email)
                                ->subject('⚠️ Aviso Importante: Cuenta Suspendida - StyleNow');
                    });
                } catch (\Exception $e) {
                    // Si el correo falla (por ej. falta de internet en el servidor), 
                    // silenciamos el error para que de todas formas SE BLOQUEE al usuario.
                }
                // --- FIN DE ENVÍO DE CORREO ---

                // Expulsar al usuario cerrando su sesión
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'success' => true,
                    'banned' => true, // Le avisamos al Javascript que fue expulsado
                    'message' => 'Has cancelado 3 citas. Por políticas de seguridad, tu cuenta ha sido bloqueada. Por favor, comunícate con recepción.'
                ]);
            }

            // 5. Si no llegó a 3, le decimos cuántas le quedan
            $restantes = 3 - $canceladasCount;
            return response()->json([
                'success' => true,
                'banned' => false,
                'message' => "Cita cancelada exitosamente. \n\n⚠️ CUIDADO: Te quedan $restantes cancelaciones permitidas antes del bloqueo de cuenta."
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al procesar la cancelación.']);
        }
    }
    // =========================================================
    // VISTA: HISTORIAL DE CITAS PASADAS
    // =========================================================
    public function historial()
    {
        $user = Auth::user();
        $cliente = DB::table('tbl_cliente')->where('cli_usuarioId', $user->usr_id)->first();

        if (!$cliente) {
            return redirect('/')->with('error', 'Perfil de cliente no encontrado.');
        }

        Carbon::setLocale('es');

        // Buscar citas que YA NO están activas (Finalizadas, Atendidas, Canceladas, etc.)
        $historialCitas = DB::table('tbl_cita')
            ->join('tbl_empleado', 'tbl_cita.cit_empleadoId', '=', 'tbl_empleado.emp_id')
            ->join('tbl_usuario as estilista', 'tbl_empleado.emp_usuarioId', '=', 'estilista.usr_id')
            ->where('tbl_cita.cit_clienteId', $cliente->cli_id)
            ->whereNotIn('tbl_cita.cit_estadoCita', ['Pendiente', 'Confirmada', 'En Progreso']) // Filtramos las activas
            ->select(
                'tbl_cita.*',
                DB::raw("CONCAT(estilista.usr_nombre, ' ', estilista.usr_apellido) as estilista_nombre")
            )
            ->orderBy('tbl_cita.cit_fechaCita', 'desc') // Las más recientes primero
            ->get();

        foreach ($historialCitas as $cita) {
            $fechaObj = Carbon::parse($cita->cit_fechaCita);
            $cita->fecha_formateada = $fechaObj->translatedFormat('d \d\e F, Y');
            $cita->hora_formato = $fechaObj->format('H:i');
        }

        return view('cliente.historial', compact('historialCitas'));
    }

    // AJAX: Guardar Calificación y Comentario
    public function calificarCita(Request $request, $id)
    {
        $user = Auth::user();
        $cliente = DB::table('tbl_cliente')->where('cli_usuarioId', $user->usr_id)->first();

        if (!$cliente) return response()->json(['success' => false, 'message' => 'No autorizado']);

        // Validar datos de entrada
        if (!$request->calificacion || $request->calificacion < 1 || $request->calificacion > 5) {
            return response()->json(['success' => false, 'message' => 'Debes seleccionar una estrella.']);
        }

        try {
            // Actualizar la cita con las estrellas y el comentario
            DB::table('tbl_cita')
                ->where('cit_id', $id)
                ->where('cit_clienteId', $cliente->cli_id)
                ->where('cit_estadoCita', 'Completada') // Solo citas completadas
                ->update([
                    'cit_calificacion' => $request->calificacion,
                    'cit_comentario' => $request->comentario,
                    // 'cit_fechaModificacion' => Carbon::now() // Si tienes esta columna, descomenta
                ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al guardar calificación.']);
        }
    }

    // =========================================================
    // CATÁLOGO DE PREMIOS (Función auxiliar)
    // =========================================================
    private function getCatalogoPremios()
    {
        return [
            ['id' => 1, 'nombre' => 'Kit de Cuidado Capilar', 'desc' => 'Shampoo y acondicionador profesional', 'costo' => 250, 'tipo' => 'Producto', 'icono' => '🧴'],
            ['id' => 2, 'nombre' => 'Sesión de Maquillaje', 'desc' => 'Maquillaje para cualquier ocasión', 'costo' => 300, 'tipo' => 'Servicio', 'icono' => '💄'],
            ['id' => 3, 'nombre' => 'Corte Gratis', 'desc' => 'Corte de cabello con tu estilista favorito', 'costo' => 450, 'tipo' => 'Servicio', 'icono' => '✂️'],
            ['id' => 4, 'nombre' => 'Productos de Lujo', 'desc' => 'Selección de productos Kerastase', 'costo' => 800, 'tipo' => 'Producto', 'icono' => '🎁'],
        ];
    }

    // =========================================================
    // VISTA: PUNTOS Y FIDELIZACIÓN (LÓGICA REAL)
    // =========================================================
    // =========================================================
    // VISTA: PUNTOS Y FIDELIZACIÓN (LÓGICA REAL)
    // =========================================================
    public function puntosFidelizacion()
    {
        $user = Auth::user();
        $cliente = DB::table('tbl_cliente')->where('cli_usuarioId', $user->usr_id)->first();

        if (!$cliente) return redirect('/')->with('error', 'Perfil no encontrado.');

        Carbon::setLocale('es');
        $diasRegistrado = Carbon::parse($user->usr_fechaRegistro)->diffInDays(Carbon::now());

        // 1. Calcular Puntos Ganados
        $citasCompletadas = DB::table('tbl_cita')->where('cit_clienteId', $cliente->cli_id)->where('cit_estadoCita', 'Completada')->orderBy('cit_fechaCita', 'desc')->get();
        $totalGastado = $citasCompletadas->sum('cit_precio');
        $puntosTotales = floor($totalGastado * 10); 

        // 2. Calcular Puntos Gastados en Canjes
        $canjesRealizados = DB::table('tbl_canje_puntos')->where('can_clienteId', $cliente->cli_id)->orderBy('created_at', 'desc')->get();
        $puntosGastados = $canjesRealizados->sum('can_puntos_gastados');
        
        // 3. LA MATEMÁTICA FINAL
        $puntosDisponibles = $puntosTotales - $puntosGastados; 

        // 4. Determinar Nivel
        $nivelActual = 'Bronce';
        $puntosParaSiguiente = 500 - $puntosTotales;
        if ($puntosTotales >= 1500) { $nivelActual = 'Platino'; $puntosParaSiguiente = 0; }
        elseif ($puntosTotales >= 1000) { $nivelActual = 'Oro'; $puntosParaSiguiente = 1500 - $puntosTotales; }
        elseif ($puntosTotales >= 500) { $nivelActual = 'Plata'; $puntosParaSiguiente = 1000 - $puntosTotales; }

        // 5. Historial Combinado
        $historialPuntos = [];
        foreach ($citasCompletadas as $cita) {
            $historialPuntos[] = [
                'timestamp' => Carbon::parse($cita->cit_fechaCita)->timestamp,
                'titulo' => 'Servicio - ' . explode('+', $cita->cit_nombres_servicios)[0],
                'fecha' => Carbon::parse($cita->cit_fechaCita)->format('d/m/Y'),
                'puntos' => '+' . floor($cita->cit_precio * 10),
                'color' => '#4ade80' 
            ];
        }
        foreach ($canjesRealizados as $canje) {
            $historialPuntos[] = [
                'timestamp' => Carbon::parse($canje->created_at)->timestamp,
                'titulo' => 'Canje: ' . $canje->can_premio_nombre,
                'fecha' => Carbon::parse($canje->created_at)->format('d/m/Y'),
                'puntos' => '-' . $canje->can_puntos_gastados,
                'color' => '#ef4444' 
            ];
        }
        usort($historialPuntos, function($a, $b) { return $b['timestamp'] <=> $a['timestamp']; });

        $premios = $this->getCatalogoPremios();

        // 6. NUEVO: Extraer solo los premios que están Pendientes de entrega
        $premiosPendientes = $canjesRealizados->where('can_estado', 'Pendiente');

        return view('cliente.Puntos_Fidelizacion', compact(
            'user', 'diasRegistrado', 'puntosTotales', 'puntosDisponibles', 
            'nivelActual', 'puntosParaSiguiente', 'historialPuntos', 'premios', 'premiosPendientes'
        ));
    }

    // =========================================================
    // AJAX: CANJEAR PREMIO (GUARDAR EN BASE DE DATOS)
    // =========================================================
    public function canjearPremio(Request $request)
    {
        $user = Auth::user();
        $cliente = DB::table('tbl_cliente')->where('cli_usuarioId', $user->usr_id)->first();
        if (!$cliente) return response()->json(['success' => false, 'message' => 'No autorizado']);

        $premioId = $request->premio_id;
        $catalogo = collect($this->getCatalogoPremios());
        $premio = $catalogo->firstWhere('id', $premioId);

        if (!$premio) return response()->json(['success' => false, 'message' => 'Premio no encontrado']);

        $totalGanado = floor(DB::table('tbl_cita')->where('cit_clienteId', $cliente->cli_id)->where('cit_estadoCita', 'Completada')->sum('cit_precio') * 10);
        $totalGastado = DB::table('tbl_canje_puntos')->where('can_clienteId', $cliente->cli_id)->sum('can_puntos_gastados');
        $disponibles = $totalGanado - $totalGastado;

        if ($disponibles < $premio['costo']) {
            return response()->json(['success' => false, 'message' => 'No tienes puntos suficientes.']);
        }

        try {
            // Guardar en BD
            DB::table('tbl_canje_puntos')->insert([
                'can_clienteId' => $cliente->cli_id,
                'can_premio_nombre' => $premio['nombre'],
                'can_puntos_gastados' => $premio['costo'],
                'can_estado' => 'Pendiente', 
                'created_at' => Carbon::now('America/Guayaquil')->format('Y-m-d H:i:s')
            ]);

            // Crear instrucciones personalizadas según el tipo de premio
            $instrucciones = '';
            if ($premio['tipo'] == 'Producto') {
                $instrucciones = "<div style='text-align:left; background:#1a1a1a; padding:15px; border-radius:10px; border-left:4px solid #fad370; margin-top:15px;'>
                    <h4 style='color:#fad370; margin-bottom:5px; font-size:16px;'>📦 ¿Cómo lo reclamo?</h4>
                    <p style='color:#ccc; font-size:14px; margin:0;'>Acércate a la recepción en tu próxima visita, indica que tienes un premio pendiente en tu cuenta y muestra tu cédula.</p>
                </div>";
            } else {
                $instrucciones = "<div style='text-align:left; background:#1a1a1a; padding:15px; border-radius:10px; border-left:4px solid #fad370; margin-top:15px;'>
                    <h4 style='color:#fad370; margin-bottom:5px; font-size:16px;'>✂️ ¿Cómo lo uso?</h4>
                    <p style='color:#ccc; font-size:14px; margin:0;'>Agenda tu cita normalmente desde la plataforma. Al llegar al salón, infórmale a tu estilista o recepción que aplicarás tu premio.</p>
                </div>";
            }

            return response()->json([
                'success' => true, 
                'titulo' => '¡Canje Exitoso!',
                'mensaje' => 'Has adquirido <b>' . $premio['nombre'] . '</b> por ' . $premio['costo'] . ' puntos.' . $instrucciones
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al procesar el canje: ' . $e->getMessage()]);
        }
    }
    // =========================================================
    // VISTA: PROMOCIONES (Datos Reales de BD)
    // =========================================================
    public function promociones()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        \Carbon\Carbon::setLocale('es');
        $hoy = \Carbon\Carbon::now('America/Guayaquil')->toDateString();

        // 1. Traer solo promociones activas y que no hayan expirado
        $promocionesDB = \Illuminate\Support\Facades\DB::table('tbl_promocion')
            ->where('prm_estado', 1)
            ->whereDate('prm_fechaFin', '>=', $hoy)
            ->orderBy('prm_fechaFin', 'asc')
            ->get();

        $promocionesFormateadas = [];
        $categorias = ['Todas'];

        foreach ($promocionesDB as $promo) {
            $fechaFin = \Carbon\Carbon::parse($promo->prm_fechaFin);
            $diasRestantes = \Carbon\Carbon::now('America/Guayaquil')->startOfDay()->diffInDays($fechaFin, false);

            // 2. Formatear el texto del descuento según su tipo
            $descuentoTexto = '';
            if ($promo->prm_tipoDescuento == 'Porcentaje') {
                $descuentoTexto = round($promo->prm_valorDescuento) . '% OFF';
            } elseif ($promo->prm_tipoDescuento == 'Fijo') {
                $descuentoTexto = '$' . number_format($promo->prm_valorDescuento, 2);
            } elseif ($promo->prm_tipoDescuento == '2x1') {
                $descuentoTexto = '2x1';
            } else {
                $descuentoTexto = 'Especial';
            }

            // 3. Asignar Iconos y Colores dinámicos
            $icono = '🎁'; $color = '#fad370'; 
            $nombreMinuscula = strtolower($promo->prm_nombre);
            
            if (strpos($nombreMinuscula, 'cumple') !== false) { $icono = '🎂'; $color = '#ffd8b4'; }
            elseif (strpos($nombreMinuscula, 'bienvenida') !== false) { $icono = '👋'; $color = '#a8d8ea'; }
            elseif ($promo->prm_tipoDescuento == '2x1') { $icono = '👯'; $color = '#ffb6b9'; }
            elseif ($promo->prm_tipoDescuento == 'Pack') { $icono = '📦'; $color = '#b4e8c7'; }
            elseif ($promo->prm_tipoDescuento == 'Temporada') { $icono = '☀️'; $color = '#e8b4bc'; }

            // Llenar categorías dinámicamente
            $categoria = ucfirst($promo->prm_tipoDescuento);
            if (!in_array($categoria, $categorias)) {
                $categorias[] = $categoria;
            }

            // 4. Generar un Código Promocional único
            $palabraClave = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $promo->prm_nombre), 0, 6));
            $codigoGenerado = $palabraClave . $promo->prm_id . date('Y');

            $promocionesFormateadas[] = [
                'id' => $promo->prm_id,
                'titulo' => $promo->prm_nombre,
                'descripcion' => $promo->prm_descripcion,
                'categoria' => $categoria,
                'descuento' => $descuentoTexto,
                'codigo' => $codigoGenerado,
                'valido_hasta' => $fechaFin->format('Y-m-d'),
                'dias_restantes' => max(0, $diasRestantes),
                'imagen' => $icono,
                'color' => $color,
                'destacado' => $diasRestantes <= 7, 
                'condiciones' => 'Válido hasta el ' . $fechaFin->translatedFormat('d \d\e F \d\e Y') . '. ' . $promo->prm_descripcion . '. Aplican restricciones del salón.'
            ];
        }

        // 5. Separar las que expiran pronto (7 días o menos)
        $promocionesProximas = array_filter($promocionesFormateadas, function($p) {
            return $p['dias_restantes'] <= 7;
        });

        // ¡AQUÍ ESTABA EL ERROR! Apuntamos a la vista correcta y le pasamos los datos
        return view('cliente.Vista_Promociones', compact('promocionesFormateadas', 'categorias', 'promocionesProximas'));
    }
    // =========================================================
    // AJAX: VALIDAR CÓDIGO PROMOCIONAL AL AGENDAR
    // =========================================================
    public function validarPromo(Request $request)
    {
        $codigoInput = strtoupper(trim($request->codigo));
        Carbon::setLocale('es');
        $hoy = Carbon::now('America/Guayaquil')->toDateString();

        // Traemos todas las promociones activas
        $promociones = DB::table('tbl_promocion')
            ->where('prm_estado', 1)
            ->whereDate('prm_fechaFin', '>=', $hoy)
            ->get();

        foreach ($promociones as $promo) {
            // Re-generamos el código exactamente igual que en la vista
            $palabraClave = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $promo->prm_nombre), 0, 6));
            $codigoGenerado = $palabraClave . $promo->prm_id . date('Y');

            if ($codigoGenerado === $codigoInput) {
                return response()->json([
                    'success' => true,
                    'id' => $promo->prm_id,
                    'nombre' => $promo->prm_nombre,
                    'tipo' => $promo->prm_tipoDescuento,
                    'valor' => (float) $promo->prm_valorDescuento
                ]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Código inválido o expirado.']);
    }

    // =========================================================
    // VISTA: PERFIL DE USUARIO
    // =========================================================
    public function perfil()
    {
        $user = Auth::user();
        $cliente = DB::table('tbl_cliente')->where('cli_usuarioId', $user->usr_id)->first();
        if (!$cliente) return redirect('/')->with('error', 'Perfil no encontrado.');

        Carbon::setLocale('es');
        $hoy = Carbon::now('America/Guayaquil');

        // 1. Historial de Citas (Completadas)
        $historialCitas = DB::table('tbl_cita')
            ->join('tbl_empleado', 'tbl_cita.cit_empleadoId', '=', 'tbl_empleado.emp_id')
            ->join('tbl_usuario as estilista', 'tbl_empleado.emp_usuarioId', '=', 'estilista.usr_id')
            ->where('cit_clienteId', $cliente->cli_id)
            ->where('cit_estadoCita', 'Completada')
            ->select('tbl_cita.*', DB::raw("CONCAT(estilista.usr_nombre, ' ', estilista.usr_apellido) as estilista_nombre"))
            ->orderBy('cit_fechaCita', 'desc')
            ->get();

        // 2. Próximas Citas (Pendientes o Confirmadas)
        $proximasCitas = DB::table('tbl_cita')
            ->join('tbl_empleado', 'tbl_cita.cit_empleadoId', '=', 'tbl_empleado.emp_id')
            ->join('tbl_usuario as estilista', 'tbl_empleado.emp_usuarioId', '=', 'estilista.usr_id')
            ->where('cit_clienteId', $cliente->cli_id)
            ->whereIn('cit_estadoCita', ['Pendiente', 'Confirmada'])
            ->where('cit_fechaCita', '>=', $hoy->toDateString())
            ->select('tbl_cita.*', DB::raw("CONCAT(estilista.usr_nombre, ' ', estilista.usr_apellido) as estilista_nombre"))
            ->orderBy('cit_fechaCita', 'asc')
            ->get();

        // 3. Lógica de Puntos y Niveles
        $totalGastado = $historialCitas->sum('cit_precio');
        $puntosGanados = floor($totalGastado * 10);
        $puntosGastados = DB::table('tbl_canje_puntos')->where('can_clienteId', $cliente->cli_id)->sum('can_puntos_gastados');
        $puntosDisponibles = max(0, $puntosGanados - $puntosGastados);

        $nivelActual = 'Bronce';
        if ($puntosGanados >= 1500) $nivelActual = 'Platino';
        elseif ($puntosGanados >= 1000) $nivelActual = 'Oro';
        elseif ($puntosGanados >= 500) $nivelActual = 'Plata';

        // 4. Estadísticas Dinámicas
        $serviciosDiferentes = $historialCitas->pluck('cit_nombres_servicios')->unique()->count();
        $estilistasVisitados = $historialCitas->pluck('estilista_nombre')->unique()->count();
        $valoraciones = $historialCitas->whereNotNull('cit_calificacion')->pluck('cit_calificacion');
        $promedioValoracion = $valoraciones->count() > 0 ? round($valoraciones->avg(), 1) : 0;

        // 5. Servicios Favoritos (Sacamos los 3 servicios que más ha comprado)
        $favoritos = $historialCitas->groupBy('cit_nombres_servicios')->sortByDesc(function($grupo) {
            return $grupo->count();
        })->take(3)->map(function($grupo) {
            return $grupo->first(); // Tomamos los datos de la cita para pintar la tarjeta
        });

        return view('cliente.Perfil_Usuario', compact(
            'user', 'historialCitas', 'proximasCitas', 'puntosGanados', 'puntosDisponibles', 
            'nivelActual', 'totalGastado', 'serviciosDiferentes', 'estilistasVisitados', 
            'promedioValoracion', 'favoritos'
        ));
    }

    // =========================================================
    // AJAX: ACTUALIZAR PERFIL
    // =========================================================
    public function actualizarPerfil(Request $request)
    {
        $user = Auth::user();
        try {
            DB::table('tbl_usuario')
                ->where('usr_id', $user->usr_id)
                ->update([
                    'usr_nombre' => $request->nombre,
                    'usr_email' => $request->email,
                    'usr_telefono' => $request->telefono
                ]);
            return response()->json(['success' => true, 'message' => 'Perfil actualizado correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'El correo ya existe o hubo un error.']);
        }
    }

    // =========================================================
    // AJAX: CAMBIAR CONTRASEÑA
    // =========================================================
    public function cambiarPassword(Request $request)
    {
        $user = Auth::user();
        if(strlen($request->password) < 6) {
            return response()->json(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.']);
        }

        try {
            DB::table('tbl_usuario')
                ->where('usr_id', $user->usr_id)
                ->update(['usr_password' => bcrypt($request->password)]);
            
            return response()->json(['success' => true, 'message' => 'Contraseña actualizada por seguridad.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al cambiar la contraseña.']);
        }
    }
}