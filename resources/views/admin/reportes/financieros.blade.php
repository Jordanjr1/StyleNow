@extends('layouts.app')

@section('title', 'Reportes Financieros - StyleNow')

<?php
$tiposReporte = [
    'ventas_mensuales' => '📈 Ventas Mensuales',
    'metodos_pago' => '💳 Métodos de Pago',
    'empleados_productivos' => '👥 Empleados Más Productivos',
];

$rangosFecha = [
    'este_mes' => 'Este Mes',
    'mes_pasado' => 'Mes Pasado',
    'ultimos_6_meses' => 'Últimos 6 Meses',
    'este_anio' => 'Este Año',
    'personalizado' => 'Personalizado',
];
?>

@push('styles')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --bg-primary: #0a0a0a; --bg-secondary: #1a1a1a; --bg-card: #2a2a2a;
            --text-primary: #ffffff; --text-secondary: #cccccc; --text-muted: #888888;
            --accent-primary: #f4ba27; --accent-secondary: #fad370;
            --border-color: rgba(99, 102, 241, 0.3); --hover-bg: rgba(99, 102, 241, 0.1);
            --chart-color-1: #6366f1; --chart-color-2: #8b5cf6; --chart-color-3: #10b981;
            --chart-color-4: #f59e0b; --chart-color-5: #ef4444; --badge-success-color: #10b981;
        }

        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .kpi-card { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; position: relative; overflow: hidden; transition: all 0.3s; }
        .kpi-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary)); }
        .kpi-icon { width: 50px; height: 50px; background: var(--hover-bg); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 15px; }
        .kpi-label { font-size: 13px; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .kpi-value { font-size: 28px; font-weight: bold; color: var(--text-primary); }

        .filters-section { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; margin-bottom: 30px; }
        .filters-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .filter-group { display: flex; flex-direction: column; gap: 8px; }
        .filter-label { font-size: 14px; font-weight: 600; color: var(--text-secondary); }
        .filter-select, .filter-input { padding: 12px 15px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 14px; outline: none; }
        .filter-select:focus, .filter-input:focus { border-color: var(--accent-primary); }
        .date-range-inputs { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        
        .filter-actions { display: flex; gap: 15px; flex-wrap: wrap; }
        .btn-generate { padding: 12px 24px; background: var(--accent-primary); border: none; border-radius: 10px; color: #000; cursor: pointer; font-weight: bold; display: flex; align-items: center; gap: 8px; transition: 0.3s; }
        .btn-generate:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(244, 186, 39, 0.4); }
        .btn-generate:disabled { opacity: 0.6; cursor: not-allowed; }
        .btn-export { padding: 12px 24px; background: transparent; border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); cursor: pointer; font-weight: bold; display: flex; align-items: center; gap: 8px; }

        .report-section { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; margin-bottom: 30px; }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .section-title { font-size: 20px; font-weight: bold; color: var(--text-primary); }
        .report-info { color: var(--text-muted); font-size: 14px; }
        .chart-container { position: relative; height: 400px; margin-bottom: 30px; background: var(--bg-secondary); border-radius: 10px; padding: 20px; }
        
        .data-table-container { overflow-x: auto; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 15px; text-align: left; font-size: 13px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; border-bottom: 2px solid var(--border-color); background: var(--bg-secondary); }
        td { padding: 18px 15px; border-bottom: 1px solid var(--border-color); font-size: 14px; color: var(--text-secondary); }
        tbody tr:hover { background: var(--hover-bg); }

        .progress-bar { height: 8px; background: var(--bg-secondary); border-radius: 4px; overflow: hidden; margin-top: 5px; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, var(--chart-color-1), var(--chart-color-2)); border-radius: 4px; transition: width 1s ease-in-out; }
    </style>
@endpush

@section('topbar-left')
    <h1>📊 Reportes y Estadísticas Financieros</h1>
    <p>Analiza el rendimiento real de tu negocio</p>
@endsection

@section('content')
    <div class="dashboard-grid">
        <div class="kpi-card">
            <div class="kpi-icon">💰</div>
            <div class="kpi-label">Ingresos Totales (Mes)</div>
            <div class="kpi-value" id="totalRevenue">Calculando...</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon">📅</div>
            <div class="kpi-label">Citas Pagadas (Mes)</div>
            <div class="kpi-value" id="totalAppointments">Calculando...</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon">👥</div>
            <div class="kpi-label">Clientes Activos (Mes)</div>
            <div class="kpi-value" id="activeClients">Calculando...</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon">⭐</div>
            <div class="kpi-label">Satisfacción Promedio</div>
            <div class="kpi-value" id="satisfactionRate">Calculando...</div>
        </div>
    </div>

    <div class="filters-section">
        <h3 style="margin-bottom: 20px; color: var(--text-primary);">⚙️ Configurar Reporte</h3>
        
        <div class="filters-grid">
            <div class="filter-group">
                <label class="filter-label">Tipo de Reporte</label>
                <select class="filter-select" id="reportType">
                    <?php foreach ($tiposReporte as $key => $label): ?>
                        <option value="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Rango de Fecha</label>
                <select class="filter-select" id="dateRange" onchange="toggleCustomDate()">
                    <?php foreach ($rangosFecha as $key => $label): ?>
                        <option value="<?php echo htmlspecialchars($key); ?>" <?php echo $key === 'ultimos_6_meses' ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group" id="customDateGroup" style="display: none;">
                <label class="filter-label">Fechas Personalizadas</label>
                <div class="date-range-inputs">
                    <input type="date" class="filter-input" id="startDate">
                    <input type="date" class="filter-input" id="endDate">
                </div>
            </div>
        </div>

        <div class="filter-actions">
            <button class="btn-generate" id="btnGenerate" onclick="generateReport()">📈 Generar Reporte</button>
            <div style="flex: 1;"></div>
            <button class="btn-export" onclick="alert('Exportación PDF en construcción')">📄 Exportar PDF</button>
        </div>
    </div>

    <div class="report-section">
        <div class="section-header">
            <div class="section-title" id="reportTitle">📈 Ventas Mensuales</div>
            <div class="report-info" id="reportInfo">Esperando datos...</div>
        </div>

        <div id="loadingIndicator" style="display: none; text-align: center; padding: 40px;">
            <div style="font-size: 40px; margin-bottom: 10px;">⏳</div>
            <div style="color: var(--accent-primary); font-weight: bold;">Procesando datos desde la Base de Datos...</div>
        </div>

        <div id="reportContentArea">
            <div id="chartContainer" class="chart-container">
                <canvas id="reportChart"></canvas>
            </div>

            <div id="dataTableContainer" class="data-table-container">
                <table id="reportTable">
                    <thead>
                        <tr id="tableHeader">
                            </tr>
                    </thead>
                    <tbody id="reportTableBody">
                        </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let currentChart = null;

        document.addEventListener('DOMContentLoaded', function() {
            setupDates();
            generateReport();
        });

        function setupDates() {
            const today = new Date();
            const last6Months = new Date();
            last6Months.setMonth(today.getMonth() - 5);
            
            document.getElementById('startDate').value = last6Months.toISOString().split('T')[0];
            document.getElementById('endDate').value = today.toISOString().split('T')[0];
        }

        function toggleCustomDate() {
            const dateRange = document.getElementById('dateRange').value;
            const customDateGroup = document.getElementById('customDateGroup');
            const today = new Date();
            let start = new Date();

            if (dateRange === 'este_mes') {
                start = new Date(today.getFullYear(), today.getMonth(), 1);
            } else if (dateRange === 'mes_pasado') {
                start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                today.setDate(0); // Último día del mes pasado
            } else if (dateRange === 'ultimos_6_meses') {
                start.setMonth(today.getMonth() - 5);
                start.setDate(1);
            } else if (dateRange === 'este_anio') {
                start = new Date(today.getFullYear(), 0, 1);
            }

            if (dateRange !== 'personalizado') {
                document.getElementById('startDate').value = start.toISOString().split('T')[0];
                document.getElementById('endDate').value = today.toISOString().split('T')[0];
                customDateGroup.style.display = 'none';
            } else {
                customDateGroup.style.display = 'block';
            }
        }

        async function generateReport() {
            const btn = document.getElementById('btnGenerate');
            const reportType = document.getElementById('reportType').value;
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            // UI Loading State
            btn.disabled = true;
            btn.innerHTML = '⏳ Generando...';
            document.getElementById('reportContentArea').style.display = 'none';
            document.getElementById('loadingIndicator').style.display = 'block';
            
            const titles = {
                'ventas_mensuales': '📈 Ventas Mensuales',
                'metodos_pago': '💳 Métodos de Pago Usados',
                'empleados_productivos': '👥 Top Empleados (Ventas)',
            };
            
            document.getElementById('reportTitle').textContent = titles[reportType] || 'Reporte';
            document.getElementById('reportInfo').textContent = `Desde ${startDate} hasta ${endDate} | Actualizado ahora`;

            try {
                const response = await fetch('/admin/reportes/financieros/data', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': csrfToken 
                    },
                    body: JSON.stringify({ report_type: reportType, start_date: startDate, end_date: endDate })
                });

                if (!response.ok) {
                    throw new Error("Ruta no encontrada o error 500");
                }

                const json = await response.json();

                // Restaurar UI
                document.getElementById('loadingIndicator').style.display = 'none';
                document.getElementById('reportContentArea').style.display = 'block';
                btn.disabled = false;
                btn.innerHTML = '📈 Generar Reporte';

                // Llenar KPIs si existen
                if(json.kpis) {
                    document.getElementById('totalRevenue').textContent = '$' + json.kpis.totalRevenue;
                    document.getElementById('totalAppointments').textContent = json.kpis.totalAppointments;
                    document.getElementById('activeClients').textContent = json.kpis.activeClients;
                    document.getElementById('satisfactionRate').textContent = json.kpis.satisfactionRate;
                }

                // Renderizar gráficos y tablas
                renderChart(reportType, json.data);
                renderTable(reportType, json.data);

                // Notificación visual de éxito rápida
                const Toast = Swal.mixin({
                    toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, background: '#2a2a2a', color: '#fff'
                });
                Toast.fire({ icon: 'success', title: 'Reporte actualizado' });

            } catch (error) {
                console.error("Error cargando reporte:", error);
                document.getElementById('loadingIndicator').style.display = 'none';
                btn.disabled = false;
                btn.innerHTML = '📈 Generar Reporte';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'No se pudo generar el reporte. Verifica que la ruta /admin/reportes/financieros/data exista en tu archivo web.php',
                    background: '#2a2a2a',
                    color: '#fff'
                });
            }
        }

        function renderChart(reportType, data) {
            const ctx = document.getElementById('reportChart').getContext('2d');
            if (currentChart) currentChart.destroy();
            
            if (!data || data.length === 0) return; // No pintar si no hay data

            let config = {};

            if (reportType === 'ventas_mensuales') {
                config = {
                    type: 'line',
                    data: {
                        labels: data.map(i => i.mes),
                        datasets: [{
                            label: 'Ventas Reales ($)',
                            data: data.map(i => i.ventas),
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            borderWidth: 3, fill: true, tension: 0.3
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                };
            } 
            else if (reportType === 'metodos_pago') {
                config = {
                    type: 'doughnut',
                    data: {
                        labels: data.map(i => i.metodo),
                        datasets: [{
                            data: data.map(i => i.porcentaje),
                            backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444'],
                            borderWidth: 0
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                };
            }
            else if (reportType === 'empleados_productivos') {
                config = {
                    type: 'bar',
                    data: {
                        labels: data.map(i => i.empleado),
                        datasets: [{
                            label: 'Ingresos Aportados ($)',
                            data: data.map(i => i.ingresos),
                            backgroundColor: '#10b981',
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                };
            }

            currentChart = new Chart(ctx, config);
        }

        function renderTable(reportType, data) {
            const tbody = document.getElementById('reportTableBody');
            const thead = document.getElementById('tableHeader');
            tbody.innerHTML = ''; thead.innerHTML = '';

            if (!data || data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" style="text-align: center;">No hay datos reales para este rango de fechas.</td></tr>`;
                return;
            }

            if (reportType === 'ventas_mensuales') {
                thead.innerHTML = '<th>Mes</th><th>Total Vendido</th><th>Citas Pagadas</th><th>Ticket Promedio</th>';
                data.forEach(item => {
                    tbody.innerHTML += `<tr>
                        <td><strong>${item.mes}</strong></td>
                        <td style="color:var(--badge-success-color); font-weight:bold;">$${item.ventas.toLocaleString()}</td>
                        <td>${item.citas}</td>
                        <td>$${item.promedio.toFixed(2)}</td>
                    </tr>`;
                });
            } 
            else if (reportType === 'metodos_pago') {
                thead.innerHTML = '<th>Método</th><th>Monto Total Recibido</th><th>Porcentaje</th><th>Uso Relativo</th>';
                data.forEach(item => {
                    tbody.innerHTML += `<tr>
                        <td><strong>${item.metodo}</strong></td>
                        <td>$${item.ventas.toLocaleString()}</td>
                        <td>${item.porcentaje}%</td>
                        <td><div class="progress-bar"><div class="progress-fill" style="width:${item.porcentaje}%"></div></div></td>
                    </tr>`;
                });
            }
            else if (reportType === 'empleados_productivos') {
                thead.innerHTML = '<th>Empleado</th><th>Citas Realizadas</th><th>Dinero Ingresado al Salón</th><th>Comisiones Ganadas</th>';
                data.forEach(item => {
                    tbody.innerHTML += `<tr>
                        <td><strong>${item.empleado}</strong></td>
                        <td>${item.citas}</td>
                        <td style="color:var(--badge-success-color); font-weight:bold;">$${parseFloat(item.ingresos).toLocaleString()}</td>
                        <td style="color:#f4ba27;">$${parseFloat(item.comisiones).toLocaleString()}</td>
                    </tr>`;
                });
            }
        }
    </script>
@endsection