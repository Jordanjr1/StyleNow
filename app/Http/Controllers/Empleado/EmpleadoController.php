<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Notifications\RespuestaEmpleado;
use App\Models\Usuario; // Usamos 'Usuario' porque así se llama tu modelo

class EmpleadoController extends Controller
{

// =========================================================
    // DASHBOARD PRINCIPAL (EMPLEADO)
    // =========================================================
    public function dashboard()
    {
        $empleadoId = $this->getEmpleadoId();
        if (!$empleadoId) return redirect('/')->with('error', 'Acceso denegado.');

        \Carbon\Carbon::setLocale('es');
        $inicioMes = \Carbon\Carbon::now('America/Guayaquil')->startOfMonth()->format('Y-m-d');
        $hoy = \Carbon\Carbon::now('America/Guayaquil')->toDateString();

        // 1. Citas del Mes Completadas
        $citasMesCount = DB::table('tbl_cita')
            ->where('cit_empleadoId', $empleadoId)
            ->where('cit_estadoCita', 'Completada')
            ->whereDate('cit_fechaCita', '>=', $inicioMes)
            ->count();

        // 2. Lógica de Comisión y Metas
        $comisionActual = 5;
        $siguienteNivel = 40;
        $porcentajeSiguiente = 10;
        
        if ($citasMesCount >= 50) {
            $comisionActual = 15;
            $siguienteNivel = 50; 
            $porcentajeSiguiente = 15;
        } elseif ($citasMesCount >= 40) {
            $comisionActual = 10;
            $siguienteNivel = 50;
            $porcentajeSiguiente = 15;
        }
        
        $faltanParaSiguiente = max(0, $siguienteNivel - $citasMesCount);
        $progresoGeneral = $siguienteNivel > 0 ? min(100, ($citasMesCount / $siguienteNivel) * 100) : 0;

        $metas = [
            'citas_mes' => $citasMesCount,
            'comision_actual' => $comisionActual,
            'siguiente_nivel' => $siguienteNivel,
            'porcentaje_siguiente' => $porcentajeSiguiente,
            'faltan' => $faltanParaSiguiente,
            'progreso' => $progresoGeneral,
            'progreso_40' => min(100, ($citasMesCount / 40) * 100),
            'progreso_50' => min(100, ($citasMesCount / 50) * 100),
        ];

        // 3. Valoración Promedio
        $promedioValoracion = DB::table('tbl_cita')
            ->where('cit_empleadoId', $empleadoId)
            ->whereNotNull('cit_calificacion')
            ->avg('cit_calificacion') ?? 5.0;

        // 4. Citas Pendientes por Aceptar
        $citasPendientes = DB::table('tbl_cita')
            ->leftJoin('tbl_cliente', 'tbl_cita.cit_clienteId', '=', 'tbl_cliente.cli_id')
            ->leftJoin('tbl_usuario', 'tbl_cliente.cli_usuarioId', '=', 'tbl_usuario.usr_id')
            ->leftJoin('tbl_servicio', 'tbl_cita.cit_servicioId', '=', 'tbl_servicio.srv_id')
            ->where('cit_empleadoId', $empleadoId)
            ->where('cit_estadoCita', 'Pendiente')
            ->whereDate('cit_fechaCita', '>=', $hoy)
            ->select(
                'tbl_cita.*', 
                'tbl_servicio.srv_nombre as servicio',
                DB::raw("CONCAT(tbl_usuario.usr_nombre, ' ', tbl_usuario.usr_apellido) as cliente_nombre")
            )
            ->orderBy('cit_fechaCita', 'asc')
            ->limit(5)
            ->get();

        // 5. Últimas Citas Atendidas
        $ultimasAtendidas = DB::table('tbl_cita')
            ->leftJoin('tbl_cliente', 'tbl_cita.cit_clienteId', '=', 'tbl_cliente.cli_id')
            ->leftJoin('tbl_usuario', 'tbl_cliente.cli_usuarioId', '=', 'tbl_usuario.usr_id')
            ->leftJoin('tbl_servicio', 'tbl_cita.cit_servicioId', '=', 'tbl_servicio.srv_id')
            ->where('cit_empleadoId', $empleadoId)
            ->where('cit_estadoCita', 'Completada')
            ->select(
                'tbl_cita.*', 
                'tbl_servicio.srv_nombre as servicio',
                DB::raw("CONCAT(tbl_usuario.usr_nombre, ' ', tbl_usuario.usr_apellido) as cliente_nombre")
            )
            ->orderBy('cit_fechaCita', 'desc')
            ->limit(4)
            ->get();

        // 6. Alertas de Inventario
        $alertasInventario = DB::table('tbl_producto')
            ->where('prd_estado', 'A')
            ->where('prd_stockActual', '<=', 5)
            ->select('prd_id', 'prd_nombre', 'prd_stockActual')
            ->orderBy('prd_stockActual', 'asc')
            ->limit(3)
            ->get();

        // Enviar todo a la vista
        return view('empleado.dashboard', compact('metas', 'promedioValoracion', 'citasPendientes', 'ultimasAtendidas', 'alertasInventario'));
    }
    private function getEmpleadoId() {
        $user = Auth::user();
        if (!$user) return null;
        $empleado = DB::table('tbl_empleado')->where('emp_usuarioId', $user->usr_id)->first();
        return $empleado ? $empleado->emp_id : null;
    }

    public function citasPendientes()
    {
        $empleadoId = $this->getEmpleadoId();
        if (!$empleadoId) return redirect('/')->with('error', 'Acceso denegado.');

        $hoy = Carbon::now('America/Guayaquil')->toDateString();
        Carbon::setLocale('es');

        $citasQuery = DB::table('tbl_cita')
            ->leftJoin('tbl_cliente', 'tbl_cita.cit_clienteId', '=', 'tbl_cliente.cli_id')
            ->leftJoin('tbl_usuario as u_cli', 'tbl_cliente.cli_usuarioId', '=', 'u_cli.usr_id')
            ->leftJoin('tbl_servicio', 'tbl_cita.cit_servicioId', '=', 'tbl_servicio.srv_id')
            ->where('cit_empleadoId', $empleadoId)
            ->whereDate('cit_fechaCita', '>=', $hoy)
            ->whereIn('cit_estadoCita', [
                'Pendiente', 'Confirmada', 'En Progreso', 'Por Cobrar', 
                'pendiente', 'confirmada', 'en progreso', 'por cobrar'
            ])
            ->select(
                'tbl_cita.*',
                DB::raw("CONCAT(u_cli.usr_nombre, ' ', u_cli.usr_apellido) as cliente_nombre"),
                'u_cli.usr_telefono as cliente_telefono',
                'tbl_servicio.srv_nombre as servicio_principal'
            )
            ->orderBy('cit_fechaCita', 'asc')
            ->get();

        $citasFormateadas = [];
        $ingresosEstimados = 0; $confirmadas = 0; $pendientes = 0;

        foreach ($citasQuery as $cita) {
            $fechaObj = Carbon::parse($cita->cit_fechaCita);
            $estadoStr = strtolower($cita->cit_estadoCita);
            
            if ($estadoStr === 'confirmada') $confirmadas++;
            if ($estadoStr === 'pendiente') $pendientes++;
            $ingresosEstimados += (float) $cita->cit_comisionGanada;

            $citasFormateadas[] = [
                'id' => $cita->cit_id,
                'cliente' => trim($cita->cliente_nombre) ?: 'Cliente General',
                'telefono' => $cita->cliente_telefono ?: 'Sin registrar',
                'servicio' => $cita->cit_nombres_servicios ?? $cita->servicio_principal ?? 'Servicio General',
                'fecha_texto' => $fechaObj->translatedFormat('d M Y'),
                'hora' => $fechaObj->format('H:i'),
                'duracion' => $cita->cit_duracionTotal ?? 30,
                'precio' => (float) $cita->cit_precio,
                'comision' => (float) $cita->cit_comisionGanada,
                'estado' => $estadoStr,
                'es_hoy' => $fechaObj->toDateString() === $hoy
            ];
        }

        return view('empleado.Citas_pendients', [
            'citas_pendientes' => $citasFormateadas,
            'estadisticas' => [
                'total_citas' => count($citasFormateadas),
                'confirmadas' => $confirmadas,
                'pendientes' => $pendientes,
                'ingresos_estimados' => $ingresosEstimados
            ]
        ]);
    }

    public function citasAtendidas() 
    { 
        $empleadoId = $this->getEmpleadoId();
        if (!$empleadoId) return redirect('/')->with('error', 'Acceso denegado.');

        Carbon::setLocale('es');

        $citasQuery = DB::table('tbl_cita')
            ->leftJoin('tbl_cliente', 'tbl_cita.cit_clienteId', '=', 'tbl_cliente.cli_id')
            ->leftJoin('tbl_usuario as u_cli', 'tbl_cliente.cli_usuarioId', '=', 'u_cli.usr_id')
            ->leftJoin('tbl_servicio', 'tbl_cita.cit_servicioId', '=', 'tbl_servicio.srv_id')
            ->where('cit_empleadoId', $empleadoId)
            ->where('cit_estadoCita', 'Completada') 
            ->select(
                'tbl_cita.*',
                DB::raw("CONCAT(u_cli.usr_nombre, ' ', u_cli.usr_apellido) as cliente_nombre"),
                'tbl_servicio.srv_nombre as servicio_principal'
            )
            ->orderBy('cit_fechaCita', 'desc')
            ->get();

        $citasAtendidas = [];
        $totalComisiones = 0;
        $sumaValoraciones = 0;
        $citasConValoracion = 0;

        foreach ($citasQuery as $cita) {
            $fechaObj = Carbon::parse($cita->cit_fechaCita);
            $comision = (float) $cita->cit_comisionGanada;
            
            // Usamos el dato real de la base de datos
            $valoracion = isset($cita->cit_calificacion) ? (float) $cita->cit_calificacion : 0; 
            
            $totalComisiones += $comision;
            if ($valoracion > 0) { $sumaValoraciones += $valoracion; $citasConValoracion++; }

            $citasAtendidas[] = [
                'id' => $cita->cit_id,
                'fecha_formato_input' => $fechaObj->format('Y-m-d'),
                'fecha_texto' => $fechaObj->format('d/m/Y'),
                'hora' => $fechaObj->format('H:i'),
                'cliente' => trim($cita->cliente_nombre) ?: 'Cliente General',
                'servicio' => $cita->cit_nombres_servicios ?? $cita->servicio_principal ?? 'Varios Servicios',
                'duracion' => $cita->cit_duracionTotal ?? 30,
                'comision' => $comision,
                'valoracion' => $valoracion
            ];
        }

        $promedioValoracion = $citasConValoracion > 0 ? round($sumaValoraciones / $citasConValoracion, 1) : 0.0;

        $estadisticas = [
            'total_citas' => count($citasAtendidas),
            'comisiones_totales' => $totalComisiones,
            'propinas' => 0, 
            'valoracion_promedio' => $promedioValoracion
        ];

        return view('empleado.Citas_atendidas', compact('citasAtendidas', 'estadisticas'));
    }

    public function historial() 
    { 
        $empleadoId = $this->getEmpleadoId();
        if (!$empleadoId) return redirect('/')->with('error', 'Acceso denegado.');

        Carbon::setLocale('es');

        $citasQuery = DB::table('tbl_cita')
            ->leftJoin('tbl_cliente', 'tbl_cita.cit_clienteId', '=', 'tbl_cliente.cli_id')
            ->leftJoin('tbl_usuario as u_cli', 'tbl_cliente.cli_usuarioId', '=', 'u_cli.usr_id')
            ->leftJoin('tbl_servicio', 'tbl_cita.cit_servicioId', '=', 'tbl_servicio.srv_id')
            ->where('cit_empleadoId', $empleadoId)
            ->where('cit_estadoCita', 'Completada')
            ->select(
                'tbl_cita.*', 
                'tbl_cliente.cli_id', 
                'tbl_servicio.srv_nombre as servicio_principal'
            )
            ->orderBy('cit_fechaCita', 'desc')
            ->get();

        $historialMensual = [];
        $serviciosRanking = [];
        $clientesUnicos = [];
        $totalCitas = 0; 
        $totalComisiones = 0; 
        $totalMinutos = 0;
        $sumaValoraciones = 0; 
        $citasConValoracion = 0;

        foreach ($citasQuery as $cita) {
            $fechaObj = Carbon::parse($cita->cit_fechaCita);
            $mesKey = $fechaObj->format('Y-m'); 
            $mesNombre = ucfirst($fechaObj->translatedFormat('F Y')); 
            
            $comision = (float) $cita->cit_comisionGanada;
            $duracion = (int) ($cita->cit_duracionTotal ?? 30);
            
            // Usamos el dato real
            $valoracion = isset($cita->cit_calificacion) ? (float) $cita->cit_calificacion : 0;
            
            $totalCitas++;
            $totalComisiones += $comision;
            $totalMinutos += $duracion;
            if ($cita->cli_id) $clientesUnicos[$cita->cli_id] = true;
            if ($valoracion > 0) { $sumaValoraciones += $valoracion; $citasConValoracion++; }

            if (!isset($historialMensual[$mesKey])) {
                $historialMensual[$mesKey] = [
                    'mes_texto' => $mesNombre, 'mes_sort' => $mesKey, 'citas' => 0, 'comisiones' => 0,
                    'propinas' => 0, 'minutos' => 0, 'suma_val' => 0, 'count_val' => 0, 'servicios' => []
                ];
            }
            
            $historialMensual[$mesKey]['citas']++;
            $historialMensual[$mesKey]['comisiones'] += $comision;
            $historialMensual[$mesKey]['minutos'] += $duracion;
            if ($valoracion > 0) {
                $historialMensual[$mesKey]['suma_val'] += $valoracion;
                $historialMensual[$mesKey]['count_val']++;
            }
            
            $servPrincipal = explode('+', $cita->cit_nombres_servicios ?? $cita->servicio_principal ?? 'General')[0];
            $servPrincipal = trim($servPrincipal);
            
            if (!isset($historialMensual[$mesKey]['servicios'][$servPrincipal])) {
                $historialMensual[$mesKey]['servicios'][$servPrincipal] = 0;
            }
            $historialMensual[$mesKey]['servicios'][$servPrincipal]++;

            if (!isset($serviciosRanking[$servPrincipal])) $serviciosRanking[$servPrincipal] = 0;
            $serviciosRanking[$servPrincipal]++;
        }

        $tablaMensual = [];
        foreach ($historialMensual as $mes) {
            $topService = 'N/A'; $maxCount = 0;
            foreach ($mes['servicios'] as $srv => $count) {
                if ($count > $maxCount) { $maxCount = $count; $topService = $srv; }
            }
            $tablaMensual[] = [
                'mes' => $mes['mes_texto'], 
                'sort' => $mes['mes_sort'], 
                'citas' => $mes['citas'],
                'comisiones' => $mes['comisiones'], 
                'propinas' => $mes['propinas'],
                'horas' => round($mes['minutos'] / 60, 1),
                'valoracion' => $mes['count_val'] > 0 ? round($mes['suma_val'] / $mes['count_val'], 1) : 0.0,
                'servicio_top' => $topService
            ];
        }
        
        usort($tablaMensual, function($a, $b) { return strcmp($b['sort'], $a['sort']) * -1; });

        arsort($serviciosRanking);
        $topServicios = array_slice($serviciosRanking, 0, 5, true);

        $estadisticas = [
            'total_citas' => $totalCitas, 
            'comisiones_totales' => $totalComisiones, 
            'propinas_totales' => 0,
            'horas_trabajadas' => round($totalMinutos / 60, 1), 
            'clientes_unicos' => count($clientesUnicos),
            'valoracion_promedio' => $citasConValoracion > 0 ? round($sumaValoraciones / $citasConValoracion, 1) : 0.0
        ];

        return view('empleado.historial_completo', compact('estadisticas', 'tablaMensual', 'topServicios'));
    }

    public function comisiones()
    {
        $empleadoId = $this->getEmpleadoId();
        if (!$empleadoId) return redirect('/')->with('error', 'Acceso denegado.');

        Carbon::setLocale('es');
        $now = Carbon::now('America/Guayaquil');
        $inicioMes = $now->copy()->startOfMonth()->format('Y-m-d');
        $finMes = $now->copy()->endOfMonth()->format('Y-m-d');
        
        $diasRestantes = $now->diffInDays($now->copy()->endOfMonth(), false) + 1;

        $citasMesActual = DB::table('tbl_cita')
            ->where('cit_empleadoId', $empleadoId)
            ->where('cit_estadoCita', 'Completada')
            ->whereBetween('cit_fechaCita', [$inicioMes . ' 00:00:00', $finMes . ' 23:59:59'])
            ->get();

        $ventasEsteMes = (float) $citasMesActual->sum('cit_precio');
        $comisionEsteMes = (float) $citasMesActual->sum('cit_comisionGanada');

        $metaVentas = 1500.00;
        $porcentajeMeta = $metaVentas > 0 ? min(round(($ventasEsteMes / $metaVentas) * 100), 100) : 0;
        $faltante = max(0, $metaVentas - $ventasEsteMes);

        $citasHistorial = DB::table('tbl_cita')
            ->where('cit_empleadoId', $empleadoId)
            ->where('cit_estadoCita', 'Completada')
            ->where('cit_fechaCita', '<', $inicioMes . ' 00:00:00')
            ->orderBy('cit_fechaCita', 'desc')
            ->get();

        $historialBruto = [];
        foreach ($citasHistorial as $cita) {
            $mesKey = Carbon::parse($cita->cit_fechaCita)->format('Y-m');
            $mesNombre = ucfirst(Carbon::parse($cita->cit_fechaCita)->translatedFormat('F Y'));
            
            if (!isset($historialBruto[$mesKey])) {
                $historialBruto[$mesKey] = [
                    'mes' => $mesNombre,
                    'sort' => $mesKey,
                    'ventas' => 0,
                    'comision' => 0,
                    'estado' => 'Pagado',
                    'fecha_pago' => Carbon::parse($cita->cit_fechaCita)->endOfMonth()->addDays(5)->format('d/m/Y')
                ];
            }
            $historialBruto[$mesKey]['ventas'] += (float)$cita->cit_precio;
            $historialBruto[$mesKey]['comision'] += (float)$cita->cit_comisionGanada;
        }

        if ($ventasEsteMes > 0 || $comisionEsteMes > 0) {
            $historialBruto[$now->format('Y-m')] = [
                'mes' => ucfirst($now->translatedFormat('F Y')),
                'sort' => $now->format('Y-m'),
                'ventas' => $ventasEsteMes,
                'comision' => $comisionEsteMes,
                'estado' => 'Pendiente',
                'fecha_pago' => null
            ];
        }

        usort($historialBruto, function($a, $b) {
            return strcmp($b['sort'], $a['sort']) * -1;
        });

        $bonos = [
            ['titulo' => 'Meta de Ventas Mensual', 'desc' => 'Alcanza $1,500 en ventas', 'bono' => 50, 'alcanzado' => $ventasEsteMes >= 1500, 'icono' => '📊'],
            ['titulo' => 'Clientes Nuevos', 'desc' => '5+ clientes nuevos en el mes', 'bono' => 30, 'alcanzado' => false, 'icono' => '👥'],
            ['titulo' => 'Valoración 5 Estrellas', 'desc' => '20+ valoraciones perfectas', 'bono' => 25, 'alcanzado' => false, 'icono' => '⭐'],
        ];

        return view('empleado.Vista_Comisiones', compact(
            'ventasEsteMes', 'comisionEsteMes', 'metaVentas', 'porcentajeMeta', 'faltante', 'diasRestantes', 'historialBruto', 'bonos'
        ));
    }

    public function actualizarEstadoCita(Request $request, $id)
    {
        $empleadoId = $this->getEmpleadoId();
        if (!$empleadoId) return response()->json(['success' => false, 'message' => 'No autorizado'], 403);

        $nuevoEstado = $request->input('estado');

        try {
            DB::table('tbl_cita')
                ->where('cit_id', $id)
                ->where('cit_empleadoId', $empleadoId) 
                ->update(['cit_estadoCita' => $nuevoEstado]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // =========================================================
    // CALIFICACIONES Y RESEÑAS
    // =========================================================
    public function calificaciones()
    {
        $empleadoId = $this->getEmpleadoId();
        if (!$empleadoId) return redirect('/')->with('error', 'Acceso denegado.');

        Carbon::setLocale('es');
        $inicioMes = Carbon::now('America/Guayaquil')->startOfMonth()->format('Y-m-d');

        // Ya podemos buscar directamente las citas que tengan una calificación
        $citasConReview = DB::table('tbl_cita')
            ->leftJoin('tbl_cliente', 'tbl_cita.cit_clienteId', '=', 'tbl_cliente.cli_id')
            ->leftJoin('tbl_usuario as u_cli', 'tbl_cliente.cli_usuarioId', '=', 'u_cli.usr_id')
            ->leftJoin('tbl_servicio', 'tbl_cita.cit_servicioId', '=', 'tbl_servicio.srv_id')
            ->where('cit_empleadoId', $empleadoId)
            ->where('cit_estadoCita', 'Completada')
            ->whereNotNull('cit_calificacion') // Trae solo las calificadas
            ->select(
                'tbl_cita.cit_id',
                'tbl_cita.cit_fechaCita',
                'tbl_cita.cit_calificacion',
                'tbl_cita.cit_comentario',
                'tbl_cita.cit_respuesta_empleado',
                DB::raw("CONCAT(u_cli.usr_nombre, ' ', u_cli.usr_apellido) as cliente_nombre"),
                'tbl_servicio.srv_nombre as servicio_principal'
            )
            ->orderBy('cit_fechaCita', 'desc')
            ->get();

        $calificaciones = [];
        $distribucion = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        $sumaTotal = 0;
        $sumaMesActual = 0;
        $countMesActual = 0;
        $serviciosCalificados = [];

        foreach ($citasConReview as $cita) {
            $fecha = Carbon::parse($cita->cit_fechaCita);
            $estrellas = (int) $cita->cit_calificacion;
            
            // Validar que las estrellas estén entre 1 y 5
            if ($estrellas < 1) $estrellas = 1;
            if ($estrellas > 5) $estrellas = 5;

            $servicio = explode('+', $cita->servicio_principal)[0] ?? 'Servicio';

            $calificaciones[] = [
                'id' => $cita->cit_id,
                'cliente' => trim($cita->cliente_nombre) ?: 'Cliente',
                'servicio' => trim($servicio),
                'fecha' => $fecha->translatedFormat('d M Y'),
                'calificacion' => $estrellas,
                'comentario' => $cita->cit_comentario ?? 'Sin comentario.',
                'respuesta' => $cita->cit_respuesta_empleado
            ];

            $distribucion[$estrellas]++;
            $sumaTotal += $estrellas;

            if ($fecha->format('Y-m-d') >= $inicioMes) {
                $sumaMesActual += $estrellas;
                $countMesActual++;
            }

            if(!isset($serviciosCalificados[$servicio])) {
                $serviciosCalificados[$servicio] = ['suma' => 0, 'count' => 0];
            }
            $serviciosCalificados[$servicio]['suma'] += $estrellas;
            $serviciosCalificados[$servicio]['count']++;
        }

        $totalReview = count($calificaciones);
        $promedioGlobal = $totalReview > 0 ? round($sumaTotal / $totalReview, 1) : 0;
        $promedioMes = $countMesActual > 0 ? round($sumaMesActual / $countMesActual, 1) : $promedioGlobal;

        $mejorServicio = 'N/A';
        $maxPromedioServicio = 0;
        foreach($serviciosCalificados as $serv => $data) {
            $prom = $data['suma'] / $data['count'];
            if($prom >= $maxPromedioServicio) {
                $maxPromedioServicio = $prom;
                $mejorServicio = $serv;
            }
        }

        $estadisticas = [
            'promedio' => $promedioGlobal,
            'total_calificaciones' => $totalReview,
            'distribucion' => $distribucion,
            'servicio_mejor_calificado' => Str::limit($mejorServicio, 20),
            'mes_actual' => $promedioMes
        ];

        return view('empleado.calificaciones', compact('calificaciones', 'estadisticas'));
    }

    public function responderCalificacion(Request $request, $id)
    {
        $empleadoId = $this->getEmpleadoId();
        if (!$empleadoId) return response()->json(['success' => false, 'message' => 'No autorizado']);

        try {
            DB::table('tbl_cita')
                ->where('cit_id', $id)
                ->where('cit_empleadoId', $empleadoId)
                ->update(['cit_respuesta_empleado' => $request->respuesta]);
            
            // === LÓGICA DE NOTIFICACIÓN AL CLIENTE ===
            $citaActualizada = DB::table('tbl_cita')->where('cit_id', $id)->first();
            if ($citaActualizada) {
                $cliente = DB::table('tbl_cliente')->where('cli_id', $citaActualizada->cit_clienteId)->first();
                if ($cliente) {
                    $usuarioCliente = Usuario::find($cliente->cli_usuarioId);
                    if ($usuarioCliente) {
                        $nombreEmpleado = Auth::user()->usr_nombre; 
                        $usuarioCliente->notify(new RespuestaEmpleado($nombreEmpleado, $citaActualizada->cit_id));
                    }
                }
            }
            // ==========================================

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al guardar.']);
        }
    }
    // En tu Controlador de Citas
public function obtenerHorasDisponibles(Request $request) {
        $fecha = $request->fecha;
        $empleadoId = $request->empleado_id;
        $duracionCitaNueva = $request->duracion_total;

        $minutosOcupados = DB::table('tbl_cita')
            ->where('cit_empleadoId', $empleadoId)
            ->whereDate('cit_fechaCita', $fecha)
            ->where('cit_estadoCita', '!=', 'Cancelada')
            ->sum('cit_duracionTotal');

        $jornadaMaxima = 480; // 8 horas legales
        
        if (($minutosOcupados + $duracionCitaNueva) > $jornadaMaxima) {
            return response()->json([
                'mensaje' => 'El profesional ha alcanzado su límite de jornada legal para este día.',
                'horas' => []
            ]);
        }
        // Aquí seguiría la lógica de retorno de horas libres...
    }

    // =========================================================
    // INVENTARIO: REPORTE DE FALTAS
    // =========================================================
    public function reportarFalta()
    {
        $empleadoId = $this->getEmpleadoId();
        if (!$empleadoId) return redirect('/')->with('error', 'Acceso denegado.');

        // 1. CARGAR PRODUCTOS (Ya con los nombres exactos de tu BD)
        $productos = DB::table('tbl_producto')
            ->leftJoin('tbl_sucursal', 'tbl_producto.prd_sucursalId', '=', 'tbl_sucursal.suc_id')
            ->select(
                'tbl_producto.prd_id as pro_id',          
                'tbl_producto.prd_nombre as pro_nombre',  
                'tbl_producto.prd_stockActual as pro_stock', // <-- CORREGIDO AQUÍ
                'tbl_sucursal.suc_nombre'
            )
            ->where('tbl_producto.prd_estado', 'A') // <-- Según tu imagen, los activos tienen una 'A'
            ->orderBy('tbl_producto.prd_nombre', 'asc')
            ->get();

        // 2. CARGAR EL HISTORIAL 
        $reportes = DB::table('tbl_reporte_falta')
            ->join('tbl_producto', 'tbl_reporte_falta.rep_productoId', '=', 'tbl_producto.prd_id')
            ->where('rep_empleadoId', $empleadoId)
            ->select('tbl_reporte_falta.*', 'tbl_producto.prd_nombre as pro_nombre')
            ->orderBy('rep_fecha', 'desc')
            ->get();

        // 3. Estadísticas rápidas
        $estadisticas = [
            'total_reportes' => count($reportes),
            'pendientes' => $reportes->where('rep_estado', 'Pendiente')->count(),
            'atendidos' => $reportes->where('rep_estado', 'Atendido')->count(),
        ];

        return view('empleado.reportar_falta', compact('productos', 'reportes', 'estadisticas'));
    }

    public function guardarReporteFalta(Request $request)
    {
        $empleadoId = $this->getEmpleadoId();
        if (!$empleadoId) return response()->json(['success' => false, 'message' => 'No autorizado']);

        try {
            // VERIFICACIÓN INTELIGENTE: ¿Realmente falta este producto?
            $producto = DB::table('tbl_producto')->where('prd_id', $request->producto_id)->first();
            
            // Si el stock es mayor a 5, bloqueamos la petición
            if ($producto && $producto->prd_stockActual > 5) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Este producto aún tiene stock suficiente (' . $producto->prd_stockActual . ' unidades). No es necesario reportarlo todavía.'
                ]);
            }

            // Si pasa la prueba (stock es 5 o menos), guardamos el reporte
            DB::table('tbl_reporte_falta')->insert([
                'rep_empleadoId' => $empleadoId,
                'rep_productoId' => $request->producto_id,
                'rep_nivel_urgencia' => $request->urgencia,
                'rep_comentario' => $request->comentario,
                'rep_estado' => 'Pendiente',
                'rep_fecha' => Carbon::now('America/Guayaquil')->format('Y-m-d H:i:s')
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al guardar el reporte: ' . $e->getMessage()]);
        }
    }


    // =========================================================
    // INVENTARIO: VENTAS DE PRODUCTOS (RETAIL)
    // =========================================================
    public function ventas()
    {
        $empleadoId = $this->getEmpleadoId();
        if (!$empleadoId) return redirect('/')->with('error', 'Acceso denegado.');

        Carbon::setLocale('es');
        $mesActual = Carbon::now('America/Guayaquil')->format('Y-m');

        // 1. Cargar productos con stock
        $productos = DB::table('tbl_producto')
            ->select('prd_id', 'prd_nombre', 'prd_stockActual', 'prd_precioVenta')
            ->where('prd_estado', 'A')
            ->where('prd_stockActual', '>', 0)
            ->orderBy('prd_nombre', 'asc')
            ->get();

        // 2. NUEVO: Cargar lista de clientes para el desplegable
        $clientes = DB::table('tbl_cliente')
            ->join('tbl_usuario', 'tbl_cliente.cli_usuarioId', '=', 'tbl_usuario.usr_id')
            ->select('tbl_cliente.cli_id', DB::raw("CONCAT(tbl_usuario.usr_nombre, ' ', tbl_usuario.usr_apellido) as nombre"))
            ->orderBy('nombre', 'asc')
            ->get();

        // 3. Historial de ventas (Ahora trae también el nombre del cliente)
        $historialVentas = DB::table('tbl_venta_producto')
            ->join('tbl_producto', 'tbl_venta_producto.ven_productoId', '=', 'tbl_producto.prd_id')
            ->leftJoin('tbl_cliente', 'tbl_venta_producto.ven_clienteId', '=', 'tbl_cliente.cli_id')
            ->leftJoin('tbl_usuario', 'tbl_cliente.cli_usuarioId', '=', 'tbl_usuario.usr_id')
            ->where('ven_empleadoId', $empleadoId)
            ->select(
                'tbl_venta_producto.*', 
                'tbl_producto.prd_nombre',
                DB::raw("CONCAT(tbl_usuario.usr_nombre, ' ', tbl_usuario.usr_apellido) as cliente_nombre")
            )
            ->orderBy('ven_fecha', 'desc')
            ->get();

        // 4. Calcular comisiones y totales del mes actual
        $ventasMes = $historialVentas->filter(function($venta) use ($mesActual) {
            return Carbon::parse($venta->ven_fecha)->format('Y-m') === $mesActual;
        });

        $estadisticas = [
            'total_items_mes' => $ventasMes->sum('ven_cantidad'),
            'total_dinero_mes' => $ventasMes->sum('ven_total'),
            'total_comision_mes' => $ventasMes->sum('ven_comisionEmpleado'),
        ];

        return view('empleado.ventas', compact('productos', 'clientes', 'historialVentas', 'estadisticas'));
    }

    public function registrarVenta(Request $request)
    {
        $empleadoId = $this->getEmpleadoId();
        if (!$empleadoId) return response()->json(['success' => false, 'message' => 'No autorizado']);

        $productoId = $request->producto_id;
        $cantidad = (int) $request->cantidad;
        $clienteId = $request->cliente_id ? $request->cliente_id : null; // NUEVO: Atrapamos al cliente

        try {
            DB::beginTransaction();

            $producto = DB::table('tbl_producto')->where('prd_id', $productoId)->lockForUpdate()->first();

            if (!$producto) throw new \Exception('Producto no encontrado.');
            if ($producto->prd_stockActual < $cantidad) throw new \Exception("Stock insuficiente. Solo quedan {$producto->prd_stockActual} unidades.");

            $precioUnitario = (float) $producto->prd_precioVenta;
            $total = $precioUnitario * $cantidad;
            $comision = $total * 0.10; // 10% de comisión

            // Registrar la venta incluyendo al cliente
            DB::table('tbl_venta_producto')->insert([
                'ven_empleadoId' => $empleadoId,
                'ven_productoId' => $productoId,
                'ven_clienteId' => $clienteId, // Se guarda el cliente
                'ven_cantidad' => $cantidad,
                'ven_precioUnitario' => $precioUnitario,
                'ven_total' => $total,
                'ven_comisionEmpleado' => $comision,
                'ven_fecha' => Carbon::now('America/Guayaquil')->format('Y-m-d H:i:s')
            ]);

            // Descontar el stock
            DB::table('tbl_producto')->where('prd_id', $productoId)->decrement('prd_stockActual', $cantidad);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Venta registrada correctamente.',
                'comision' => $comision
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // =========================================================
    // PERFIL DEL EMPLEADO
    // =========================================================
    public function perfil()
    {
        $user = Auth::user();
        $empleadoId = $this->getEmpleadoId();
        if (!$empleadoId) return redirect('/')->with('error', 'Acceso denegado.');

        Carbon::setLocale('es');
        $hoy = Carbon::now('America/Guayaquil')->toDateString();

        // 1. Datos del empleado
        $empleado = DB::table('tbl_empleado')
            ->join('tbl_usuario', 'tbl_empleado.emp_usuarioId', '=', 'tbl_usuario.usr_id')
            ->where('tbl_empleado.emp_id', $empleadoId)
            ->select('tbl_usuario.*', 'tbl_empleado.*')
            ->first();

        // 2. Historial para estadísticas
        $citasCompletadas = DB::table('tbl_cita')
            ->where('cit_empleadoId', $empleadoId)
            ->where('cit_estadoCita', 'Completada')
            ->get();

        $totalGanado = $citasCompletadas->sum('cit_comisionGanada');
        $promedioValoracion = DB::table('tbl_cita')
            ->where('cit_empleadoId', $empleadoId)
            ->whereNotNull('cit_calificacion')
            ->avg('cit_calificacion') ?? 5.0;

        // 3. Próximas Citas (Agenda)
        $proximasCitas = DB::table('tbl_cita')
            ->leftJoin('tbl_servicio', 'tbl_cita.cit_servicioId', '=', 'tbl_servicio.srv_id')
            ->leftJoin('tbl_cliente', 'tbl_cita.cit_clienteId', '=', 'tbl_cliente.cli_id')
            ->leftJoin('tbl_usuario as u_cli', 'tbl_cliente.cli_usuarioId', '=', 'u_cli.usr_id')
            ->where('cit_empleadoId', $empleadoId)
            ->whereDate('cit_fechaCita', '>=', $hoy)
            ->whereIn('cit_estadoCita', ['Pendiente', 'Confirmada'])
            ->select(
                'tbl_cita.*', 
                'tbl_servicio.srv_nombre',
                DB::raw("CONCAT(u_cli.usr_nombre, ' ', u_cli.usr_apellido) as cliente_nombre")
            )
            ->orderBy('cit_fechaCita', 'asc')
            ->limit(5)
            ->get();

        return view('empleado.perfil', compact('empleado', 'citasCompletadas', 'totalGanado', 'promedioValoracion', 'proximasCitas'));
    }

    public function actualizarPerfil(Request $request)
    {
        $user = Auth::user();
        
        try {
            DB::table('tbl_usuario')
                ->where('usr_id', $user->usr_id)
                ->update([
                    'usr_nombre' => $request->nombre,
                    'usr_apellido' => $request->apellido,
                    'usr_telefono' => $request->telefono,
                    'usr_email' => $request->email
                ]);

            return response()->json(['success' => true, 'message' => 'Perfil actualizado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()]);
        }
    }

    public function cambiarPassword(Request $request)
    {
        $user = Auth::user();

        // Validar contraseña
        if(strlen($request->password) < 6) {
            return response()->json(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres']);
        }

        try {
            DB::table('tbl_usuario')
                ->where('usr_id', $user->usr_id)
                ->update([
                    'usr_password' => bcrypt($request->password) // Encriptamos la contraseña
                ]);

            return response()->json(['success' => true, 'message' => 'Contraseña actualizada por seguridad']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al cambiar contraseña']);
        }
    }
}