<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class CitaController extends Controller
{
    public function index() { return view('admin.reservas.lista_citas'); }

    public function data()
    {
        $citas = DB::table('tbl_cita')
            ->leftJoin('tbl_cliente', 'tbl_cita.cit_clienteId', '=', 'tbl_cliente.cli_id')
            ->leftJoin('tbl_usuario as u_cli', 'tbl_cliente.cli_usuarioId', '=', 'u_cli.usr_id')
            ->leftJoin('tbl_empleado', 'tbl_cita.cit_empleadoId', '=', 'tbl_empleado.emp_id')
            ->leftJoin('tbl_usuario as u_emp', 'tbl_empleado.emp_usuarioId', '=', 'u_emp.usr_id')
            ->leftJoin('tbl_servicio', 'tbl_cita.cit_servicioId', '=', 'tbl_servicio.srv_id')
            ->leftJoin('tbl_sucursal', 'tbl_cita.cit_sucursalId', '=', 'tbl_sucursal.suc_id')
            ->select(
                'tbl_cita.*',
                DB::raw("CONCAT(u_cli.usr_nombre, ' ', u_cli.usr_apellido) as cliente_nombre"),
                'u_cli.usr_telefono as cliente_telefono',
                DB::raw("CONCAT(u_emp.usr_nombre, ' ', u_emp.usr_apellido) as empleado_nombre"),
                'tbl_servicio.srv_nombre as servicio_nombre',
                'tbl_sucursal.suc_nombre as sucursal_nombre'
            )
            ->orderBy('cit_fechaCita', 'desc')
            ->get();

        $citas->transform(function ($cita) {
            $cita->cliente_nombre = trim($cita->cliente_nombre) ?: 'Desconocido';
            $cita->empleado_nombre = trim($cita->empleado_nombre) ?: 'Sin Empleado';
            $cita->servicio_nombre = $cita->cit_nombres_servicios ?? ($cita->servicio_nombre ?? 'Varios Servicios');
            $cita->sucursal_nombre = $cita->sucursal_nombre ?? 'Global';
            return $cita;
        });

        return response()->json($citas);
    }

    // ========================================================================
    // MÓDULO DE CAJA (PUNTO DE VENTA / CHECKOUT)
    // ========================================================================
    
    public function getProductos() {
        return response()->json(DB::table('tbl_producto')
            ->where('prd_estado', 'A')
            ->where('prd_stockActual', '>', 0)
            ->select(
                'prd_id as id', 
                'prd_nombre as nombre', 
                'prd_precioVenta as precio', 
                'prd_stockActual as stock',
                'prd_sucursalId as sucursal_id'
            )
            ->get());
    }

    public function checkout(Request $request, $id) {
    DB::beginTransaction();
    try {
        $cita = DB::table('tbl_cita')->where('cit_id', $id)->first();
        if (!$cita || strtolower($cita->cit_estadoCita) === 'completada') {
            return response()->json(['success' => false, 'message' => 'Cita no válida o ya completada.'], 400);
        }

        $metodoPago = $request->metodo_pago;
        $productosVendidos = $request->productos ?? []; 
        $serviciosIds = $request->servicios ?? [];
        $promocionId = $request->promocion_id;
        
        if (empty($serviciosIds) && empty($productosVendidos)) {
            return response()->json(['success' => false, 'message' => 'Debe cobrar al menos un servicio o producto.'], 400);
        }

        // ─── SERVICIOS ───────────────────────────────────────────────
        $totalServicios = 0;
        $nombresServiciosCobrados = [];
        $lineasServicios = []; // Para el desglose de factura

        if (!empty($serviciosIds)) {
            $serviciosDB = DB::table('tbl_servicio')->whereIn('srv_id', $serviciosIds)->get();
            foreach ($serviciosDB as $s) {
                $precio = (float) $s->srv_precio;
                $totalServicios += $precio;
                $nombresServiciosCobrados[] = $s->srv_nombre;
                $lineasServicios[] = [
                    'nombre'   => $s->srv_nombre,
                    'cantidad' => 1,
                    'precio'   => $precio,
                    'subtotal' => $precio,
                ];
            }
        }

        // ─── DESCUENTO / PROMOCIÓN (se aplica sobre servicios) ───────
        $descuentoServicios = 0;
        if (!empty($promocionId)) {
            $promo = DB::table('tbl_promocion')->where('prm_id', $promocionId)->first();
            if ($promo && $promo->prm_estado == '1') {
                $tipo = strtolower($promo->prm_tipoDescuento);
                if ($tipo == 'porcentaje') {
                    $descuentoServicios = $totalServicios * ($promo->prm_valorDescuento / 100);
                } elseif ($tipo == 'fijo ($)' || $tipo == 'temporada') {
                    $descuentoServicios = (float) $promo->prm_valorDescuento;
                } elseif ($tipo == '2x1') {
                    $descuentoServicios = $totalServicios / 2;
                }
            }
        }

        $totalServiciosConDescuento = max(0, $totalServicios - $descuentoServicios);

        // ─── PRODUCTOS ────────────────────────────────────────────────
        $empleado = DB::table('tbl_empleado')->where('emp_id', $cita->cit_empleadoId)->first();
        $porcentajeComisionProductos = (float) ($empleado->emp_comisionProductos ?? 10);

        $totalProductos        = 0;
        $totalProductosSinIva  = 0; // productos que NO graban IVA
        $totalProductosConIva  = 0; // productos que SÍ graban IVA (precio base)
        $arrayProductosVenta   = [];
        $lineasProductos       = []; // Para el desglose de factura

        foreach ($productosVendidos as $prod) {
            $idProd          = $prod['id'];
            $cant            = (int)   $prod['cantidad'];
            $subt            = (float) $prod['subtotal'];   // precio base * cantidad
            $precioUnitario  = $subt / $cant;

            $totalProductos += $subt;

            $prodDB  = DB::table('tbl_producto')->where('prd_id', $idProd)->first();
            $grabaIva = isset($prodDB->prd_tieneIva) ? (bool) $prodDB->prd_tieneIva : true;


            if ($grabaIva) {
                $totalProductosConIva += $subt;
            } else {
                $totalProductosSinIva += $subt;
            }

            $arrayProductosVenta[] = [
                'id'             => $idProd,
                'nombre'         => $prod['nombre'],
                'cantidad'       => $cant,
                'precio_unitario'=> $precioUnitario,
                'subtotal'       => $subt,
                'graba_iva'      => $grabaIva,
            ];

            $lineasProductos[] = [
                'nombre'    => $prod['nombre'],
                'cantidad'  => $cant,
                'precio'    => $precioUnitario,
                'subtotal'  => $subt,
                'graba_iva' => $grabaIva,
            ];

            DB::table('tbl_producto')->where('prd_id', $idProd)->decrement('prd_stockActual', $cant);
        }

        // ─── IVA 15 % (Ecuador 2026) ─────────────────────────────────
        // Los precios guardados en BD son BASE (sin IVA).
        // IVA solo aplica a: servicios con descuento + productos que graban IVA.
        $tasaIva         = 0.15;

        $baseGravaIva   = $totalServiciosConDescuento + $totalProductosConIva;
        $baseExentaIva  = $totalProductosSinIva;

        $subtotalFactura = round($totalServiciosConDescuento + $totalProductos, 2);
$ivaTotal        = round($subtotalFactura * 0.15, 2);
$totalFactura    = round($subtotalFactura + $ivaTotal, 2);

        // ─── ABONO y SALDO A COBRAR EN CAJA ──────────────────────────
        $abonoPagado        = (float) ($cita->cit_abono ?? 0);
        $saldoACobrarEnCaja = max(0, round($totalFactura - $abonoPagado, 2));

        // ─── COMISIONES ───────────────────────────────────────────────
        $comisionProductosGanada = $totalProductos * ($porcentajeComisionProductos / 100);
        $comisionServiciosGanada = $this->calcularComisionGanada($serviciosIds, $totalServiciosConDescuento);
        $comisionTotalEmpleado   = $comisionServiciosGanada + $comisionProductosGanada;

        // ─── ACTUALIZAR CITA ──────────────────────────────────────────
        DB::table('tbl_cita')->where('cit_id', $id)->update([
            'cit_estadoCita'        => 'Completada',
            'cit_servicioId'        => !empty($serviciosIds) ? $serviciosIds[0] : $cita->cit_servicioId,
            'cit_servicios_ids'     => !empty($serviciosIds) ? implode(',', $serviciosIds) : null,
            'cit_nombres_servicios' => !empty($nombresServiciosCobrados) ? implode(' + ', $nombresServiciosCobrados) : null,
            'cit_precio'            => $totalServiciosConDescuento,
            'cit_promocionId'       => empty($promocionId) ? null : $promocionId,
            'cit_comisionGanada'    => $comisionTotalEmpleado,
        ]);

        // ─── INSERTAR VENTA ───────────────────────────────────────────
        $numeroFactura = 'FAC-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

        $ventaId = DB::table('tbl_venta')->insertGetId([
            'vnt_numeroFactura'  => $numeroFactura,
            'vnt_citaId'         => $id,
            'vnt_clienteId'      => $cita->cit_clienteId,
            'vnt_empleadoId'     => $cita->cit_empleadoId,
            'vnt_servicioId'     => !empty($serviciosIds) ? $serviciosIds[0] : $cita->cit_servicioId,
            'vnt_productos_desc' => json_encode($arrayProductosVenta),
            'vnt_promocionId'    => empty($promocionId) ? null : $promocionId,
            'vnt_subtotal'       => $subtotalFactura,   // base sin IVA
            'vnt_iva'            => $ivaTotal,           // 15 % sobre base gravada
            'vnt_descuento'      => round($descuentoServicios, 2),
            'vnt_total'          => $saldoACobrarEnCaja, // lo que pagó en caja
            'vnt_comisionEmpleado'=> $comisionTotalEmpleado,
            'vnt_metodoPago'     => $metodoPago,
            'vnt_fechaVenta'     => now(),
            'vnt_estado'         => 'Pagada',
        ]);

        // ─── PUNTOS DE FIDELIZACIÓN ───────────────────────────────────
        // Se ganan sobre el total real de la factura (antes de restar abono)
        $puntosGanados = floor($totalFactura);
        DB::table('tbl_cliente')
            ->where('cli_id', $cita->cit_clienteId)
            ->increment('cli_puntosFidelizacion', $puntosGanados);

        // ─── ESTADÍSTICAS EMPLEADO ────────────────────────────────────
        DB::table('tbl_empleado')->where('emp_id', $cita->cit_empleadoId)->increment('emp_citasCompletadas', 1);
        DB::table('tbl_empleado')->where('emp_id', $cita->cit_empleadoId)->increment('emp_comisionTotal', $comisionTotalEmpleado);

        DB::commit();

        // ─── CORREO ───────────────────────────────────────────────────
        try {
            $clienteObj   = DB::table('tbl_cliente')
                ->join('tbl_usuario', 'tbl_cliente.cli_usuarioId', '=', 'tbl_usuario.usr_id')
                ->where('cli_id', $cita->cit_clienteId)
                ->first();
            $estilistaObj = DB::table('tbl_usuario')->where('usr_id', $empleado->emp_usuarioId)->first();

            $datosCorreo = [
                'clienteNombre'   => $clienteObj->usr_nombre . ' ' . $clienteObj->usr_apellido,
                'numeroFactura'   => $numeroFactura,
                'servicioNombre'  => !empty($nombresServiciosCobrados)
                                        ? implode(' + ', $nombresServiciosCobrados)
                                        : $cita->cit_nombres_servicios,
                'lineasServicios' => $lineasServicios,   // desglose para la vista
                'lineasProductos' => $lineasProductos,   // desglose para la vista
                'productosJson'   => json_encode($arrayProductosVenta),
                'estilistaNombre' => $estilistaObj->usr_nombre . ' ' . $estilistaObj->usr_apellido,
                'subtotal'        => $subtotalFactura,
                'iva'             => $ivaTotal,
                'descuento'       => round($descuentoServicios, 2),
                'totalFactura'    => $totalFactura,
                'abono'           => $abonoPagado,
                'saldoPagado'     => $saldoACobrarEnCaja,
                'urlFactura'      => url('/admin/citas/' . $id . '/factura'),
            ];

            Mail::send('emails.factura_correo', $datosCorreo, function ($message) use ($clienteObj) {
                $message->to($clienteObj->usr_email)->subject('Tu comprobante de pago - StyleNow');
            });
        } catch (\Exception $e) {}

        // ─── RESPUESTA ────────────────────────────────────────────────
        return response()->json([
            'success'     => true,
            'factura'     => $numeroFactura,
            'puntos'      => $puntosGanados,
            'factura_url' => url('/admin/citas/' . $id . '/factura'),
            // Datos para mostrar el resumen en pantalla con el formato de Gabriel
            'resumen' => [
                'lineas_servicios' => $lineasServicios,
                'lineas_productos' => $lineasProductos,
                'subtotal'         => $subtotalFactura,
                'iva'              => $ivaTotal,
                'descuento'        => round($descuentoServicios, 2),
                'total_factura'    => $totalFactura,
                'abono'            => $abonoPagado,
                'saldo_pagado'     => $saldoACobrarEnCaja,
            ],
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => 'Error en Caja: ' . $e->getMessage()], 500);
    }
}
    
    public function verFactura($id)
    {
        $cita = DB::table('tbl_cita')->where('cit_id', $id)->first();
        if (!$cita) abort(404);

        $venta = DB::table('tbl_venta')->where('vnt_citaId', $id)->first();
        if (!$venta) abort(404, 'No hay venta registrada para esta cita.');

        $cliente = DB::table('tbl_cliente')
            ->join('tbl_usuario', 'tbl_cliente.cli_usuarioId', '=', 'tbl_usuario.usr_id')
            ->where('cli_id', $cita->cit_clienteId)->first();

        $estilista = DB::table('tbl_empleado')
            ->join('tbl_usuario', 'tbl_empleado.emp_usuarioId', '=', 'tbl_usuario.usr_id')
            ->where('emp_id', $cita->cit_empleadoId)->first();

        $sucursal = DB::table('tbl_sucursal')->where('suc_id', $cita->cit_sucursalId)->first();

        return view('admin.reservas.factura', compact('cita', 'venta', 'cliente', 'estilista', 'sucursal'));
    }

    // ========================================================================
    // ALGORITMO INTELIGENTE Y CÁLCULOS ESTÁNDAR
    // ========================================================================

    private function calcularComisionGanada($serviciosIds, $precioFinalCobrado) {
        if (empty($serviciosIds) || empty($precioFinalCobrado) || $precioFinalCobrado <= 0) return 0.00;

        $servicios = DB::table('tbl_servicio')
            ->leftJoin('tbl_categoriaservicio', 'tbl_servicio.srv_categoriaId', '=', 'tbl_categoriaservicio.cats_id')
            ->whereIn('srv_id', $serviciosIds)
            ->select('srv_precio', 'cats_porcentajeComision')
            ->get();

        $sumaPrecioBase = 0; $sumaComisionBase = 0;

        foreach ($servicios as $srv) {
            $precio = (float) $srv->srv_precio;
            $porcentaje = (int) ($srv->cats_porcentajeComision ?? 20);
            $sumaPrecioBase += $precio;
            $sumaComisionBase += $precio * ($porcentaje / 100);
        }

        if ($sumaPrecioBase <= 0) return 0.00;

        $porcentajeEfectivo = $sumaComisionBase / $sumaPrecioBase;
        return round($precioFinalCobrado * $porcentajeEfectivo, 2);
    }

    private function sugerirHorariosYVerificarChoque($citasExistentes, $inicioSolicitado, $duracionNueva) {
        $conflicto = false; $horaChoque = ''; $intervalosOcupados = [];
        $duracionNueva = (int) $duracionNueva; $finSolicitado = (clone $inicioSolicitado)->addMinutes($duracionNueva);

        foreach ($citasExistentes as $cita) {
            if (empty($cita->cit_fechaCita)) continue;
            $inicioExistente = Carbon::parse($cita->cit_fechaCita);
            $duracionExistente = max((int)($cita->cit_duracionTotal ?? $cita->srv_duracionMinutos ?? 30), 1);
            $finExistente = (clone $inicioExistente)->addMinutes($duracionExistente);
            $intervalosOcupados[] = ['inicio' => $inicioExistente, 'fin' => $finExistente];

            if ($inicioSolicitado < $finExistente && $finSolicitado > $inicioExistente) {
                $conflicto = true;
                if ($horaChoque === '') $horaChoque = $inicioExistente->format('H:i');
            }
        }
        if (!$conflicto) return ['conflicto' => false];

        usort($intervalosOcupados, function($a, $b) { return $a['inicio'] <=> $b['inicio']; });
        $sugerencias = [];
        $horaRevision = Carbon::parse($inicioSolicitado->toDateString() . ' 09:00:00');
        $horaCierre = Carbon::parse($inicioSolicitado->toDateString() . ' 19:00:00');
        $failsafe = 0;

        while ($horaRevision->copy()->addMinutes($duracionNueva) <= $horaCierre && count($sugerencias) < 3 && $failsafe < 50) {
            $failsafe++; $posibleFin = $horaRevision->copy()->addMinutes($duracionNueva); $choca = false;
            foreach ($intervalosOcupados as $intervalo) {
                 if ($horaRevision < $intervalo['fin'] && $posibleFin > $intervalo['inicio']) {
                    $choca = true;
                    if ($horaRevision < $intervalo['fin']) $horaRevision = $intervalo['fin']->copy(); 
                    else $horaRevision->addMinutes(15);
                    break;
                }
            }
            if (!$choca) { $sugerencias[] = "<b>" . $horaRevision->format('H:i') . "</b>"; $horaRevision->addMinutes(30); }
        }

        $sugerenciasTexto = empty($sugerencias) ? "<span style='color:#f44336;'>No hay espacio libre suficiente hoy.</span>" : "<span style='color:#4ade80;'>💡 Horarios sugeridos libres:</span><br><br>" . implode(' &nbsp;•&nbsp; ', $sugerencias);
        return ['conflicto' => true, 'mensaje' => "El horario choca con otra cita a las <b>$horaChoque</b>.<br><br>$sugerenciasTexto"];
    }
    
    private function calcularDuracionTotal($serviciosIds) {
        if (empty($serviciosIds)) return 30; 
        $duracionTotal = (int) DB::table('tbl_servicio')->whereIn('srv_id', $serviciosIds)->sum('srv_duracionMinutos');
        return $duracionTotal > 0 ? $duracionTotal : 30;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cit_clienteId'   => 'required|integer',
            'cit_empleadoId'  => 'required|integer',
            'servicios'       => 'required|array|min:1', 
            'cit_sucursalId'  => 'required|integer',
            'cit_fechaCita'   => 'required', 
            'cit_estadoCita'  => 'required',
        ]);

        if ($validator->fails()) return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        try {
            $inicioSolicitado = Carbon::parse($request->cit_fechaCita);
            $duracionTotal = $this->calcularDuracionTotal($request->servicios);
            $finSolicitado = (clone $inicioSolicitado)->addMinutes($duracionTotal);

            if ($inicioSolicitado->isSunday()) return response()->json(['success' => false, 'message' => 'El salón no atiende los días <b>Domingo</b>.'], 400);
            if ($inicioSolicitado->format('H:i:s') < '09:00:00' || $finSolicitado->format('H:i:s') > '19:00:00') return response()->json(['success' => false, 'message' => "El horario excede el cierre (19:00)."], 400);

            $citasDelDia = DB::table('tbl_cita')->where('cit_empleadoId', $request->cit_empleadoId)->whereDate('cit_fechaCita', $inicioSolicitado->toDateString())->whereNotIn('cit_estadoCita', ['Cancelada', 'cancelada'])->count();
            if ($citasDelDia >= 8) return response()->json(['success' => false, 'message' => "El empleado ya tiene la agenda llena."], 400);

            $citasExistentes = DB::table('tbl_cita')->leftJoin('tbl_servicio', 'tbl_cita.cit_servicioId', '=', 'tbl_servicio.srv_id')->where('cit_empleadoId', $request->cit_empleadoId)->whereDate('cit_fechaCita', $inicioSolicitado->toDateString())->whereNotIn('cit_estadoCita', ['Cancelada', 'cancelada'])->get();
            $verificacion = $this->sugerirHorariosYVerificarChoque($citasExistentes, $inicioSolicitado, $duracionTotal);
            if ($verificacion['conflicto']) return response()->json(['success' => false, 'message' => $verificacion['mensaje']], 400);

            $nombresArray = DB::table('tbl_servicio')->whereIn('srv_id', $request->servicios)->pluck('srv_nombre')->toArray();
            $precioCobrado = $request->cit_precio ? (float)$request->cit_precio : 0.00;
            
            // Abono desde el panel de admin si se requiere (generalmente 0)
            $abono = $request->cit_abono ? (float)$request->cit_abono : 0.00;
            
            $comisionGanada = $this->calcularComisionGanada($request->servicios, $precioCobrado);

            $data = [
                'cit_clienteId'   => $request->cit_clienteId,
                'cit_empleadoId'  => $request->cit_empleadoId,
                'cit_servicioId'  => $request->servicios[0], 
                'cit_servicios_ids' => implode(',', $request->servicios), 
                'cit_nombres_servicios' => implode(' + ', $nombresArray),
                'cit_sucursalId'  => $request->cit_sucursalId,
                'cit_fechaCita'   => $request->cit_fechaCita,
                'cit_estadoCita'  => $request->cit_estadoCita,
                'cit_duracionTotal' => $duracionTotal, 
                'cit_precio'      => $precioCobrado,
                'cit_abono'       => $abono,
                'cit_comisionGanada' => $comisionGanada, 
                'cit_promocionId' => empty($request->cit_promocionId) ? null : $request->cit_promocionId,
                'cit_fechaCreacion' => now()
            ];

            if ($request->cit_estadoCita === 'Cancelada') $data['cit_fechaCancelacion'] = now();
            $id = DB::table('tbl_cita')->insertGetId($data);

            return response()->json(['success' => true, 'id' => $id], 201);
        } catch (\Throwable $e) { return response()->json(['success' => false, 'message' => 'Error BD: ' . $e->getMessage()], 500); }
    }

    public function update(Request $request, $id)
    {
        try {
            $citaAntigua = DB::table('tbl_cita')->where('cit_id', $id)->first();
            
            $data = $request->only(['cit_clienteId', 'cit_empleadoId', 'cit_sucursalId', 'cit_fechaCita', 'cit_estadoCita']);
            if ($request->has('cit_precio')) $data['cit_precio'] = $request->cit_precio ? (float)$request->cit_precio : 0.00;
            if ($request->has('cit_promocionId')) $data['cit_promocionId'] = empty($request->cit_promocionId) ? null : $request->cit_promocionId;
            if ($request->has('cit_abono')) $data['cit_abono'] = $request->cit_abono ? (float)$request->cit_abono : 0.00;

            $serviciosNuevos = $request->servicios;
            if (empty($serviciosNuevos)) {
                $serviciosNuevos = $citaAntigua->cit_servicios_ids ? explode(',', $citaAntigua->cit_servicios_ids) : [$citaAntigua->cit_servicioId];
            }

            $precioParaCalcular = $data['cit_precio'] ?? $citaAntigua->cit_precio;
            $data['cit_comisionGanada'] = $this->calcularComisionGanada($serviciosNuevos, $precioParaCalcular);

            $duracionTotal = (int) ($citaAntigua->cit_duracionTotal ?? 30); 
            if ($request->has('servicios') && is_array($request->servicios) && count($request->servicios) > 0) {
                $duracionTotal = $this->calcularDuracionTotal($request->servicios);
                $nombresArray = DB::table('tbl_servicio')->whereIn('srv_id', $request->servicios)->pluck('srv_nombre')->toArray();
                $data['cit_servicioId'] = $request->servicios[0]; 
                $data['cit_servicios_ids'] = implode(',', $request->servicios);
                $data['cit_nombres_servicios'] = implode(' + ', $nombresArray);
                $data['cit_duracionTotal'] = $duracionTotal;
            }

            if ($request->cit_estadoCita !== 'Cancelada') {
                $inicioSolicitado = Carbon::parse($request->cit_fechaCita);
                $finSolicitado = (clone $inicioSolicitado)->addMinutes($duracionTotal);

                if ($inicioSolicitado->isSunday()) return response()->json(['success' => false, 'message' => 'Domingo cerrado.'], 400);
                if ($inicioSolicitado->format('H:i:s') < '09:00:00' || $finSolicitado->format('H:i:s') > '19:00:00') return response()->json(['success' => false, 'message' => "El horario excede el cierre."], 400);

                $citasExistentes = DB::table('tbl_cita')->leftJoin('tbl_servicio', 'tbl_cita.cit_servicioId', '=', 'tbl_servicio.srv_id')->where('cit_empleadoId', $request->cit_empleadoId)->whereDate('cit_fechaCita', $inicioSolicitado->toDateString())->whereNotIn('cit_estadoCita', ['Cancelada', 'cancelada'])->where('cit_id', '!=', $id)->get();
                $verificacion = $this->sugerirHorariosYVerificarChoque($citasExistentes, $inicioSolicitado, $duracionTotal);
                if ($verificacion['conflicto']) return response()->json(['success' => false, 'message' => $verificacion['mensaje']], 400);
            }

            $data['cit_fechaCancelacion'] = ($request->cit_estadoCita === 'Cancelada') ? now() : null;
            $data['cit_fechaModificacion'] = now();

            DB::table('tbl_cita')->where('cit_id', $id)->update($data);
            return response()->json(['success' => true]);
        } catch (\Throwable $e) { return response()->json(['success' => false, 'message' => 'Error BD: ' . $e->getMessage()], 500); }
    }

    public function destroy($id)
    {
        $cita = DB::table('tbl_cita')->where('cit_id', $id)->first();
        if ($cita && strtolower($cita->cit_estadoCita) === 'completada') return response()->json(['success' => false, 'message' => 'No se pueden eliminar citas Completadas.'], 400);
        DB::table('tbl_cita')->where('cit_id', $id)->delete();
        return response()->json(['success' => true]);
    }

    public function getClientes() { return response()->json(DB::table('tbl_cliente')->join('tbl_usuario', 'tbl_cliente.cli_usuarioId', '=', 'tbl_usuario.usr_id')->where('tbl_usuario.usr_estado', 'A')->select('tbl_cliente.cli_id as id', DB::raw("CONCAT(tbl_usuario.usr_nombre, ' ', tbl_usuario.usr_apellido) as nombre"), 'tbl_usuario.usr_telefono as telefono')->get()); }
    public function getEmpleados(Request $request) { 
        $query = DB::table('tbl_empleado')->join('tbl_usuario', 'tbl_empleado.emp_usuarioId', '=', 'tbl_usuario.usr_id')->where('tbl_usuario.usr_estado', 'A')->select('tbl_empleado.emp_id as id', DB::raw("CONCAT(tbl_usuario.usr_nombre, ' ', tbl_usuario.usr_apellido) as nombre"));
        if ($request->has('sucursal_id') && $request->sucursal_id != '') $query->where('tbl_empleado.emp_sucursalId', $request->sucursal_id);
        return response()->json($query->get());
    }
    public function getSucursales() { return response()->json(DB::table('tbl_sucursal')->where('suc_estado', '1')->select('suc_id as id', 'suc_nombre as nombre')->get()); }
    public function getServicios(Request $request)
    {
        $query = DB::table('tbl_servicio')->join('tbl_categoriaservicio', 'tbl_servicio.srv_categoriaId', '=', 'tbl_categoriaservicio.cats_id')->where('srv_estado', '1')->select('tbl_servicio.srv_id as id', 'tbl_servicio.srv_nombre as nombre', 'tbl_servicio.srv_precio as precio', 'tbl_servicio.srv_duracionMinutos as duracion', 'tbl_categoriaservicio.cats_nombre as categoria');
        if ($request->has('empleado_id') && $request->empleado_id != '') {
            $categoriasDelEmpleado = DB::table('tbl_empleado_especialidad')->where('emp_id', $request->empleado_id)->pluck('cats_id');
            $query->whereIn('tbl_servicio.srv_categoriaId', $categoriasDelEmpleado);
        }
        return response()->json($query->get());
    }
    public function getPromocionesActivas() {
        $hoy = Carbon::now('America/Guayaquil')->toDateString();
        return response()->json(DB::table('tbl_promocion')->where('prm_estado', '1')->whereDate('prm_fechaInicio', '<=', $hoy)->whereDate('prm_fechaFin', '>=', $hoy)->select('prm_id as id', 'prm_nombre as nombre', 'prm_tipoDescuento as tipo', 'prm_valorDescuento as valor')->get());
    }

    // ========================================================================
    // MARKETING AUTOMATIZADO: RECORDATORIOS DE RETORNO AL CLIENTE
    // ========================================================================
    public function enviarRecordatoriosRetorno()
    {
        $hoy = \Carbon\Carbon::now('America/Guayaquil')->startOfDay();
        $correosEnviados = 0;

        $citasAntiguas = DB::table('tbl_cita')
            ->join('tbl_cliente', 'tbl_cita.cit_clienteId', '=', 'tbl_cliente.cli_id')
            ->join('tbl_usuario', 'tbl_cliente.cli_usuarioId', '=', 'tbl_usuario.usr_id')
            ->where('cit_estadoCita', 'Completada')
            ->where('cit_fechaCita', '<', $hoy->copy()->subDays(10)) 
            ->get();

        foreach ($citasAntiguas as $cita) {
            $fechaCita = \Carbon\Carbon::parse($cita->cit_fechaCita)->startOfDay();
            $diasPasados = $fechaCita->diffInDays($hoy);

            $servicioMinuscula = strtolower($cita->cit_nombres_servicios);
            $diasObjetivo = 30; 

            if (str_contains($servicioMinuscula, 'uña') || str_contains($servicioMinuscula, 'manicure') || str_contains($servicioMinuscula, 'pedicure') || str_contains($servicioMinuscula, 'barba')) {
                $diasObjetivo = 15; 
            } elseif (str_contains($servicioMinuscula, 'keratina') || str_contains($servicioMinuscula, 'alisado')) {
                $diasObjetivo = 90; 
            } elseif (str_contains($servicioMinuscula, 'color') || str_contains($servicioMinuscula, 'tinte') || str_contains($servicioMinuscula, 'mechas')) {
                $diasObjetivo = 45; 
            }

            if ($diasPasados == $diasObjetivo) {
                
                $yaVolvio = DB::table('tbl_cita')
                    ->where('cit_clienteId', $cita->cit_clienteId)
                    ->where('cit_fechaCita', '>', $cita->cit_fechaCita)
                    ->whereNotIn('cit_estadoCita', ['Cancelada'])
                    ->exists();

                if (!$yaVolvio) {
                    $datosCorreo = [
                        'clienteNombre' => $cita->usr_nombre,
                        'servicio' => $cita->cit_nombres_servicios,
                        'urlReserva' => url('/login') 
                    ];

                    try {
                        Mail::send('emails.recordatorio_retorno', $datosCorreo, function($message) use ($cita) {
                            $message->to($cita->usr_email)->subject('¡Es hora de tu retoque en StyleNow! ✨');
                        });
                        $correosEnviados++;
                    } catch (\Exception $e) {}
                }
            }
        }

        return response()->json([
            'success' => true, 
            'message' => "Análisis CRM finalizado. Se han enviado $correosEnviados recordatorios de retorno hoy."
        ]);
    }
}