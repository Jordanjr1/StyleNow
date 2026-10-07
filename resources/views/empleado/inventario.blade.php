<?php
// Configuración de conexión a la base de datos
$host = 'localhost';
$dbname = 'NewStyle';
$username = 'root'; // Cambia según tu configuración
$password = 'root'; // Cambia según tu configuración

// Datos de prueba para categorías de producto
$categoriasProducto = [
    ['id' => 1, 'nombre' => 'Tintes y Coloración', 'descripcion' => 'Productos para coloración capilar'],
    ['id' => 2, 'nombre' => 'Tratamientos Capilares', 'descripcion' => 'Productos para tratamientos especializados'],
    ['id' => 3, 'nombre' => 'Shampoo y Acondicionador', 'descripcion' => 'Productos para lavado y cuidado diario'],
    ['id' => 4, 'nombre' => 'Productos para Estilizar', 'descripcion' => 'Geles, lacas, ceras y sprays'],
    ['id' => 5, 'nombre' => 'Cuidado Facial', 'descripcion' => 'Productos para tratamientos faciales'],
    ['id' => 6, 'nombre' => 'Manicure y Pedicure', 'descripcion' => 'Productos para uñas y cuidado de manos/pies'],
    ['id' => 7, 'nombre' => 'Herramientas y Equipos', 'descripcion' => 'Tijeras, peines, secadores, etc.'],
    ['id' => 8, 'nombre' => 'Productos Desechables', 'descripcion' => 'Guantes, toallas, capas, etc.'],
];

// Datos de prueba para proveedores
$proveedores = [
    ['id' => 1, 'nombre' => 'Distribuidora Bella S.A.', 'telefono' => '555-0101', 'email' => 'ventas@bellasa.com'],
    ['id' => 2, 'nombre' => 'Cosméticos Profesionales', 'telefono' => '555-0102', 'email' => 'info@cosmeticospro.com'],
    ['id' => 3, 'nombre' => 'Suministros Beauty', 'telefono' => '555-0103', 'email' => 'contacto@beautysupplies.com'],
    ['id' => 4, 'nombre' => 'Importadora Capilar', 'telefono' => '555-0104', 'email' => 'ventas@importadoracapilar.com'],
    ['id' => 5, 'nombre' => 'Equipos Salon Pro', 'telefono' => '555-0105', 'email' => 'soporte@salonpro.com'],
];

// Datos de prueba para sucursales
$sucursales = [
    ['id' => 1, 'nombre' => 'Centro', 'direccion' => 'Av. Principal #123'],
    ['id' => 2, 'nombre' => 'Norte', 'direccion' => 'Calle Norte #456'],
    ['id' => 3, 'nombre' => 'Sur', 'direccion' => 'Plaza Sur #789'],
];

// Datos de prueba para inventario
$inventario = [
    // Tintes y Coloración
    [
        'id' => 1, 
        'nombre' => 'Tinte Rubio Dorado #8.3', 
        'categoria' => 'Tintes y Coloración',
        'categoria_id' => 1,
        'proveedor' => 'Distribuidora Bella S.A.',
        'proveedor_id' => 1,
        'sucursal' => 'Centro',
        'sucursal_id' => 1,
        'stock_actual' => 24,
        'stock_minimo' => 10,
        'stock_maximo' => 50,
        'unidad_medida' => 'unidad',
        'precio_compra' => 8.50,
        'precio_venta' => 15.00,
        'codigo_barras' => '7501234567890',
        'ubicacion' => 'Estante A1',
        'fecha_ingreso' => '2024-02-15',
        'fecha_vencimiento' => '2025-02-15',
        'estado' => 'A'
    ],
    // ... (resto del inventario como en tu código original)
];

// Unidades de medida
$unidadesMedida = [
    'unidad', 'kg', 'g', 'mg', 'litro', 'ml', 'par', 'docena', 'paquete', 'kit', 'caja', 'rollo'
];

// Estados de producto
$estadosProducto = [
    'A' => 'Activo',
    'I' => 'Inactivo',
    'D' => 'Descontinuado',
    'R' => 'Reservado'
];

// Tipos de reportes de falta
$tiposFalta = [
    '1' => 'Robo o hurto',
    '2' => 'Daño por manipulación',
    '3' => 'Vencimiento',
    '4' => 'Desperdicio',
    '5' => 'Error en conteo',
    '6' => 'Otro'
];

// Simulación de empleado actual (en producción esto vendría de la sesión)
$empleado_actual = [
    'id' => 101,
    'nombre' => 'Ana García',
    'sucursal_id' => 1,
    'sucursal_nombre' => 'Centro',
    'turno_actual' => date('Y-m-d') . ' - Matutino'
];

// Datos de prueba para faltas reportadas
$faltas_reportadas = [
    [
        'id' => 1,
        'producto_id' => 17,
        'producto_nombre' => 'Developer 30 Volumen (500ml)',
        'sucursal_id' => 2,
        'sucursal_nombre' => 'Norte',
        'empleado_id' => 101,
        'empleado_nombre' => 'Ana García',
        'cantidad_faltante' => 3,
        'tipo_falta' => 'Error en conteo',
        'descripcion' => 'No coincide el conteo físico con el sistema',
        'fecha_reporte' => '2024-04-10 09:30:00',
        'estado' => 'pendiente',
        'fecha_resolucion' => null,
        'comentario_resolucion' => null
    ],
    [
        'id' => 2,
        'producto_id' => 18,
        'producto_nombre' => 'Secador Profesional 2000W',
        'sucursal_id' => 3,
        'sucursal_nombre' => 'Sur',
        'empleado_id' => 102,
        'empleado_nombre' => 'Carlos López',
        'cantidad_faltante' => 1,
        'tipo_falta' => 'Daño por manipulación',
        'descripcion' => 'Secador dejó de funcionar durante el turno',
        'fecha_reporte' => '2024-04-09 14:15:00',
        'estado' => 'resuelto',
        'fecha_resolucion' => '2024-04-09 16:00:00',
        'comentario_resolucion' => 'Repuesto enviado a sucursal'
    ]
];

// Si se solicita datos vía AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'get_inventory_data') {
        $sucursalId = $_POST['sucursal_id'] ?? $empleado_actual['sucursal_id'];
        $stockFilter = $_POST['stock_filter'] ?? 'all';
        
        // Filtrar inventario por sucursal del empleado
        $filteredInventory = array_filter($inventario, function($item) use ($sucursalId, $stockFilter) {
            // Filtro por sucursal
            if ($item['sucursal_id'] != $sucursalId) {
                return false;
            }
            
            // Filtro por stock
            if ($stockFilter !== 'all') {
                switch ($stockFilter) {
                    case 'low':
                        if ($item['stock_actual'] >= $item['stock_minimo']) return false;
                        break;
                    case 'critical':
                        if ($item['stock_actual'] > 0 || $item['stock_actual'] >= ($item['stock_minimo'] * 0.5)) return false;
                        break;
                    case 'out':
                        if ($item['stock_actual'] > 0) return false;
                        break;
                    case 'good':
                        if ($item['stock_actual'] < $item['stock_minimo'] || $item['stock_actual'] > $item['stock_maximo'] * 0.8) return false;
                        break;
                    case 'over':
                        if ($item['stock_actual'] <= $item['stock_maximo']) return false;
                        break;
                }
            }
            
            return true;
        });
        
        $filteredInventory = array_values($filteredInventory);
        
        // Calcular métricas específicas de la sucursal
        $metrics = calculateInventoryMetrics($filteredInventory);
        
        $response = [
            'inventario' => $filteredInventory,
            'metrics' => $metrics,
            'sucursal_actual' => $empleado_actual['sucursal_nombre'],
            'generated_at' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode($response);
        exit;
    }
    
    if ($action === 'report_missing') {
        $productoId = $_POST['producto_id'] ?? 0;
        $cantidad = $_POST['cantidad'] ?? 0;
        $tipoFalta = $_POST['tipo_falta'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        
        // Simular reporte de falta
        $nuevoReporte = [
            'id' => count($faltas_reportadas) + 1,
            'producto_id' => $productoId,
            'producto_nombre' => 'Producto ' . $productoId,
            'sucursal_id' => $empleado_actual['sucursal_id'],
            'sucursal_nombre' => $empleado_actual['sucursal_nombre'],
            'empleado_id' => $empleado_actual['id'],
            'empleado_nombre' => $empleado_actual['nombre'],
            'cantidad_faltante' => $cantidad,
            'tipo_falta' => $tipoFalta,
            'descripcion' => $descripcion,
            'fecha_reporte' => date('Y-m-d H:i:s'),
            'estado' => 'pendiente',
            'fecha_resolucion' => null,
            'comentario_resolucion' => null
        ];
        
        // En producción, esto insertaría en la base de datos
        array_push($faltas_reportadas, $nuevoReporte);
        
        echo json_encode([
            'success' => true,
            'message' => 'Falta reportada exitosamente',
            'reporte_id' => $nuevoReporte['id']
        ]);
        exit;
    }
    
    if ($action === 'get_my_reports') {
        $empleadoId = $empleado_actual['id'];
        
        // Filtrar reportes del empleado actual
        $misReportes = array_filter($faltas_reportadas, function($reporte) use ($empleadoId) {
            return $reporte['empleado_id'] == $empleadoId;
        });
        
        $misReportes = array_values($misReportes);
        
        echo json_encode([
            'success' => true,
            'reportes' => $misReportes,
            'total' => count($misReportes)
        ]);
        exit;
    }
    
    if ($action === 'get_turn_summary') {
        $sucursalId = $empleado_actual['sucursal_id'];
        $hoy = date('Y-m-d');
        
        // Filtrar inventario de la sucursal
        $inventarioSucursal = array_filter($inventario, function($item) use ($sucursalId) {
            return $item['sucursal_id'] == $sucursalId;
        });
        
        // Calcular resumen del turno
        $totalProductos = count($inventarioSucursal);
        $bajoStock = 0;
        $sinStock = 0;
        $porVencer = 0;
        
        foreach ($inventarioSucursal as $item) {
            if ($item['stock_actual'] < $item['stock_minimo']) $bajoStock++;
            if ($item['stock_actual'] == 0) $sinStock++;
            
            if ($item['fecha_vencimiento']) {
                $vencimiento = new DateTime($item['fecha_vencimiento']);
                $hoyDate = new DateTime();
                $diferencia = $hoyDate->diff($vencimiento);
                
                if ($diferencia->days <= 30 && $diferencia->invert == 0) {
                    $porVencer++;
                }
            }
        }
        
        // Reportes del día del empleado
        $reportesHoy = array_filter($faltas_reportadas, function($reporte) use ($empleado_actual, $hoy) {
            return $reporte['empleado_id'] == $empleado_actual['id'] && 
                   date('Y-m-d', strtotime($reporte['fecha_reporte'])) == $hoy;
        });
        
        echo json_encode([
            'success' => true,
            'resumen' => [
                'total_productos' => $totalProductos,
                'bajo_stock' => $bajoStock,
                'sin_stock' => $sinStock,
                'por_vencer' => $porVencer,
                'reportes_hoy' => count($reportesHoy)
            ],
            'sucursal' => $empleado_actual['sucursal_nombre'],
            'turno' => $empleado_actual['turno_actual'],
            'fecha' => $hoy
        ]);
        exit;
    }
}

function calculateInventoryMetrics($inventario) {
    $totalItems = count($inventario);
    $totalStock = 0;
    $totalValue = 0;
    $lowStockItems = 0;
    $outOfStockItems = 0;
    $expiringSoonItems = 0;
    
    foreach ($inventario as $item) {
        $totalStock += $item['stock_actual'];
        $totalValue += $item['stock_actual'] * $item['precio_compra'];
        
        if ($item['stock_actual'] < $item['stock_minimo']) {
            $lowStockItems++;
        }
        
        if ($item['stock_actual'] == 0) {
            $outOfStockItems++;
        }
        
        // Verificar productos próximos a vencer (menos de 30 días)
        if ($item['fecha_vencimiento']) {
            $vencimiento = new DateTime($item['fecha_vencimiento']);
            $hoy = new DateTime();
            $diferencia = $hoy->diff($vencimiento);
            
            if ($diferencia->days <= 30 && $diferencia->invert == 0) {
                $expiringSoonItems++;
            }
        }
    }
    
    return [
        'total_items' => $totalItems,
        'total_stock' => $totalStock,
        'total_value' => $totalValue,
        'low_stock_items' => $lowStockItems,
        'out_of_stock_items' => $outOfStockItems,
        'expiring_soon_items' => $expiringSoonItems,
        'avg_stock_level' => $totalItems > 0 ? $totalStock / $totalItems : 0
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NewStyle - Inventario de Sucursal</title>
    <style>
        :root {
            --bg-primary: #0a0a0a;
            --bg-secondary: #1a1a1a;
            --bg-card: #2a2a2a;
            --text-primary: #ffffff;
            --text-secondary: #cccccc;
            --text-muted: #888888;
            --accent-primary: #e8b4bc;
            --accent-secondary: #d48a95;
            --border-color: rgba(232, 180, 188, 0.3);
            --hover-bg: rgba(232, 180, 188, 0.1);
            --badge-low-bg: rgba(239, 68, 68, 0.2);
            --badge-low-color: #ef4444;
            --badge-critical-bg: rgba(220, 38, 38, 0.2);
            --badge-critical-color: #dc2626;
            --badge-out-bg: rgba(185, 28, 28, 0.2);
            --badge-out-color: #b91c1c;
            --badge-good-bg: rgba(16, 185, 129, 0.2);
            --badge-good-color: #10b981;
            --badge-warning-bg: rgba(245, 158, 11, 0.2);
            --badge-warning-color: #f59e0b;
        }

        [data-theme="light"] {
            --bg-primary: #f6f0e8;
            --bg-secondary: #d2d2d2;
            --bg-card: #ffffff;
            --text-primary: #000000;
            --text-secondary: #333333;
            --text-muted: #666666;
            --accent-primary: #e8b4bc;
            --accent-secondary: #d48a95;
            --border-color: rgba(232, 180, 188, 0.5);
            --hover-bg: rgba(232, 180, 188, 0.15);
            --badge-low-bg: rgba(239, 68, 68, 0.15);
            --badge-low-color: #b91c1c;
            --badge-critical-bg: rgba(220, 38, 38, 0.15);
            --badge-critical-color: #991b1b;
            --badge-out-bg: rgba(185, 28, 28, 0.15);
            --badge-out-color: #7f1d1d;
            --badge-good-bg: rgba(16, 185, 129, 0.15);
            --badge-good-color: #047857;
            --badge-warning-bg: rgba(245, 158, 11, 0.15);
            --badge-warning-color: #b45309;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--accent-primary);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--accent-primary);
        }

        .header-subtitle {
            color: var(--text-muted);
            font-size: 14px;
        }

        .employee-info {
            background: var(--bg-card);
            padding: 15px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            display: inline-block;
        }

        .employee-name {
            font-weight: 600;
            color: var(--text-primary);
        }

        .employee-details {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 5px;
        }

        /* Nav Tabs */
        .nav-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .nav-tab {
            padding: 12px 24px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-secondary);
            text-decoration: none;
        }

        .nav-tab:hover {
            border-color: var(--accent-primary);
            color: var(--accent-primary);
        }

        .nav-tab.active {
            background: var(--accent-primary);
            color: white;
            border-color: var(--accent-primary);
        }

        /* Quick Stats */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: var(--bg-card);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            text-align: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(232, 180, 188, 0.2);
        }

        .stat-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: var(--accent-primary);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-muted);
        }

        /* Main Content */
        .main-content {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 25px;
        }

        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 1fr;
            }
        }

        /* Inventory Section */
        .section {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid var(--border-color);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Filters */
        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .filter-select {
            padding: 10px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 14px;
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--accent-primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--accent-secondary);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            border-color: var(--accent-primary);
            color: var(--accent-primary);
        }

        .btn-warning {
            background: var(--badge-warning-bg);
            color: var(--badge-warning-color);
            border: 1px solid var(--badge-warning-color);
        }

        .btn-warning:hover {
            background: var(--badge-warning-color);
            color: white;
        }

        /* Inventory Table */
        .table-container {
            overflow-x: auto;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        th {
            padding: 15px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            border-bottom: 2px solid var(--border-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            font-size: 14px;
            color: var(--text-secondary);
        }

        tbody tr:hover {
            background: var(--hover-bg);
        }

        /* Badges */
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-low { background: var(--badge-low-bg); color: var(--badge-low-color); }
        .badge-critical { background: var(--badge-critical-bg); color: var(--badge-critical-color); }
        .badge-out { background: var(--badge-out-bg); color: var(--badge-out-color); }
        .badge-good { background: var(--badge-good-bg); color: var(--badge-good-color); }
        .badge-warning { background: var(--badge-warning-bg); color: var(--badge-warning-color); }

        /* Stock Bar */
        .stock-bar {
            height: 6px;
            background: var(--bg-secondary);
            border-radius: 3px;
            overflow: hidden;
            margin: 5px 0;
        }

        .stock-fill {
            height: 100%;
            border-radius: 3px;
        }

        .stock-fill.good { background: var(--badge-good-color); }
        .stock-fill.low { background: var(--badge-low-color); }
        .stock-fill.critical { background: var(--badge-critical-color); }
        .stock-fill.out { background: var(--badge-out-color); }

        /* Sidebar */
        .sidebar-section {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid var(--border-color);
        }

        .sidebar-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Report List */
        .report-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .report-item {
            background: var(--bg-secondary);
            padding: 12px;
            border-radius: 8px;
            border-left: 4px solid var(--accent-primary);
        }

        .report-product {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 5px;
        }

        .report-details {
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            justify-content: space-between;
        }

        /* Report Form */
        .report-form {
            display: none;
            background: var(--bg-secondary);
            padding: 20px;
            border-radius: 10px;
            margin-top: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 10px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-primary);
            font-size: 14px;
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .close-btn {
            font-size: 24px;
            cursor: pointer;
            color: var(--text-muted);
        }

        /* Loading */
        .loading {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
            font-size: 16px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .filters {
                grid-template-columns: 1fr;
            }
            
            .nav-tabs {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-top">
                <div>
                    <h1>📦 Inventario de Sucursal</h1>
                    <p class="header-subtitle">Control y reporte de inventario durante tu turno</p>
                </div>
                <div class="employee-info">
                    <div class="employee-name"><?php echo $empleado_actual['nombre']; ?></div>
                    <div class="employee-details">
                        Sucursal: <?php echo $empleado_actual['sucursal_nombre']; ?><br>
                        Turno: <?php echo $empleado_actual['turno_actual']; ?>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Tabs -->
            <div class="nav-tabs">
                <a href="#" class="nav-tab active" onclick="showSection('inventory')">📋 Inventario Actual</a>
                <a href="#" class="nav-tab" onclick="showSection('reports')">📝 Mis Reportes</a>
                <a href="#" class="nav-tab" onclick="showSection('summary')">📊 Resumen del Turno</a>
                <a href="#" class="nav-tab" onclick="showSection('alerts')">⚠️ Alertas</a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="quick-stats" id="quickStats">
            <!-- Las estadísticas se cargarán aquí -->
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Left Column: Main Content -->
            <div class="left-column">
                <!-- Inventory Section -->
                <div class="section" id="inventorySection">
                    <div class="section-header">
                        <div class="section-title">📦 Productos en Sucursal</div>
                        <button class="btn btn-primary" onclick="openReportModal()">
                            📝 Reportar Falta
                        </button>
                    </div>

                    <div class="filters">
                        <div class="filter-group">
                            <label class="filter-label">Estado de Stock</label>
                            <select class="filter-select" id="stockFilter" onchange="loadInventory()">
                                <option value="all">Todo el Stock</option>
                                <option value="critical">⚠️ Crítico</option>
                                <option value="low">🔻 Bajo Stock</option>
                                <option value="out">❌ Sin Stock</option>
                                <option value="good">✅ Stock Adecuado</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">Categoría</label>
                            <select class="filter-select" id="categoryFilter" onchange="loadInventory()">
                                <option value="all">Todas las Categorías</option>
                                <?php foreach ($categoriasProducto as $categoria): ?>
                                    <option value="<?php echo $categoria['id']; ?>">
                                        <?php echo htmlspecialchars($categoria['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">Buscar Producto</label>
                            <input type="text" class="form-input" id="searchInput" 
                                   placeholder="Nombre, código..." oninput="searchProducts()">
                        </div>
                    </div>

                    <div class="table-container">
                        <div class="loading" id="loadingInventory">Cargando inventario...</div>
                        <table id="inventoryTable">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Stock</th>
                                    <th>Estado</th>
                                    <th>Ubicación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="inventoryTableBody">
                                <!-- Los datos se cargarán aquí -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- My Reports Section (hidden by default) -->
                <div class="section" id="reportsSection" style="display: none;">
                    <div class="section-header">
                        <div class="section-title">📝 Mis Reportes de Faltas</div>
                    </div>

                    <div class="report-list" id="myReportsList">
                        <!-- Los reportes se cargarán aquí -->
                    </div>
                </div>

                <!-- Turn Summary Section (hidden by default) -->
                <div class="section" id="summarySection" style="display: none;">
                    <div class="section-header">
                        <div class="section-title">📊 Resumen del Turno</div>
                    </div>

                    <div id="turnSummaryContent">
                        <!-- El resumen se cargará aquí -->
                    </div>
                </div>

                <!-- Alerts Section (hidden by default) -->
                <div class="section" id="alertsSection" style="display: none;">
                    <div class="section-header">
                        <div class="section-title">⚠️ Alertas de Inventario</div>
                    </div>

                    <div id="alertsContent">
                        <!-- Las alertas se cargarán aquí -->
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar -->
            <div class="right-column">
                <!-- Quick Actions -->
                <div class="sidebar-section">
                    <div class="sidebar-title">🚀 Acciones Rápidas</div>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button class="btn btn-primary" onclick="openReportModal()">
                            📝 Reportar Falta
                        </button>
                        
                        <button class="btn btn-warning" onclick="showCriticalProducts()">
                            ⚠️ Ver Críticos
                        </button>
                    </div>
                </div>

                <!-- Recent Reports -->
                <div class="sidebar-section">
                    <div class="sidebar-title">🕐 Reportes Recientes</div>
                    <div class="report-list" id="recentReports">
                        <!-- Reportes recientes se cargarán aquí -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Modal -->
        <div class="modal" id="reportModal">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">📝 Reportar Falta en Inventario</div>
                    <div class="close-btn" onclick="closeReportModal()">×</div>
                </div>

                <form id="reportForm">
                    <div class="form-group">
                        <label class="form-label">Producto *</label>
                        <select class="form-select" id="reportProduct" required>
                            <option value="">Seleccionar producto...</option>
                            <?php 
                            $sucursalProductos = array_filter($inventario, function($item) use ($empleado_actual) {
                                return $item['sucursal_id'] == $empleado_actual['sucursal_id'];
                            });
                            foreach ($sucursalProductos as $producto): ?>
                                <option value="<?php echo $producto['id']; ?>">
                                    <?php echo htmlspecialchars($producto['nombre']); ?> 
                                    (Stock: <?php echo $producto['stock_actual']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Cantidad Faltante *</label>
                        <input type="number" class="form-input" id="reportQuantity" 
                               min="1" max="100" value="1" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tipo de Falta *</label>
                        <select class="form-select" id="reportType" required>
                            <option value="">Seleccionar tipo...</option>
                            <?php foreach ($tiposFalta as $key => $tipo): ?>
                                <option value="<?php echo $key; ?>">
                                    <?php echo htmlspecialchars($tipo); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Descripción Detallada *</label>
                        <textarea class="form-textarea" id="reportDescription" 
                                  placeholder="Describe la situación de la falta..." 
                                  rows="3" required></textarea>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="button" class="btn btn-secondary" 
                                onclick="closeReportModal()" style="flex: 1;">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" style="flex: 1;">
                            📤 Enviar Reporte
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        let currentInventory = [];
        let currentSection = 'inventory';

        // Inicializar cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            loadQuickStats();
            loadInventory();
            loadRecentReports();
            setupEventListeners();
        });

        function setupEventListeners() {
            // Tema
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            // Formulario de reporte
            document.getElementById('reportForm').addEventListener('submit', submitReport);
            
            // Cerrar modal al hacer clic fuera
            document.getElementById('reportModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeReportModal();
                }
            });
        }

        function showSection(section) {
            // Ocultar todas las secciones
            document.getElementById('inventorySection').style.display = 'none';
            document.getElementById('reportsSection').style.display = 'none';
            document.getElementById('summarySection').style.display = 'none';
            document.getElementById('alertsSection').style.display = 'none';
            
            // Remover clase active de todos los tabs
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Mostrar la sección seleccionada
            document.getElementById(section + 'Section').style.display = 'block';
            
            // Activar el tab correspondiente
            document.querySelectorAll('.nav-tab').forEach(tab => {
                if (tab.textContent.includes(getSectionTitle(section))) {
                    tab.classList.add('active');
                }
            });
            
            currentSection = section;
            
            // Cargar datos específicos de la sección
            switch(section) {
                case 'reports':
                    loadMyReports();
                    break;
                case 'summary':
                    loadTurnSummary();
                    break;
                case 'alerts':
                    loadAlerts();
                    break;
            }
        }

        function getSectionTitle(section) {
            const titles = {
                'inventory': '📋 Inventario Actual',
                'reports': '📝 Mis Reportes',
                'summary': '📊 Resumen del Turno',
                'alerts': '⚠️ Alertas'
            };
            return titles[section] || section;
        }

        function loadQuickStats() {
            // En una implementación real, esto sería una llamada AJAX
            const stats = {
                total_products: <?php echo count(array_filter($inventario, function($item) use ($empleado_actual) { 
                    return $item['sucursal_id'] == $empleado_actual['sucursal_id']; 
                })); ?>,
                low_stock: <?php echo count(array_filter($inventario, function($item) use ($empleado_actual) { 
                    return $item['sucursal_id'] == $empleado_actual['sucursal_id'] && 
                           $item['stock_actual'] < $item['stock_minimo']; 
                })); ?>,
                missing_reports: <?php echo count(array_filter($faltas_reportadas, function($reporte) use ($empleado_actual) { 
                    return $reporte['empleado_id'] == $empleado_actual['id']; 
                })); ?>,
                critical_items: <?php echo count(array_filter($inventario, function($item) use ($empleado_actual) { 
                    return $item['sucursal_id'] == $empleado_actual['sucursal_id'] && 
                           ($item['stock_actual'] == 0 || $item['stock_actual'] < $item['stock_minimo'] * 0.5); 
                })); ?>
            };

            const statsHTML = `
                <div class="stat-card">
                    <div class="stat-icon">📦</div>
                    <div class="stat-number">${stats.total_products}</div>
                    <div class="stat-label">Productos en Sucursal</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⚠️</div>
                    <div class="stat-number">${stats.low_stock}</div>
                    <div class="stat-label">Bajo Stock</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📝</div>
                    <div class="stat-number">${stats.missing_reports}</div>
                    <div class="stat-label">Reportes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🚨</div>
                    <div class="stat-number">${stats.critical_items}</div>
                    <div class="stat-label">Críticos</div>
                </div>
            `;

            document.getElementById('quickStats').innerHTML = statsHTML;
        }

        function loadInventory() {
            const stockFilter = document.getElementById('stockFilter').value;
            const categoryFilter = document.getElementById('categoryFilter').value;
            
            // Mostrar loading
            document.getElementById('loadingInventory').style.display = 'block';
            
            // Simular carga AJAX
            setTimeout(() => {
                // Filtrar inventario
                const filtered = <?php echo json_encode($inventario); ?>.filter(item => {
                    // Filtrar por sucursal del empleado
                    if (item.sucursal_id != <?php echo $empleado_actual['sucursal_id']; ?>) {
                        return false;
                    }
                    
                    // Filtrar por categoría
                    if (categoryFilter !== 'all' && item.categoria_id != categoryFilter) {
                        return false;
                    }
                    
                    // Filtrar por estado de stock
                    if (stockFilter !== 'all') {
                        const stockState = getStockState(item);
                        if (stockFilter === 'critical' && stockState !== 'critical') return false;
                        if (stockFilter === 'low' && stockState !== 'low') return false;
                        if (stockFilter === 'out' && stockState !== 'out') return false;
                        if (stockFilter === 'good' && stockState !== 'good') return false;
                    }
                    
                    return true;
                });
                
                currentInventory = filtered;
                displayInventory(currentInventory);
                document.getElementById('loadingInventory').style.display = 'none';
            }, 500);
        }

        function searchProducts() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            
            if (!searchTerm) {
                displayInventory(currentInventory);
                return;
            }
            
            const filtered = currentInventory.filter(item => {
                return item.nombre.toLowerCase().includes(searchTerm) ||
                       item.categoria.toLowerCase().includes(searchTerm) ||
                       item.codigo_barras.includes(searchTerm);
            });
            
            displayInventory(filtered);
        }

        function displayInventory(inventory) {
            const tbody = document.getElementById('inventoryTableBody');
            
            if (inventory.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <div style="font-size: 48px; margin-bottom: 10px;">📦</div>
                            <div style="font-size: 16px;">No se encontraron productos</div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            let html = '';
            inventory.forEach(item => {
                const stockState = getStockState(item);
                const badgeClass = getBadgeClass(stockState);
                const badgeText = getBadgeText(stockState);
                const stockPercentage = Math.min((item.stock_actual / item.stock_maximo) * 100, 100);
                
                html += `
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: var(--text-primary);">
                                ${item.nombre}
                            </div>
                            <div style="font-size: 12px; color: var(--text-muted);">
                                Código: ${item.codigo_barras}
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 13px;">${item.categoria}</div>
                        </td>
                        <td style="min-width: 150px;">
                            <div style="font-weight: 600; color: var(--text-primary);">
                                ${item.stock_actual} ${item.unidad_medida}
                            </div>
                            <div class="stock-bar">
                                <div class="stock-fill ${stockState}" style="width: ${stockPercentage}%"></div>
                            </div>
                            <div style="font-size: 11px; color: var(--text-muted); display: flex; justify-content: space-between;">
                                <span>Min: ${item.stock_minimo}</span>
                                <span>Max: ${item.stock_maximo}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge ${badgeClass}">${badgeText}</span>
                        </td>
                        <td>
                            <div style="font-size: 13px;">${item.ubicacion}</div>
                        </td>
                        <td>
                            <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;" 
                                    onclick="reportMissingProduct(${item.id})">
                                📝 Reportar
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        }

        function getStockState(item) {
            if (item.stock_actual === 0) return 'out';
            if (item.stock_actual < item.stock_minimo * 0.3) return 'critical';
            if (item.stock_actual < item.stock_minimo) return 'low';
            return 'good';
        }

        function getBadgeClass(state) {
            return `badge-${state}`;
        }

        function getBadgeText(state) {
            const texts = {
                'out': '❌ Sin Stock',
                'critical': '🚨 Crítico',
                'low': '⚠️ Bajo',
                'good': '✅ Adecuado'
            };
            return texts[state] || state;
        }

        function openReportModal(productId = null) {
            const modal = document.getElementById('reportModal');
            if (productId) {
                document.getElementById('reportProduct').value = productId;
            }
            modal.classList.add('active');
        }

        function closeReportModal() {
            document.getElementById('reportModal').classList.remove('active');
            document.getElementById('reportForm').reset();
        }

        function submitReport(event) {
            event.preventDefault();
            
            const productId = document.getElementById('reportProduct').value;
            const quantity = document.getElementById('reportQuantity').value;
            const type = document.getElementById('reportType').value;
            const description = document.getElementById('reportDescription').value;
            
            // Simular envío AJAX
            setTimeout(() => {
                alert('✅ Reporte enviado exitosamente');
                closeReportModal();
                loadQuickStats();
                loadRecentReports();
                if (currentSection === 'reports') {
                    loadMyReports();
                }
            }, 500);
        }

        function reportMissingProduct(productId) {
            openReportModal(productId);
        }

        function loadMyReports() {
            // Simular carga de reportes
            const misReportes = <?php echo json_encode(array_filter($faltas_reportadas, function($r) use ($empleado_actual) { 
                return $r['empleado_id'] == $empleado_actual['id']; 
            })); ?>;
            
            let html = '';
            if (misReportes.length === 0) {
                html = `
                    <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                        <div style="font-size: 48px; margin-bottom: 10px;">📝</div>
                        <div style="font-size: 16px;">No tienes reportes aún</div>
                    </div>
                `;
            } else {
                misReportes.forEach(reporte => {
                    const statusClass = reporte.estado === 'pendiente' ? 'badge-warning' : 'badge-good';
                    const statusText = reporte.estado === 'pendiente' ? '⏳ Pendiente' : '✅ Resuelto';
                    
                    html += `
                        <div class="report-item">
                            <div class="report-product">${reporte.producto_nombre}</div>
                            <div style="margin-bottom: 5px; font-size: 13px;">
                                Cantidad: ${reporte.cantidad_faltante} | 
                                Tipo: ${reporte.tipo_falta}
                            </div>
                            <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 5px;">
                                ${reporte.descripcion}
                            </div>
                            <div class="report-details">
                                <span>${formatDate(reporte.fecha_reporte)}</span>
                                <span class="badge ${statusClass}">${statusText}</span>
                            </div>
                        </div>
                    `;
                });
            }
            
            document.getElementById('myReportsList').innerHTML = html;
        }

        function loadRecentReports() {
            // Obtener reportes recientes (últimas 5)
            const recentReports = <?php echo json_encode(array_slice($faltas_reportadas, 0, 3)); ?>;
            
            let html = '';
            recentReports.forEach(reporte => {
                const timeAgo = getTimeAgo(reporte.fecha_reporte);
                
                html += `
                    <div class="report-item">
                        <div class="report-product">${reporte.producto_nombre}</div>
                        <div class="report-details">
                            <span>${reporte.empleado_nombre}</span>
                            <span>${timeAgo}</span>
                        </div>
                    </div>
                `;
            });
            
            document.getElementById('recentReports').innerHTML = html;
        }

        function loadTurnSummary() {
            // Simular carga de resumen
            const summary = {
                total_products: 24,
                low_stock: 3,
                out_of_stock: 1,
                expiring_soon: 2,
                today_reports: 1,
                average_stock: 65
            };
            
            const html = `
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 20px;">
                    <div class="stat-card">
                        <div class="stat-number">${summary.total_products}</div>
                        <div class="stat-label">Productos Totales</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${summary.low_stock}</div>
                        <div class="stat-label">Bajo Stock</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${summary.out_of_stock}</div>
                        <div class="stat-label">Sin Stock</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">${summary.expiring_soon}</div>
                        <div class="stat-label">Por Vencer</div>
                    </div>
                </div>
                
                <div style="background: var(--bg-secondary); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <div style="font-weight: 600; margin-bottom: 10px;">📈 Análisis del Turno</div>
                    <div style="font-size: 14px; color: var(--text-muted);">
                        • ${summary.today_reports} reportes de falta hoy<br>
                        • Stock promedio: ${summary.average_stock}%<br>
                        • 3 productos necesitan reorden inmediato
                    </div>
                </div>
                
                <div style="background: var(--bg-secondary); padding: 15px; border-radius: 8px;">
                    <div style="font-weight: 600; margin-bottom: 10px;">✅ Acciones Completadas</div>
                    <div style="font-size: 14px; color: var(--text-muted);">
                        • Conteo inicial completado<br>
                        • Reporte de faltas enviado<br>
                        • Productos críticos identificados
                    </div>
                </div>
            `;
            
            document.getElementById('turnSummaryContent').innerHTML = html;
        }

        function loadAlerts() {
            // Obtener productos críticos
            const criticalProducts = <?php echo json_encode(array_filter($inventario, function($item) use ($empleado_actual) { 
                return $item['sucursal_id'] == $empleado_actual['sucursal_id'] && 
                       ($item['stock_actual'] == 0 || $item['stock_actual'] < $item['stock_minimo'] * 0.3); 
            })); ?>;
            
            // Obtener productos por vencer
            
            
            let html = '';
            
            if (criticalProducts.length === 0 && expiringProducts.length === 0) {
                html = `
                    <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                        <div style="font-size: 48px; margin-bottom: 10px;">✅</div>
                        <div style="font-size: 16px;">No hay alertas críticas</div>
                    </div>
                `;
            } else {
                if (criticalProducts.length > 0) {
                    html += `
                        <div style="margin-bottom: 25px;">
                            <div style="font-weight: 600; margin-bottom: 15px; color: var(--badge-critical-color);">
                                🚨 Productos Críticos (${criticalProducts.length})
                            </div>
                    `;
                    
                    criticalProducts.forEach(product => {
                        html += `
                            <div style="background: var(--badge-critical-bg); padding: 12px; border-radius: 8px; margin-bottom: 10px;">
                                <div style="font-weight: 600;">${product.nombre}</div>
                                <div style="font-size: 13px; color: var(--text-muted);">
                                    Stock: ${product.stock_actual} | Mínimo: ${product.stock_minimo}
                                </div>
                            </div>
                        `;
                    });
                    
                    html += `</div>`;
                }
                
                if (expiringProducts.length > 0) {
                    html += `
                        <div>
                            <div style="font-weight: 600; margin-bottom: 15px; color: var(--badge-warning-color);">
                                📅 Productos por Vencer (${expiringProducts.length})
                            </div>
                    `;
                    
                    expiringProducts.forEach(product => {
                        const dias = Math.ceil((new Date(product.fecha_vencimiento) - new Date()) / (1000 * 60 * 60 * 24));
                        
                        html += `
                            <div style="background: var(--badge-warning-bg); padding: 12px; border-radius: 8px; margin-bottom: 10px;">
                                <div style="font-weight: 600;">${product.nombre}</div>
                                <div style="font-size: 13px; color: var(--text-muted);">
                                    Vence en ${dias} días | ${product.fecha_vencimiento}
                                </div>
                            </div>
                        `;
                    });
                    
                    html += `</div>`;
                }
            }
            
            document.getElementById('alertsContent').innerHTML = html;
        }

        function showCriticalProducts() {
            document.getElementById('stockFilter').value = 'critical';
            showSection('inventory');
            loadInventory();
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function getTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / (1000 * 60));
            const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
            const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
            
            if (diffMins < 60) return `Hace ${diffMins} min`;
            if (diffHours < 24) return `Hace ${diffHours} h`;
            if (diffDays === 1) return 'Ayer';
            return `Hace ${diffDays} días`;
        }
    </script>
</body>
</html>