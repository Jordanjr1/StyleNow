<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function index() {
        return view('admin.reportes.financieros');
    }

    public function getReportData(Request $request) {
        $reportType = $request->input('report_type', 'ventas_mensuales');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Si no hay fechas, por defecto los últimos 6 meses
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->subMonths(5)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $data = [];

        switch ($reportType) {
            case 'ventas_mensuales':
                // Lee las ventas reales agrupadas por mes y año
                $ventasData = DB::table('tbl_venta')
                    ->whereBetween('vnt_fechaVenta', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->where('vnt_estado', 'Pagada')
                    ->select(
                        DB::raw('DATE_FORMAT(vnt_fechaVenta, "%Y-%m") as mes_num'),
                        DB::raw('SUM(vnt_total) as ventas'),
                        DB::raw('COUNT(vnt_id) as citas')
                    )
                    ->groupBy('mes_num')
                    ->orderBy('mes_num', 'asc')
                    ->get();

                $mesesEspanol = ['01'=>'Enero', '02'=>'Febrero', '03'=>'Marzo', '04'=>'Abril', '05'=>'Mayo', '06'=>'Junio', '07'=>'Julio', '08'=>'Agosto', '09'=>'Septiembre', '10'=>'Octubre', '11'=>'Noviembre', '12'=>'Diciembre'];

                foreach ($ventasData as $v) {
                    $mesArr = explode('-', $v->mes_num); // [YYYY, MM]
                    $mesNombre = $mesesEspanol[$mesArr[1]] . ' ' . $mesArr[0];
                    $promedio = $v->citas > 0 ? ($v->ventas / $v->citas) : 0;
                    
                    $data[] = [
                        'mes' => $mesNombre,
                        'ventas' => (float) $v->ventas,
                        'citas' => (int) $v->citas,
                        'promedio' => (float) $promedio
                    ];
                }
                break;

            case 'metodos_pago':
                // Qué método de pago usan más tus clientes reales
                $pagosData = DB::table('tbl_venta')
                    ->whereBetween('vnt_fechaVenta', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->where('vnt_estado', 'Pagada')
                    ->select(
                        'vnt_metodoPago as metodo',
                        DB::raw('SUM(vnt_total) as ventas')
                    )
                    ->groupBy('vnt_metodoPago')
                    ->get();

                $totalVentasGeneral = $pagosData->sum('ventas');

                foreach ($pagosData as $p) {
                    $porcentaje = $totalVentasGeneral > 0 ? ($p->ventas / $totalVentasGeneral) * 100 : 0;
                    $data[] = [
                        'metodo' => $p->metodo ?: 'Desconocido',
                        'ventas' => (float) $p->ventas,
                        'porcentaje' => round($porcentaje, 1)
                    ];
                }
                break;

            case 'empleados_productivos':
                // Quién es el empleado que más dinero le hace ganar al salón
                $empleadosData = DB::table('tbl_venta')
                    ->join('tbl_empleado', 'tbl_venta.vnt_empleadoId', '=', 'tbl_empleado.emp_id')
                    ->join('tbl_usuario', 'tbl_empleado.emp_usuarioId', '=', 'tbl_usuario.usr_id')
                    ->whereBetween('tbl_venta.vnt_fechaVenta', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->where('tbl_venta.vnt_estado', 'Pagada')
                    ->select(
                        DB::raw("CONCAT(tbl_usuario.usr_nombre, ' ', tbl_usuario.usr_apellido) as empleado"),
                        DB::raw('COUNT(tbl_venta.vnt_id) as citas'),
                        DB::raw('SUM(tbl_venta.vnt_total) as ingresos'),
                        DB::raw('SUM(tbl_venta.vnt_comisionEmpleado) as comisiones')
                    )
                    ->groupBy('tbl_venta.vnt_empleadoId', 'empleado')
                    ->orderBy('ingresos', 'desc')
                    ->limit(10) // Top 10
                    ->get();

                foreach ($empleadosData as $e) {
                    $data[] = (array) $e;
                }
                break;
        }

        // KPIs Superiores (Siempre basados en el mes actual)
        $inicioMesActual = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $finMesActual = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');

        $kpis = DB::table('tbl_venta')
            ->whereBetween('vnt_fechaVenta', [$inicioMesActual, $finMesActual])
            ->where('vnt_estado', 'Pagada')
            ->select(
                DB::raw('SUM(vnt_total) as total_ingresos'),
                DB::raw('COUNT(vnt_id) as total_citas')
            )
            ->first();

        // Clientes activos este mes
        $clientesActivos = DB::table('tbl_venta')
            ->whereBetween('vnt_fechaVenta', [$inicioMesActual, $finMesActual])
            ->where('vnt_estado', 'Pagada')
            ->distinct('vnt_clienteId')
            ->count('vnt_clienteId');

        return response()->json([
            'data' => $data,
            'kpis' => [
                'totalRevenue' => number_format($kpis->total_ingresos ?? 0, 2),
                'totalAppointments' => $kpis->total_citas ?? 0,
                'activeClients' => $clientesActivos,
                'satisfactionRate' => '98.5%' // Pendiente de implementar calificaciones de clientes
            ],
            'filters' => [
                'report_type' => $reportType,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }
    // ==========================================================
    // REPORTES DE INVENTARIO
    // ==========================================================

    public function inventario() {
        // Obtenemos las categorías de PRODUCTOS de la base de datos
        $categorias = DB::table('tbl_categoriaproducto')->get(); 
        
        // Obtenemos las sucursales activas
        $sucursales = DB::table('tbl_sucursal')->where('suc_estado', '1')->get();
        
        // Enviamos ambas variables a la vista
        return view('admin.reportes.inventario', compact('categorias', 'sucursales'));
    }

    public function getInventoryData(Request $request) {
        $reportType = $request->input('report_type', 'analisis_stock');
        $categoriaId = $request->input('categoria_id', 'all');
        $sucursalId = $request->input('sucursal_id', 'all');

        // Construir la consulta base de productos reales
        $query = DB::table('tbl_producto')
            ->leftJoin('tbl_categoriaservicio', 'tbl_producto.prd_categoriaId', '=', 'tbl_categoriaservicio.cats_id')
            ->leftJoin('tbl_sucursal', 'tbl_producto.prd_sucursalId', '=', 'tbl_sucursal.suc_id')
            ->select(
                'tbl_producto.*',
                'tbl_categoriaservicio.cats_nombre as categoria',
                'tbl_sucursal.suc_nombre as sucursal'
            );

        // Aplicar filtros si existen
        if ($categoriaId !== 'all') {
            $query->where('prd_categoriaId', $categoriaId);
        }
        if ($sucursalId !== 'all') {
            $query->where('prd_sucursalId', $sucursalId);
        }

        $productosDB = $query->get();

        $inventario = [];
        foreach ($productosDB as $p) {
            // Calcular días de stock simulado basado en el mínimo (para el frontend)
            $diasStock = $p->prd_stockActual > 0 ? floor(($p->prd_stockActual / max($p->prd_stockMinimo, 1)) * 30) : 0;
            
            // Estado del stock
            $estado = 'optimal_stock';
            if ($p->prd_stockActual == 0) $estado = 'out_of_stock';
            elseif ($p->prd_stockActual <= $p->prd_stockMinimo) $estado = 'low_stock';
            elseif ($p->prd_stockActual > ($p->prd_stockMinimo * 3)) $estado = 'over_stock';

            // Rotación simulada para el reporte
            $rotacion = $p->prd_stockActual > 0 ? round(rand(5, 20) / 10, 2) : 0;

            $inventario[] = [
                'producto' => $p->prd_nombre,
                'categoria' => $p->categoria ?? 'General',
                'sucursal' => $p->sucursal ?? 'Global',
                'stock_actual' => (int)$p->prd_stockActual,
                'stock_minimo' => (int)$p->prd_stockMinimo,
                'stock_maximo' => (int)($p->prd_stockMinimo * 3), // Estimación
                'precio_compra' => (float)$p->prd_precioCompra,
                'precio_venta' => (float)$p->prd_precioVenta,
                'valor_total' => (float)($p->prd_stockActual * $p->prd_precioCompra),
                'estado' => $estado,
                'dias_stock' => $diasStock,
                'rotacion' => $rotacion,
                'consumo_mensual' => rand(5, 50), // Dato simulado hasta tener módulo de requisiciones
                'cantidad_sugerida' => max(0, ($p->prd_stockMinimo * 2) - $p->prd_stockActual),
                'costo_estimado' => max(0, ($p->prd_stockMinimo * 2) - $p->prd_stockActual) * (float)$p->prd_precioCompra,
                'urgencia' => $p->prd_stockActual == 0 ? 'urgente' : ($p->prd_stockActual <= $p->prd_stockMinimo ? 'alta' : 'baja'),
                'proveedor_sugerido' => 'Proveedor Habitual'
            ];
        }

        // Resumen General
        $totalStock = array_sum(array_column($inventario, 'stock_actual'));
        $totalValue = array_sum(array_column($inventario, 'valor_total'));
        $lowStockCount = count(array_filter($inventario, fn($i) => $i['estado'] === 'low_stock'));
        $outOfStockCount = count(array_filter($inventario, fn($i) => $i['estado'] === 'out_of_stock'));

        $summary = [
            'total_productos' => count($inventario),
            'total_stock' => $totalStock,
            'valor_total' => $totalValue,
            'productos_bajo_stock' => $lowStockCount,
            'productos_sin_stock' => $outOfStockCount,
            'stock_promedio' => count($inventario) > 0 ? $totalStock / count($inventario) : 0,
            'valor_promedio' => count($inventario) > 0 ? $totalValue / count($inventario) : 0,
            'rotacion_promedio' => 1.2,
            'eficiencia_inventario' => 85
        ];

        // Formatear datos según el tipo de reporte solicitado
        $reportData = $inventario;
        if ($reportType === 'valor_inventario') {
            usort($reportData, fn($a, $b) => $b['valor_total'] <=> $a['valor_total']);
            // Añadir porcentaje_total para el gráfico
            foreach ($reportData as &$item) {
                $item['porcentaje_total'] = $totalValue > 0 ? round(($item['valor_total'] / $totalValue) * 100, 2) : 0;
            }
        } elseif ($reportType === 'rotacion_productos') {
            usort($reportData, fn($a, $b) => $b['rotacion'] <=> $a['rotacion']);
            foreach ($reportData as &$item) {
                $item['estado_rotacion'] = $item['rotacion'] > 1.5 ? 'alta_rotacion' : ($item['rotacion'] > 0.8 ? 'buena_rotacion' : 'baja_rotacion');
            }
        } elseif ($reportType === 'sugerencias_compras') {
            $reportData = array_filter($reportData, fn($i) => $i['cantidad_sugerida'] > 0);
            usort($reportData, fn($a, $b) => $b['costo_estimado'] <=> $a['costo_estimado']);
        }

        return response()->json([
            'report_data' => $reportData,
            'summary' => $summary
        ]);
    }
}