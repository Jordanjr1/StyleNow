@extends('layouts.app')

@section('title', 'Reportes de Productividad y Nómina - StyleNow')

<?php
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// 1. OBTENER SUCURSALES DE LA BASE DE DATOS
$sucursalesDB = DB::table('tbl_sucursal')->get();
$sucursales = [];
foreach($sucursalesDB as $suc) {
    $sucursales[] = [
        'id' => $suc->suc_id, 
        'nombre' => $suc->suc_nombre, 
        'estado' => $suc->suc_estado == '1' ? 'A' : 'I'
    ];
}

// 2. OBTENER EMPLEADOS Y CALCULAR NÓMINA / LIQUIDACIÓN (Mes Actual)
$inicioMes = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
$finMes = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');

$empleadosData = DB::table('tbl_empleado')
    ->join('tbl_usuario', 'tbl_empleado.emp_usuarioId', '=', 'tbl_usuario.usr_id')
    ->leftJoin('tbl_sucursal', 'tbl_empleado.emp_sucursalId', '=', 'tbl_sucursal.suc_id')
    ->leftJoin('tbl_rol', 'tbl_usuario.usr_rolId', '=', 'tbl_rol.rol_id')
    ->where('tbl_usuario.usr_estado', 'A')
    ->select(
        'tbl_empleado.emp_id',
        'tbl_usuario.usr_nombre',
        'tbl_usuario.usr_apellido',
        'tbl_sucursal.suc_nombre',
        'tbl_rol.rol_nombre',
        'tbl_empleado.emp_sueldoBase',
        'tbl_empleado.emp_comisionProductos',
        'tbl_empleado.emp_metaMensual',
        'tbl_empleado.emp_bonoMeta'
    )->get();

$empleados = [];
foreach($empleadosData as $emp) {
    // Buscar todas las citas completadas del empleado este mes
    $citasStats = DB::table('tbl_cita')
        ->where('cit_empleadoId', $emp->emp_id)
        ->where('cit_estadoCita', 'Completada')
        ->whereBetween('cit_fechaCita', [$inicioMes, $finMes])
        ->selectRaw('COUNT(cit_id) as total_citas, SUM(cit_precio) as ingresos, SUM(cit_comisionGanada) as comisiones, AVG(cit_calificacion) as satisfaccion')
        ->first();

    $citas = (int)($citasStats->total_citas ?? 0);
    $ingresos = (float)($citasStats->ingresos ?? 0);
    $comisionServicios = (float)($citasStats->comisiones ?? 0);
    $satisfaccion = round((float)($citasStats->satisfaccion ?? 4.8), 1); // 4.8 por defecto si no hay calificaciones aún

    // --- CÁLCULO DEL MODELO PROFESIONAL DE NÓMINA ---
    $sueldoBase = (float)($emp->emp_sueldoBase ?? 0);
    $metaMensual = (float)($emp->emp_metaMensual ?? 0);
    $bonoMeta = (float)($emp->emp_bonoMeta ?? 0);
    
    // ¿Logró la meta del mes?
    $bonoGanado = ($metaMensual > 0 && $ingresos >= $metaMensual) ? $bonoMeta : 0;
    
    // Liquidación Total a pagar
    $liquidacionTotal = $sueldoBase + $comisionServicios + $bonoGanado;

    $empleados[] = [
        'id' => $emp->emp_id,
        'nombre' => $emp->usr_nombre . ' ' . $emp->usr_apellido,
        'sucursal' => $emp->suc_nombre ?? 'Global',
        'cargo' => $emp->rol_nombre ?? 'Estilista',
        'citas_completadas' => $citas,
        'ingresos_generados' => round($ingresos, 2),
        // Datos Financieros
        'comision_servicios' => round($comisionServicios, 2),
        'sueldo_base' => $sueldoBase,
        'bono_ganado' => $bonoGanado,
        'meta_mensual' => $metaMensual,
        'comisiones' => round($liquidacionTotal, 2), // Total final a pagar
        
        'satisfaccion' => $satisfaccion,
        'horas_trabajadas' => 160 // Jornada estándar mensual
    ];
}

$rangosFecha = [
    'mes_actual' => 'Este Mes (Liquidación)'
];
?>

@push('styles')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

    <style>
        :root {
            --bg-primary: #0a0a0a;
            --bg-secondary: #1a1a1a;
            --bg-card: #2a2a2a;
            --text-primary: #ffffff;
            --text-secondary: #cccccc;
            --text-muted: #888888;
            --accent-primary: #f4ba27;
            --accent-secondary: #fad370;
            --border-color: rgba(16, 185, 129, 0.3);
            --hover-bg: rgba(16, 185, 129, 0.1);
            --chart-color-1: #10b981;
            --chart-color-2: #3b82f6;
            --chart-color-3: #8b5cf6;
            --chart-color-4: #f59e0b;
            --chart-color-5: #ef4444;
            --badge-high-bg: rgba(16, 185, 129, 0.2);
            --badge-high-color: #10b981;
            --badge-medium-bg: rgba(245, 158, 11, 0.2);
            --badge-medium-color: #f59e0b;
            --badge-low-bg: rgba(239, 68, 68, 0.2);
            --badge-low-color: #ef4444;
            --badge-sucursal-bg: rgba(139, 92, 246, 0.2);
            --badge-sucursal-color: #8b5cf6;
        }

        [data-theme="light"] {
            --bg-primary: #f6f0e8;
            --bg-secondary: #d2d2d2;
            --bg-card: #ffffff;
            --text-primary: #000000;
            --text-secondary: #333333;
            --text-muted: #666666;
            --accent-primary: #f4ba27;
            --accent-secondary: #fad370;
            --border-color: rgba(16, 185, 129, 0.5);
            --hover-bg: rgba(16, 185, 129, 0.15);
            --badge-high-bg: rgba(16, 185, 129, 0.15);
            --badge-high-color: #047857;
            --badge-medium-bg: rgba(245, 158, 11, 0.15);
            --badge-medium-color: #b45309;
            --badge-low-bg: rgba(239, 68, 68, 0.15);
            --badge-low-color: #b91c1c;
            --badge-sucursal-bg: rgba(139, 92, 246, 0.15);
            --badge-sucursal-color: #5b21b6;
        }

        .filters-section { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; margin-bottom: 30px; }
        .filters-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .filter-group { display: flex; flex-direction: column; gap: 8px; }
        .filter-label { font-size: 14px; font-weight: 600; color: var(--text-secondary); display: flex; align-items: center; gap: 8px; }
        .filter-select, .filter-input { padding: 12px 15px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 14px; transition: all 0.3s; }
        .filter-select:focus, .filter-input:focus { outline: none; border-color: var(--accent-primary); box-shadow: 0 0 0 3px var(--hover-bg); }
        .date-range-inputs { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .filter-actions { display: flex; gap: 15px; flex-wrap: wrap; align-items: center; }
        .btn-generate { padding: 12px 24px; background: var(--accent-primary); border: none; border-radius: 10px; color: white; cursor: pointer; transition: all 0.3s; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .btn-generate:hover { background: var(--accent-secondary); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3); }
        .btn-reset { padding: 12px 24px; background: transparent; border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); cursor: pointer; transition: all 0.3s; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .btn-reset:hover { border-color: var(--accent-primary); color: var(--accent-primary); }

        .sucursal-stats { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; margin-bottom: 30px; }
        .stats-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .stats-title { font-size: 20px; font-weight: bold; color: var(--text-primary); display: flex; align-items: center; gap: 12px; }
        .sucursal-badge { padding: 6px 12px; background: var(--badge-sucursal-bg); color: var(--badge-sucursal-color); border-radius: 20px; font-size: 14px; font-weight: 600; border: 1px solid var(--badge-sucursal-color); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .stat-item { background: var(--bg-secondary); border-radius: 10px; padding: 20px; text-align: center; transition: all 0.3s; }
        .stat-item:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(16, 185, 129, 0.2); }
        .stat-value { font-size: 28px; font-weight: bold; color: var(--text-primary); margin-bottom: 5px; }
        .stat-label { font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-change { font-size: 12px; margin-top: 5px; display: flex; align-items: center; justify-content: center; gap: 5px; }
        .stat-change.positive { color: var(--badge-high-color); }
        .stat-change.negative { color: var(--badge-low-color); }

        .employees-section { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; margin-bottom: 30px; }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .section-title { display: flex; align-items: center; gap: 12px; font-size: 20px; font-weight: bold; color: var(--text-primary); }
        .employees-count { color: var(--text-muted); font-size: 14px; }
        .employees-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; }
        
        .employee-card { background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 15px; padding: 20px; transition: all 0.3s; position: relative; overflow: hidden; }
        .employee-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(16, 185, 129, 0.2); border-color: var(--accent-primary); }
        .employee-card::before { content: ''; position: absolute; top: 0; left: 0; width: 6px; height: 100%; background: linear-gradient(180deg, var(--accent-primary), var(--accent-secondary)); }
        
        .employee-header { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; }
        .employee-avatar { width: 60px; height: 60px; background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; font-weight: bold; }
        .employee-info { flex: 1; }
        .employee-name { font-size: 18px; font-weight: bold; color: var(--text-primary); margin-bottom: 5px; }
        .employee-details { display: flex; gap: 10px; flex-wrap: wrap; }
        .employee-detail { font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 4px; }
        
        .employee-stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 20px; }
        .employee-stat { background: var(--bg-card); border-radius: 10px; padding: 12px; text-align: center; }
        .employee-stat-value { font-size: 20px; font-weight: bold; color: var(--text-primary); margin-bottom: 4px; }
        .employee-stat-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        
        .progress-bar { height: 8px; background: var(--bg-card); border-radius: 4px; overflow: hidden; margin-top: 5px; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary)); border-radius: 4px; transition: width 1s ease-in-out; }
        .satisfaction-stars { display: flex; gap: 2px; margin-top: 5px; }
        .star { color: var(--accent-secondary); font-size: 14px; }
        
        .employee-actions { display: flex; gap: 10px; margin-top: 15px; }
        .btn-action { flex: 1; padding: 8px 12px; border: none; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 6px; }
        .btn-detail { background: var(--hover-bg); color: var(--accent-primary); }
        .btn-comparar { background: var(--chart-color-2); color: white; }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); }

        .ranking-section { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; }
        .ranking-table { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: var(--bg-secondary); }
        th { padding: 15px; text-align: left; font-size: 13px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; border-bottom: 2px solid var(--border-color); }
        td { padding: 18px 15px; border-bottom: 1px solid var(--border-color); font-size: 14px; color: var(--text-secondary); }
        tbody tr:hover { background: var(--hover-bg); }
        
        .ranking-badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; display: inline-block; }
        .badge-high { background: var(--badge-high-bg); color: var(--badge-high-color); }
        .badge-medium { background: var(--badge-medium-bg); color: var(--badge-medium-color); }
        .badge-low { background: var(--badge-low-bg); color: var(--badge-low-color); }
        
        .loading { text-align: center; padding: 40px; color: var(--text-muted); font-size: 16px; }

        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); z-index: 1000; align-items: center; justify-content: center; padding: 20px; }
        .modal.active { display: flex; }
        .modal-content { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 20px; padding: 30px; width: 90%; max-width: 900px; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .modal-title { font-size: 24px; font-weight: bold; color: var(--text-primary); display: flex; align-items: center; gap: 10px; }
        .close-btn { font-size: 28px; cursor: pointer; color: var(--text-muted); transition: all 0.3s; }
        .close-btn:hover { color: var(--accent-primary); transform: rotate(90deg); }
        
        .employee-detail-grid { display: grid; grid-template-columns: 300px 1fr; gap: 30px; }
        .employee-sidebar { background: var(--bg-secondary); border-radius: 15px; padding: 25px; }
        .employee-avatar-large { width: 100px; height: 100px; background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 36px; color: white; font-weight: bold; margin: 0 auto 20px; }
        
        .detail-section { margin-bottom: 30px; }
        .detail-section-title { font-size: 16px; font-weight: 600; color: var(--text-primary); margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid var(--border-color); }
        .detail-list { display: flex; flex-direction: column; gap: 10px; }
        .detail-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .detail-label { font-size: 13px; color: var(--text-muted); }
        .detail-value { font-size: 14px; font-weight: 600; color: var(--text-primary); }
        .detail-chart { height: 200px; margin-top: 15px; }

        @media (max-width: 768px) {
            .date-range-inputs { grid-template-columns: 1fr; }
            .filter-actions { flex-direction: column; }
            .btn-generate, .btn-reset { width: 100%; justify-content: center; }
            .employees-grid, .employee-detail-grid { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('topbar-left')
    <h1>📊 Productividad y Nómina</h1>
    <p>Liquidación y rendimiento en tiempo real</p>
@endsection

@section('content')
    <div class="filters-section">
        <h3 style="margin-bottom: 20px; color: var(--text-primary);">⚙️ Filtros de Productividad</h3>
        
        <div class="filters-grid">
            <div class="filter-group">
                <label class="filter-label"><span>🏬</span> Sucursal</label>
                <select class="filter-select" id="sucursalSelect">
                    <option value="all">🏢 Todas las Sucursales</option>
                    <?php foreach ($sucursales as $sucursal): ?>
                        <option value="<?php echo $sucursal['nombre']; ?>">
                            <?php echo htmlspecialchars($sucursal['nombre']); ?>
                            <?php echo $sucursal['estado'] === 'I' ? ' (Inactiva)' : ''; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label"><span>📅</span> Rango de Fecha</label>
                <select class="filter-select" id="dateRange" disabled>
                    <?php foreach ($rangosFecha as $key => $label): ?>
                        <option value="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="filter-actions">
            <button class="btn-generate" onclick="applyFilters()">📈 Filtrar Sucursal</button>
            <button class="btn-reset" onclick="resetFilters()">🔄 Ver Todas</button>
            <div style="flex: 1;"></div>
            <div style="font-size: 12px; color: var(--text-muted);">Calculado con base en citas "Completadas" del mes.</div>
        </div>
    </div>

    <div class="sucursal-stats" id="sucursalStats">
        <div class="stats-header">
            <div class="stats-title">
                📊 Resumen de Salón
                <span class="sucursal-badge" id="currentSucursalBadge">Todas las Sucursales</span>
            </div>
            <div class="employees-count" id="sucursalEmployeesCount">0 empleados</div>
        </div>
        <div class="stats-grid" id="statsGrid">
            </div>
    </div>

    <div class="employees-section">
        <div class="section-header">
            <div class="section-title">👥 Panel de Empleados</div>
            <div class="employees-count" id="employeesCount">0 encontrados</div>
        </div>
        <div class="employees-grid" id="employeesGrid">
            </div>
    </div>

    <div class="ranking-section">
        <div class="section-header">
            <div class="section-title">🏆 Top Producción</div>
            <div class="employees-count">Ordenado por Ingresos al Salón</div>
        </div>
        <div class="ranking-table">
            <table id="rankingTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Empleado</th>
                        <th>Sucursal</th>
                        <th>Citas</th>
                        <th>Ingresos al Salón</th>
                        <th>A Pagar (Nómina)</th>
                        <th>Rendimiento</th>
                        <th>Satisfacción</th>
                    </tr>
                </thead>
                <tbody id="rankingTableBody">
                    </tbody>
            </table>
        </div>
    </div>

    <div class="modal" id="employeeDetailModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalEmployeeName">
                    <span id="modalEmployeeAvatar" class="employee-avatar-large"></span>
                    Detalle
                </h2>
                <span class="close-btn" onclick="closeEmployeeModal()">×</span>
            </div>
            <div class="employee-detail-grid" id="employeeDetailContent"></div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // RECIBIR DATOS REALES DE PHP 
        const dbData = <?php echo json_encode($empleados); ?>;
        let currentEmployees = [...dbData];

        document.addEventListener('DOMContentLoaded', function() {
            renderDashboard();
        });

        function applyFilters() {
            const sucursalFiltro = document.getElementById('sucursalSelect').value;
            document.getElementById('currentSucursalBadge').textContent = sucursalFiltro === 'all' ? 'Todas las Sucursales' : sucursalFiltro;

            if (sucursalFiltro === 'all') {
                currentEmployees = [...dbData];
            } else {
                currentEmployees = dbData.filter(emp => emp.sucursal === sucursalFiltro);
            }
            renderDashboard();
        }

        function resetFilters() {
            document.getElementById('sucursalSelect').value = 'all';
            applyFilters();
        }

        function renderDashboard() {
            calculateAndDisplaySucursalMetrics(currentEmployees);
            displayEmployees(currentEmployees);
            displayRanking(currentEmployees);
        }

        function calculateAndDisplaySucursalMetrics(empleados) {
            const statsGrid = document.getElementById('statsGrid');
            if (empleados.length === 0) {
                statsGrid.innerHTML = `<div class="stat-item" style="grid-column: 1 / -1; text-align: center;"><div style="font-size: 48px;">📊</div><div style="color: var(--text-muted);">No hay empleados aquí</div></div>`;
                document.getElementById('sucursalEmployeesCount').textContent = '0 empleados';
                return;
            }
            
            const totalCitas = empleados.reduce((sum, emp) => sum + emp.citas_completadas, 0);
            const totalIngresos = empleados.reduce((sum, emp) => sum + emp.ingresos_generados, 0);
            const totalNomina = empleados.reduce((sum, emp) => sum + emp.comisiones, 0);
            const totalHoras = empleados.reduce((sum, emp) => sum + emp.horas_trabajadas, 0);
            
            statsGrid.innerHTML = `
                <div class="stat-item">
                    <div class="stat-value">${empleados.length}</div>
                    <div class="stat-label">Empleados Evaluados</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">${totalCitas}</div>
                    <div class="stat-label">Citas del Mes</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" style="color:var(--badge-high-color)">$${totalIngresos.toLocaleString(undefined, {minimumFractionDigits: 2})}</div>
                    <div class="stat-label">Ingresos Brutos</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" style="color:var(--accent-primary)">$${totalNomina.toLocaleString(undefined, {minimumFractionDigits: 2})}</div>
                    <div class="stat-label">Nómina a Pagar</div>
                </div>
            `;
            document.getElementById('sucursalEmployeesCount').textContent = `${empleados.length} empleados`;
        }

        function displayEmployees(empleados) {
            const employeesGrid = document.getElementById('employeesGrid');
            employeesGrid.innerHTML = '';
            
            if (empleados.length === 0) {
                document.getElementById('employeesCount').textContent = '0 encontrados';
                return;
            }
            
            empleados.forEach(emp => {
                const productividad = (emp.citas_completadas / emp.horas_trabajadas * 8).toFixed(1);
                const badge = getProductivityBadge(productividad);
                const starsHtml = generateStars(emp.satisfaccion);
                
                employeesGrid.innerHTML += `
                    <div class="employee-card">
                        <div class="employee-header">
                            <div class="employee-avatar">${getInitials(emp.nombre)}</div>
                            <div class="employee-info">
                                <div class="employee-name">${emp.nombre}</div>
                                <div class="employee-details">
                                    <span class="employee-detail"><span>🏬</span> ${emp.sucursal}</span>
                                    <span class="employee-detail"><span>👔</span> ${emp.cargo}</span>
                                </div>
                            </div>
                        </div>
                        <div class="employee-stats">
                            <div class="employee-stat">
                                <div class="employee-stat-value">${emp.citas_completadas}</div>
                                <div class="employee-stat-label">Citas</div>
                            </div>
                            <div class="employee-stat">
                                <div class="employee-stat-value">$${emp.ingresos_generados.toLocaleString()}</div>
                                <div class="employee-stat-label">Ingresos</div>
                            </div>
                            <div class="employee-stat">
                                <div class="employee-stat-value" style="color:var(--accent-primary)">$${emp.comisiones.toLocaleString()}</div>
                                <div class="employee-stat-label">A Pagar</div>
                            </div>
                            <div class="employee-stat">
                                <div class="employee-stat-value ${badge.class}">${productividad}</div>
                                <div class="employee-stat-label">Citas/Día</div>
                            </div>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 12px; color: var(--text-muted);">Meta de Ingresos ($${emp.meta_mensual}):</span>
                                <span style="font-size: 14px; font-weight: 600; color: var(--text-primary);">${emp.meta_mensual > 0 ? Math.round((emp.ingresos_generados / emp.meta_mensual)*100) : 0}%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: ${emp.meta_mensual > 0 ? Math.min((emp.ingresos_generados / emp.meta_mensual)*100, 100) : 0}%"></div>
                            </div>
                        </div>
                        <div class="employee-actions">
                            <button class="btn-action btn-detail" onclick="viewEmployeeDetail(${emp.id})">👁️ Ver Liquidación</button>
                        </div>
                    </div>
                `;
            });
            document.getElementById('employeesCount').textContent = `${empleados.length} encontrados`;
        }

        function displayRanking(empleados) {
            const tbody = document.getElementById('rankingTableBody');
            tbody.innerHTML = '';
            
            const sorted = [...empleados].sort((a, b) => b.ingresos_generados - a.ingresos_generados);
            
            sorted.forEach((emp, index) => {
                const prod = (emp.citas_completadas / emp.horas_trabajadas * 8).toFixed(1);
                const badge = getProductivityBadge(prod);
                
                tbody.innerHTML += `
                    <tr>
                        <td>
                            <div style="width:28px;height:28px;border-radius:50%;background:${index < 3 ? 'var(--accent-primary)' : 'var(--bg-secondary)'};color:${index < 3 ? 'white' : 'var(--text-primary)'};display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:14px;">
                                ${index + 1}
                            </div>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--accent-primary),var(--accent-secondary));display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:12px;">
                                    ${getInitials(emp.nombre)}
                                </div>
                                <div>
                                    <div style="font-weight:600;color:var(--text-primary);">${emp.nombre}</div>
                                    <div style="font-size:12px;color:var(--text-muted);">${emp.cargo}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge badge-sucursal">${emp.sucursal}</span></td>
                        <td><div style="font-weight:600;color:var(--text-primary);">${emp.citas_completadas}</div><div style="font-size:12px;color:var(--text-muted);">citas</div></td>
                        <td><div style="font-weight:600;color:var(--badge-high-color);">$${emp.ingresos_generados.toLocaleString()}</div></td>
                        <td><div style="font-weight:600;color:var(--accent-primary);">$${emp.comisiones.toLocaleString()}</div></td>
                        <td><span class="ranking-badge ${badge.class}">${prod}</span></td>
                        <td><div style="display:flex;align-items:center;gap:5px;"><div style="font-weight:600;color:var(--text-primary);">${emp.satisfaccion.toFixed(1)}</div><div style="font-size:12px;color:var(--accent-secondary);">★</div></div></td>
                    </tr>
                `;
            });
        }

        let employeeDetailChart = null;

        function viewEmployeeDetail(id) {
            const emp = currentEmployees.find(e => e.id === id);
            if (!emp) return;
            
            document.getElementById('modalEmployeeName').innerHTML = `<span class="employee-avatar-large" style="width:50px;height:50px;font-size:18px;display:inline-flex;margin:0;">${getInitials(emp.nombre)}</span> &nbsp; Nómina: ${emp.nombre}`;
            
            const metaPorcentaje = emp.meta_mensual > 0 ? Math.min(Math.round((emp.ingresos_generados / emp.meta_mensual) * 100), 100) : 0;

            document.getElementById('employeeDetailContent').innerHTML = `
                <div class="employee-sidebar">
                    <div class="detail-section">
                        <div class="detail-section-title">💰 Desglose de Liquidación</div>
                        <div class="detail-list">
                            <div class="detail-item">
                                <span class="detail-label">Sueldo Base Fijo</span>
                                <span class="detail-value">$${emp.sueldo_base.toFixed(2)}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Comisión por Servicios</span>
                                <span class="detail-value">$${emp.comision_servicios.toFixed(2)}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Bono Meta (${metaPorcentaje}%)</span>
                                <span class="detail-value" style="color:var(--badge-high-color)">+$${emp.bono_ganado.toFixed(2)}</span>
                            </div>
                            <div class="detail-item" style="border-top: 2px solid var(--border-color); padding-top: 10px; margin-top: 5px;">
                                <span class="detail-label" style="font-weight:bold; color:var(--text-primary)">Total a Pagar</span>
                                <span class="detail-value" style="font-size: 18px; color:var(--accent-primary)">$${emp.comisiones.toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                    <div class="detail-section">
                        <div class="detail-section-title">📊 Rendimiento del Mes</div>
                        <div class="detail-list">
                            <div class="detail-item">
                                <span class="detail-label">Citas Atendidas</span>
                                <span class="detail-value">${emp.citas_completadas}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Ingresos al Salón</span>
                                <span class="detail-value">$${emp.ingresos_generados.toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="detail-section">
                        <div class="detail-section-title">🎯 Progreso de Meta Mensual</div>
                        <div style="background: var(--bg-secondary); border-radius: 10px; padding: 20px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <span style="color: var(--text-muted);">Ventas actuales: <b>$${emp.ingresos_generados}</b></span>
                                <span style="color: var(--text-primary);">Meta: <b>$${emp.meta_mensual}</b></span>
                            </div>
                            <div class="progress-bar" style="height: 12px; background: rgba(0,0,0,0.3);">
                                <div class="progress-fill" style="width: ${metaPorcentaje}%; background: ${metaPorcentaje >= 100 ? 'var(--badge-high-color)' : 'linear-gradient(90deg, var(--accent-primary), var(--accent-secondary))'}"></div>
                            </div>
                            <div style="margin-top: 10px; font-size: 13px; color: var(--text-muted); text-align:right;">
                                ${metaPorcentaje >= 100 ? '¡Meta alcanzada! Bono desbloqueado 🎁' : `Faltan $${(emp.meta_mensual - emp.ingresos_generados).toFixed(2)} para el bono.`}
                            </div>
                        </div>
                    </div>
                    <div class="detail-section">
                        <div class="detail-section-title">📈 Tendencia de Citas (Simulación)</div>
                        <div class="detail-chart"><canvas id="employeeCitasChart"></canvas></div>
                    </div>
                </div>
            `;
            document.getElementById('employeeDetailModal').classList.add('active');
            
            setTimeout(() => {
                const ctx = document.getElementById('employeeCitasChart').getContext('2d');
                if(employeeDetailChart) employeeDetailChart.destroy();
                employeeDetailChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'],
                        datasets: [{
                            label: 'Citas',
                            data: [Math.floor(emp.citas_completadas*0.2), Math.floor(emp.citas_completadas*0.3), Math.floor(emp.citas_completadas*0.25), Math.floor(emp.citas_completadas*0.25)],
                            backgroundColor: 'rgba(250, 211, 112, 0.6)',
                            borderRadius: 4
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.1)' } }, x: { grid: { display: false } } } }
                });
            }, 100);
        }

        function closeEmployeeModal() { document.getElementById('employeeDetailModal').classList.remove('active'); }
        function getInitials(nombre) { return nombre.split(' ').slice(0,2).map(w => w[0]).join('').toUpperCase(); }
        function getProductivityBadge(prod) { if(prod >= 4) return {class: 'badge-high'}; if(prod >= 2) return {class: 'badge-medium'}; return {class: 'badge-low'}; }
        function generateStars(rating) {
            let stars = ''; const full = Math.floor(rating); const half = rating % 1 >= 0.5;
            for(let i=0; i<full; i++) stars += '<span class="star">★</span>';
            if(half) stars += '<span class="star">★</span>';
            return stars;
        }
    </script>
@endsection