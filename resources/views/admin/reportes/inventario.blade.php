@extends('layouts.app')

@section('title', 'Reportes de Inventario - StyleNow')

<?php
$tiposReporte = [
    'analisis_stock' => '📊 Análisis de Stock',
    'valor_inventario' => '💰 Valor del Inventario',
    'rotacion_productos' => '🔄 Rotación de Productos',
    'sugerencias_compras' => '🛒 Sugerencias de Compra'
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
            --border-color: rgba(16, 185, 129, 0.3); --hover-bg: rgba(16, 185, 129, 0.1);
            --chart-color-1: #10b981; --chart-color-2: #3b82f6; --chart-color-3: #8b5cf6;
            --chart-color-4: #f59e0b; --chart-color-5: #ef4444;
            --badge-out-color: #ef4444; --badge-low-color: #f59e0b;
            --badge-optimal-color: #10b981; --badge-over-color: #3b82f6;
            --badge-urgent-color: #dc2626; --badge-high-color: #f59e0b;
        }

        .summary-section { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; margin-bottom: 30px; }
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .summary-card { background: var(--bg-secondary); border-radius: 12px; padding: 20px; text-align: center; position: relative; overflow: hidden; }
        .summary-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary)); }
        .summary-icon { width: 50px; height: 50px; background: var(--hover-bg); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 15px; }
        .summary-value { font-size: 28px; font-weight: bold; color: var(--text-primary); margin-bottom: 5px; }
        .summary-label { font-size: 13px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }

        .filters-section { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; margin-bottom: 30px; }
        .filters-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .filters-title { font-size: 18px; font-weight: bold; color: var(--text-primary); display: flex; align-items: center; gap: 10px; }
        .filters-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .filter-group { display: flex; flex-direction: column; gap: 8px; }
        .filter-label { font-size: 14px; font-weight: 600; color: var(--text-secondary); }
        .filter-select { padding: 12px 15px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); outline: none; }
        .filter-select:focus { border-color: var(--accent-primary); }
        
        .filter-actions { display: flex; gap: 15px; flex-wrap: wrap; align-items: center; }
        .btn-primary { padding: 12px 24px; background: var(--accent-primary); border: none; border-radius: 10px; color: #000; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3); }
        .btn-secondary { padding: 12px 24px; background: transparent; border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); cursor: pointer; font-weight: bold; transition: 0.3s; }
        .btn-secondary:hover { border-color: var(--accent-primary); color: var(--accent-primary); }

        .report-section { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; margin-bottom: 30px; }
        .report-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid var(--border-color); }
        .report-title { font-size: 20px; font-weight: bold; color: var(--text-primary); }
        .report-info { color: var(--text-muted); font-size: 14px; }
        
        .chart-container { height: 300px; margin-bottom: 30px; background: var(--bg-secondary); border-radius: 10px; padding: 20px; }
        .report-table { overflow-x: auto; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th { padding: 15px; text-align: left; font-size: 13px; color: var(--text-muted); font-weight: bold; text-transform: uppercase; background: var(--bg-secondary); }
        td { padding: 18px 15px; border-bottom: 1px solid var(--border-color); font-size: 14px; color: var(--text-secondary); }
        
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-out { background: rgba(239, 68, 68, 0.2); color: var(--badge-out-color); border: 1px solid var(--badge-out-color); }
        .badge-low { background: rgba(245, 158, 11, 0.2); color: var(--badge-low-color); border: 1px solid var(--badge-low-color); }
        .badge-optimal { background: rgba(16, 185, 129, 0.2); color: var(--badge-optimal-color); border: 1px solid var(--badge-optimal-color); }
        .badge-over { background: rgba(59, 130, 246, 0.2); color: var(--badge-over-color); border: 1px solid var(--badge-over-color); }

        .progress-bar { height: 8px; background: var(--bg-secondary); border-radius: 4px; overflow: hidden; margin-top: 5px; }
        .progress-fill { height: 100%; border-radius: 4px; transition: width 0.3s ease; }
    </style>
@endpush

@section('topbar-left')
    <h1>📊 Reportes de Inventario</h1>
    <p>Análisis avanzado y métricas de tu inventario (Datos Reales)</p>
@endsection

@section('content')
    <div class="summary-section">
        <div class="summary-grid" id="summaryGrid">
            </div>
    </div>

    <div class="filters-section">
        <div class="filters-header">
            <div class="filters-title"><span>⚙️</span> Configurar Reporte</div>
            <div style="display:flex; gap:10px;">
                <button class="btn-secondary" onclick="exportReport('pdf')" style="color: #ef4444; border-color: #ef4444;">📄 Exportar PDF</button>
                <button class="btn-secondary" onclick="exportReport('excel')" style="color: #10b981; border-color: #10b981;">📊 Exportar Excel</button>
            </div>
        </div>
        
        <div class="filters-grid">
            <div class="filter-group">
                <label class="filter-label"><span>📋</span> Tipo de Reporte</label>
                <select class="filter-select" id="reportType">
                    <?php foreach ($tiposReporte as $key => $label): ?>
                        <option value="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label"><span>📂</span> Categoría</label>
                <select class="filter-select" id="categoriaFilter">
                    <option value="all">Todas las Categorías</option>
                    @foreach($categorias as $c)
                        <option value="{{ $c->cats_id ?? $c->catp_id }}">{{ $c->cats_nombre ?? $c->catp_nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label"><span>🏬</span> Sucursal</label>
                <select class="filter-select" id="sucursalFilter">
                    <option value="all">Todas las Sucursales</option>
                    @foreach($sucursales as $s)
                        <option value="{{ $s->suc_id }}">{{ $s->suc_nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="filter-actions">
            <button class="btn-primary" id="btnGenerate" onclick="generateReport()">📈 Generar Reporte</button>
            <button class="btn-secondary" onclick="resetFilters()">🔄 Restablecer Filtros</button>
            <div style="flex: 1;"></div>
            <div style="font-size: 12px; color: var(--text-muted);">Conectado a Base de Datos ✅</div>
        </div>
    </div>

    <div class="report-section">
        <div class="report-header">
            <div>
                <div class="report-title" id="reportTitle">📊 Análisis de Stock</div>
                <div class="report-info" id="reportInfo">Esperando datos...</div>
            </div>
        </div>

        <div id="chartContainer" class="chart-container">
            <canvas id="reportChart"></canvas>
        </div>

        <div id="reportTableContainer" class="report-table">
            <table id="reportTable">
                <thead id="tableHeader"></thead>
                <tbody id="reportTableBody"></tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let currentChart = null;
        let globalReportData = []; // Variable global para guardar los datos y exportarlos

        document.addEventListener('DOMContentLoaded', function() {
            generateReport();
        });

        async function generateReport() {
            const btn = document.getElementById('btnGenerate');
            const reportType = document.getElementById('reportType').value;
            const categoriaId = document.getElementById('categoriaFilter').value;
            const sucursalId = document.getElementById('sucursalFilter').value;
            
            btn.disabled = true;
            btn.innerHTML = '⏳ Cargando...';
            
            const titles = {
                'analisis_stock': '📊 Análisis de Stock',
                'valor_inventario': '💰 Valor del Inventario',
                'rotacion_productos': '🔄 Rotación de Productos',
                'sugerencias_compras': '🛒 Sugerencias de Compra'
            };
            
            document.getElementById('reportTitle').textContent = titles[reportType];
            document.getElementById('reportInfo').textContent = `Actualizado: ${new Date().toLocaleString('es-ES')}`;

            try {
                const response = await fetch('/admin/reportes/inventario/data', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ report_type: reportType, categoria_id: categoriaId, sucursal_id: sucursalId })
                });

                if (!response.ok) throw new Error("Error en servidor");
                const json = await response.json();

                btn.disabled = false;
                btn.innerHTML = '📈 Generar Reporte';

                globalReportData = json.report_data; // Guardar para EXCEL/PDF

                updateSummary(json.summary);
                renderChart(reportType, json.report_data);
                renderTable(reportType, json.report_data);

            } catch (error) {
                console.error(error);
                btn.disabled = false;
                btn.innerHTML = '📈 Generar Reporte';
                Swal.fire({icon:'error', title:'Error de Conexión', text:'Revisa tu controlador.', background:'#2a2a2a', color:'#fff'});
            }
        }

        function resetFilters() {
            document.getElementById('reportType').value = 'analisis_stock';
            document.getElementById('categoriaFilter').value = 'all';
            document.getElementById('sucursalFilter').value = 'all';
            generateReport();
        }

        // ==========================================
        // FUNCIONES DE EXPORTACIÓN (EXCEL Y PDF)
        // ==========================================
        function getMappedDataForExport() {
            const reportType = document.getElementById('reportType').value;
            let mappedData = [];

            globalReportData.forEach(item => {
                let row = {};
                if (reportType === 'analisis_stock') {
                    row = {
                        'Producto': item.producto, 'Categoría': item.categoria, 'Sucursal': item.sucursal, 
                        'Stock Actual': item.stock_actual, 'Stock Mínimo': item.stock_minimo, 
                        'Estado': (item.estado === 'out_of_stock' ? 'Sin Stock' : item.estado === 'low_stock' ? 'Bajo Stock' : 'Óptimo'),
                        'Días de Cobertura': item.dias_stock
                    };
                } else if (reportType === 'valor_inventario') {
                    row = {
                        'Producto': item.producto, 'Sucursal': item.sucursal, 'Unidades': item.stock_actual, 
                        'Costo Unitario ($)': item.precio_compra.toFixed(2), 
                        'Capital Invertido ($)': item.valor_total.toFixed(2), 
                        '% del Total': item.porcentaje_total + '%'
                    };
                } else if (reportType === 'sugerencias_compras') {
                    row = {
                        'Producto': item.producto, 'Sucursal': item.sucursal, 'Stock Actual': item.stock_actual, 
                        'Faltante (Sugerido)': item.cantidad_sugerida, 
                        'Inversión Estimada ($)': item.costo_estimado.toFixed(2), 
                        'Urgencia': item.urgencia.toUpperCase()
                    };
                } else {
                    row = { 'Producto': item.producto, 'Stock': item.stock_actual, 'Sucursal': item.sucursal };
                }
                mappedData.push(row);
            });
            return mappedData;
        }

        function exportReport(format) {
            if (!globalReportData || globalReportData.length === 0) {
                Swal.fire({icon: 'warning', title: 'Sin Datos', text: 'No hay información en la tabla para exportar.', background:'#2a2a2a', color:'#fff'});
                return;
            }

            const dataToExport = getMappedDataForExport();
            const reportName = document.getElementById('reportTitle').innerText.replace(/[^a-zA-Z0-9 ]/g, "").trim();

            if (format === 'excel') {
                // Generar Excel con SheetJS
                const ws = XLSX.utils.json_to_sheet(dataToExport);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Reporte");
                XLSX.writeFile(wb, `${reportName}_${new Date().getTime()}.xlsx`);
                
                Swal.fire({icon: 'success', title: 'Excel Descargado', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background:'#2a2a2a', color:'#fff'});
            } 
            else if (format === 'pdf') {
                // Generar PDF con jsPDF
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                
                // Configurar Título del PDF
                doc.setFontSize(18);
                doc.text(reportName, 14, 20);
                
                doc.setFontSize(10);
                doc.text(`Generado el: ${new Date().toLocaleString('es-ES')}`, 14, 28);

                // Configurar Tabla
                const headers = [Object.keys(dataToExport[0])];
                const rows = dataToExport.map(obj => Object.values(obj));

                doc.autoTable({
                    startY: 35,
                    head: headers,
                    body: rows,
                    theme: 'grid',
                    styles: { fontSize: 8, cellPadding: 3 },
                    headStyles: { fillColor: [250, 211, 112], textColor: [0, 0, 0], fontStyle: 'bold' }, // Color dorado del sistema
                    alternateRowStyles: { fillColor: [245, 245, 245] }
                });

                doc.save(`${reportName}_${new Date().getTime()}.pdf`);
                
                Swal.fire({icon: 'success', title: 'PDF Descargado', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, background:'#2a2a2a', color:'#fff'});
            }
        }
        // ==========================================

        function updateSummary(summary) {
            const container = document.getElementById('summaryGrid');
            if(!summary) return;

            container.innerHTML = `
                <div class="summary-card">
                    <div class="summary-icon">📦</div>
                    <div class="summary-value">${summary.total_productos}</div>
                    <div class="summary-label">Productos Activos</div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon">📊</div>
                    <div class="summary-value">${summary.total_stock}</div>
                    <div class="summary-label">Unidades Físicas</div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon">💰</div>
                    <div class="summary-value">$${summary.valor_total.toLocaleString('es-ES', {minimumFractionDigits: 2})}</div>
                    <div class="summary-label">Capital Invertido</div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon">⚠️</div>
                    <div class="summary-value" style="color:var(--badge-low-color);">${summary.productos_bajo_stock}</div>
                    <div class="summary-label">Alerta de Stock</div>
                </div>
            `;
        }

        function renderChart(reportType, data) {
            const ctx = document.getElementById('reportChart').getContext('2d');
            if (currentChart) currentChart.destroy();
            if (!data || data.length === 0) return;

            let config = {};

            if (reportType === 'analisis_stock') {
                const statusCounts = { 'out_of_stock': 0, 'low_stock': 0, 'optimal_stock': 0, 'over_stock': 0 };
                data.forEach(item => statusCounts[item.estado]++);
                config = {
                    type: 'doughnut',
                    data: {
                        labels: ['Sin Stock', 'Bajo Stock', 'Óptimo', 'Sobre Stock'],
                        datasets: [{
                            data: [statusCounts.out_of_stock, statusCounts.low_stock, statusCounts.optimal_stock, statusCounts.over_stock],
                            backgroundColor: ['#ef4444', '#f59e0b', '#10b981', '#3b82f6'],
                            borderWidth: 0
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                };
            } 
            else if (reportType === 'valor_inventario') {
                const top = data.slice(0, 10); // Top 10 más caros
                config = {
                    type: 'bar',
                    data: {
                        labels: top.map(i => i.producto.substring(0, 15)),
                        datasets: [{
                            label: 'Capital Invertido ($)',
                            data: top.map(i => i.valor_total),
                            backgroundColor: '#fad370',
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                };
            }
            else {
                config = { type: 'bar', data: { labels: ['No chart data'], datasets: [{ data: [1], backgroundColor: '#333' }] } };
            }

            currentChart = new Chart(ctx, config);
        }

        function renderTable(reportType, data) {
            const tbody = document.getElementById('reportTableBody');
            const thead = document.getElementById('tableHeader');
            tbody.innerHTML = ''; thead.innerHTML = '';

            if (!data || data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;">No hay productos con este filtro.</td></tr>`;
                return;
            }

            if (reportType === 'analisis_stock') {
                thead.innerHTML = '<th>Producto</th><th>Categoría</th><th>Sucursal</th><th>Stock Físico</th><th>Alerta Mínima</th><th>Estado</th>';
                data.slice(0,25).forEach(i => {
                    const perc = Math.min((i.stock_actual / i.stock_maximo) * 100, 100);
                    const color = i.estado === 'out_of_stock' ? '#ef4444' : i.estado === 'low_stock' ? '#f59e0b' : '#10b981';
                    const badge = i.estado === 'out_of_stock' ? '<span class="badge badge-out">Sin Stock</span>' : i.estado === 'low_stock' ? '<span class="badge badge-low">Bajo Stock</span>' : '<span class="badge badge-optimal">Óptimo</span>';
                    
                    tbody.innerHTML += `<tr>
                        <td><strong>${i.producto}</strong></td>
                        <td>${i.categoria}</td>
                        <td>${i.sucursal}</td>
                        <td>
                            <div style="font-weight:bold;">${i.stock_actual} unid.</div>
                            <div class="progress-bar"><div class="progress-fill" style="width:${perc}%; background:${color};"></div></div>
                        </td>
                        <td>${i.stock_minimo}</td>
                        <td>${badge}</td>
                    </tr>`;
                });
            } 
            else if (reportType === 'valor_inventario') {
                thead.innerHTML = '<th>Producto</th><th>Sucursal</th><th>Unidades</th><th>Costo Unit.</th><th>Capital Invertido</th><th>% del Total</th>';
                data.slice(0,25).forEach(i => {
                    tbody.innerHTML += `<tr>
                        <td><strong>${i.producto}</strong></td>
                        <td>${i.sucursal}</td>
                        <td>${i.stock_actual}</td>
                        <td>$${i.precio_compra.toFixed(2)}</td>
                        <td style="color:var(--accent-primary); font-weight:bold;">$${i.valor_total.toFixed(2)}</td>
                        <td>${i.porcentaje_total}%</td>
                    </tr>`;
                });
            }
            else if (reportType === 'sugerencias_compras') {
                thead.innerHTML = '<th>Producto</th><th>Sucursal</th><th>Stock Actual</th><th>Faltante (Sugerido)</th><th>Inversión Estimada</th><th>Urgencia</th>';
                data.forEach(i => {
                    const urgBadge = i.urgencia === 'urgente' ? '<span class="badge badge-out">Urgente</span>' : '<span class="badge badge-low">Comprar Pronto</span>';
                    tbody.innerHTML += `<tr>
                        <td><strong>${i.producto}</strong></td>
                        <td>${i.sucursal}</td>
                        <td style="color:#ef4444; font-weight:bold;">${i.stock_actual}</td>
                        <td style="color:#10b981; font-weight:bold;">+ ${i.cantidad_sugerida} unid.</td>
                        <td>$${i.costo_estimado.toFixed(2)}</td>
                        <td>${urgBadge}</td>
                    </tr>`;
                });
            }
            else {
                thead.innerHTML = '<th>Producto</th><th>Sucursal</th><th>Stock</th><th>Precio</th>';
                data.slice(0,10).forEach(i => {
                    tbody.innerHTML += `<tr><td>${i.producto}</td><td>${i.sucursal}</td><td>${i.stock_actual}</td><td>$${i.precio_venta}</td></tr>`;
                });
            }
        }
    </script>
@endsection