@extends('layouts.app')

@section('title', 'Gestión de Promociones - StyleNow')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    /* VARIABLES (Mismo tema) */
    :root {
        --bg-primary: #0a0a0a; --bg-secondary: #1a1a1a; --bg-card: #2a2a2a;
        --text-primary: #ffffff; --text-secondary: #cccccc; --text-muted: #aeaeae;
        --accent-primary: #fad370; --accent-secondary: #eec95c;
        --border-color: rgba(0, 188, 212, 0.3); --hover-bg: rgba(0, 188, 212, 0.1);
        --badge-active-bg: rgba(76, 175, 80, 0.2); --badge-active-color: #4caf50;
        --badge-expired-bg: rgba(244, 67, 54, 0.2); --badge-expired-color: #f44336;
        --badge-inactive-bg: rgba(96, 125, 139, 0.2); --badge-inactive-color: #607d8b;
    }

    /* ESTADISTICAS */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; position: relative; overflow: hidden; }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary)); }
    .stat-icon { width: 50px; height: 50px; background: var(--hover-bg); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 15px; }
    .stat-label { font-size: 13px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; }
    .stat-value { font-size: 28px; font-weight: bold; color: var(--text-primary); }

    /* TOOLBAR */
    .toolbar { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 20px; margin-bottom: 30px; }
    .toolbar-row { display: flex; gap: 15px; flex-wrap: wrap; align-items: center; margin-bottom: 15px; }
    
    .search-box { flex: 1; min-width: 280px; position: relative; }
    .search-box input { width: 100%; padding: 12px 15px 12px 40px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 15px; }
    .search-box::before { content: '🔍'; position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; color: var(--text-muted); }

    .filter-group { display: flex; gap: 10px; flex-wrap: wrap; }
    .filter-select { padding: 12px 15px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); min-width: 160px; cursor: pointer; font-size: 14px; }
    
    .view-all-btn, .clear-filters { padding: 12px 20px; background: var(--hover-bg); border: 2px solid var(--border-color); border-radius: 10px; color: var(--accent-primary); cursor: pointer; font-weight: 600; transition: 0.3s; }
    .view-all-btn:hover, .clear-filters:hover { background: var(--accent-primary); color: #000; }

    /* TABLA */
    .table-section { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 15px; text-align: left; font-size: 13px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; border-bottom: 2px solid var(--border-color); }
    td { padding: 18px 15px; border-bottom: 1px solid var(--border-color); font-size: 14px; color: var(--text-secondary); }
    tbody tr:hover { background: rgba(250, 211, 112, 0.1); }

    .promo-name { font-weight: 600; color: var(--text-primary); display: block; }
    .promo-desc { font-size: 12px; color: var(--text-muted); display: block; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .discount-tag { background: var(--hover-bg); color: var(--accent-primary); padding: 4px 8px; border-radius: 8px; font-weight: 600; font-size: 12px; }
    
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; border: 1px solid; }
    .badge-active { background: var(--badge-active-bg); color: var(--badge-active-color); border-color: var(--badge-active-color); }
    .badge-expired { background: var(--badge-expired-bg); color: var(--badge-expired-color); border-color: var(--badge-expired-color); }
    .badge-inactive { background: var(--badge-inactive-bg); color: var(--badge-inactive-color); border-color: var(--badge-inactive-color); }

    .action-buttons { display: flex; gap: 8px; }
    .action-btn { width: 35px; height: 35px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; transition: 0.3s; }
    
    .btn-view { background: var(--hover-bg); color: var(--accent-primary); } /* Ver */
    .btn-edit { background: rgba(33, 150, 243, 0.2); color: #2196f3; } /* Editar */
    .btn-toggle { background: rgba(156, 39, 176, 0.2); color: #ab47bc; } /* Switch Status */
    .btn-delete { background: rgba(244, 67, 54, 0.2); color: #f44336; } /* Eliminar */
    
    .action-btn:hover { transform: scale(1.1); }

    /* MODAL */
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; }
    .modal.active { display: flex; }
    .modal-content { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 20px; padding: 30px; width: 90%; max-width: 800px; max-height: 90vh; overflow-y: auto; }
    .modal-header { display: flex; justify-content: space-between; margin-bottom: 25px; }
    .modal-title { font-size: 24px; font-weight: bold; color: var(--text-primary); }
    .close-btn { font-size: 28px; cursor: pointer; color: var(--text-muted); }
    
    .form-group { margin-bottom: 15px; }
    .form-label { display: block; margin-bottom: 5px; color: var(--text-secondary); font-size: 14px; }
    .form-input, .form-select, .form-textarea { width: 100%; padding: 10px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 8px; color: white; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .form-actions { display: flex; gap: 12px; margin-top: 25px; }
    .btn-save { flex: 1; padding: 12px; background: var(--accent-primary); border: none; border-radius: 10px; color: white; font-weight: bold; cursor: pointer; }
    .btn-cancel { flex: 1; padding: 12px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); cursor: pointer; }
    
    /* DETALLES EN MODAL DE VISTA */
    .detail-row { display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding: 10px 0; }
    .detail-label { color: var(--text-muted); font-weight: 600; }
    .detail-value { color: var(--text-primary); }
    .btn-status-toggle { width: 100%; padding: 12px; margin-top: 15px; border-radius: 10px; border:none; cursor: pointer; font-weight: bold; transition: 0.3s; }
    .btn-status-toggle.is-active { background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; } /* Para desactivar */
    .btn-status-toggle.is-inactive { background: rgba(76, 175, 80, 0.2); color: #4caf50; border: 1px solid #4caf50; } /* Para activar */

    .pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 20px; border-top: 2px solid var(--border-color); }
    .pagination-btn { padding: 8px 16px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 8px; color: var(--text-primary); cursor: pointer; }
</style>
@endpush

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">🎯</div>
            <div class="stat-label">Promociones Activas</div>
            <div class="stat-value" id="statActivas">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-label">Promedio Descuento</div>
            <div class="stat-value" id="statPromedio">0%</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📅</div>
            <div class="stat-label">Próximas a Vencer</div>
            <div class="stat-value" id="statVencer">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-label">Total Histórico</div>
            <div class="stat-value" id="statTotal">0</div>
        </div>
    </div>

    <div class="toolbar">
        <div class="toolbar-row" style="justify-content: space-between;">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Buscar promociones...">
            </div>
            <button class="view-all-btn" onclick="openModal()">+ Nueva Promoción</button>
        </div>
        <div class="toolbar-row">
            <div class="filter-group">
                <select class="filter-select" id="filterTipo">
                    <option value="">🏷️ Todos los Tipos</option>
                    <option value="Porcentaje">Porcentaje</option>
                    <option value="Fijo">Fijo</option>
                    <option value="2x1">2x1</option>
                    <option value="Pack">Pack</option>
                    <option value="Temporada">Temporada</option>
                </select>
                <select class="filter-select" id="filterEstado">
                    <option value="">📊 Estado</option>
                    <option value="Activa">✅ Activas</option>
                    <option value="Vencida">⚠️ Vencidas</option>
                    <option value="Inactiva">❌ Inactivas</option>
                </select>
                <select class="filter-select" id="filterFecha">
                    <option value="">📅 Fechas</option>
                    <option value="vigentes">Vigentes</option>
                    <option value="mes">Este Mes</option>
                </select>
                <button class="clear-filters" onclick="clearFilters()">🔄 Limpiar</button>
            </div>
        </div>
    </div>

    <div class="table-section">
        <div class="section-header">
            <div class="section-title">📋 Lista de Promociones</div>
            <div class="results-info" id="resultsInfo">Mostrando 0 promociones</div>
        </div>

        <table id="promoTableContainer">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="promoTable"></tbody>
        </table>

        <div class="pagination">
            <div class="pagination-info" id="paginationInfo">Mostrando 0 de 0</div>
            <div class="pagination-controls">
                <button class="pagination-btn" onclick="previousPage()" id="prevBtn">←</button>
                <span id="pageNumbers"></span>
                <button class="pagination-btn" onclick="nextPage()" id="nextBtn">→</button>
            </div>
        </div>
    </div>

    <div class="modal" id="promoModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Nueva Promoción</h2>
                <span class="close-btn" onclick="closeModal()">×</span>
            </div>
            <form id="promoForm">
                <input type="hidden" id="prmId" name="prm_id">
                <div class="form-group">
                    <label class="form-label">Nombre <span>*</span></label>
                    <input type="text" id="nombre" name="prm_nombre" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea id="descripcion" name="prm_descripcion" class="form-textarea"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tipo <span>*</span></label>
                        <select class="form-select" id="tipo" name="prm_tipoDescuento" required>
                            <option value="Porcentaje">Porcentaje</option><option value="Fijo">Fijo ($)</option><option value="2x1">2x1</option><option value="Pack">Pack</option><option value="Temporada">Temporada</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Valor <span>*</span></label>
                        <input type="number" id="valor" name="prm_valorDescuento" class="form-input" required step="0.01">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Inicio <span>*</span></label>
                        <input type="date" id="fechaInicio" name="prm_fechaInicio" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fin <span>*</span></label>
                        <input type="date" id="fechaFin" name="prm_fechaFin" class="form-input" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Estado</label>
                    <select class="form-select" id="estado" name="prm_estado">
                        <option value="1">Activo</option><option value="0">Inactivo</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn-save">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="viewModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Detalle de Promoción</h2>
                <span class="close-btn" onclick="closeViewModal()">×</span>
            </div>
            <div id="viewContent">
                </div>
            <div id="viewActions">
                </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let allPromotions = <?php echo json_encode($promociones); ?>;
    let currentPage = 1, pageSize = 8, totalPages = 1;
    let filteredPromotions = [...allPromotions];
    let editMode = false, currentEditId = null;

    document.addEventListener('DOMContentLoaded', () => {
        updateStats(); renderTable(); updatePagination(); setupEventListeners();
    });

    function setupEventListeners() {
        document.getElementById('promoForm').addEventListener('submit', savePromotion);
        document.getElementById('searchInput').addEventListener('input', applyFilters);
        document.getElementById('filterTipo').addEventListener('change', applyFilters);
        document.getElementById('filterEstado').addEventListener('change', applyFilters);
        document.getElementById('filterFecha').addEventListener('change', applyFilters);
    }

    // --- TABLA ---
    function renderTable() {
        const tbody = document.getElementById('promoTable');
        const start = (currentPage - 1) * pageSize;
        const data = filteredPromotions.slice(start, start + pageSize);
        tbody.innerHTML = '';

        if(data.length === 0) { tbody.innerHTML = '<tr><td colspan="9" style="text-align:center; padding:30px; color:#888;">No hay datos.</td></tr>'; return; }

        data.forEach(p => {
            let displayVal = p.prm_tipoDescuento === 'Porcentaje' ? `${parseInt(p.prm_valorDescuento)}%` : `$${parseFloat(p.prm_valorDescuento).toFixed(2)}`;
            if(p.prm_tipoDescuento === '2x1') displayVal = '2x1';

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>#${p.prm_id}</td>
                <td><span class="promo-name">${p.prm_nombre}</span></td>
                <td><span class="promo-desc">${p.prm_descripcion || '-'}</span></td>
                <td>${p.prm_tipoDescuento}</td>
                <td><span class="discount-tag">${displayVal}</span></td>
                <td>${formatDate(p.prm_fechaInicio)}</td>
                <td>${formatDate(p.prm_fechaFin)}</td>
                <td><span class="badge ${p.clase_visual}">${p.estado_visual}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn btn-view" onclick='viewPromotion(${JSON.stringify(p)})' title="Ver Detalle">👁️</button>
                        <button class="action-btn btn-toggle" onclick="toggleStatus(${p.prm_id}, ${p.prm_estado})" title="${p.prm_estado == 1 ? 'Deshabilitar' : 'Habilitar'}">
                            ${p.prm_estado == 1 ? '🔒' : '🔓'}
                        </button>
                        <button class="action-btn btn-edit" onclick='editPromotion(${JSON.stringify(p)})' title="Editar">✏️</button>
                        <button class="action-btn btn-delete" onclick="deletePromotion(${p.prm_id})" title="Eliminar">🗑️</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
        updateResultsInfo();
    }

    // --- ACCIONES (Ver, Toggle, Edit, Save, Delete) ---

    // 1. Ver Detalles
    function viewPromotion(p) {
        let displayVal = p.prm_tipoDescuento === 'Porcentaje' ? `${parseInt(p.prm_valorDescuento)}%` : `$${parseFloat(p.prm_valorDescuento).toFixed(2)}`;
        
        // Contenido del Modal
        document.getElementById('viewContent').innerHTML = `
            <div class="detail-row"><span class="detail-label">Nombre:</span> <span class="detail-value">${p.prm_nombre}</span></div>
            <div class="detail-row"><span class="detail-label">Descripción:</span> <span class="detail-value">${p.prm_descripcion || 'Sin descripción'}</span></div>
            <div class="detail-row"><span class="detail-label">Tipo:</span> <span class="detail-value">${p.prm_tipoDescuento}</span></div>
            <div class="detail-row"><span class="detail-label">Valor:</span> <span class="detail-value">${displayVal}</span></div>
            <div class="detail-row"><span class="detail-label">Vigencia:</span> <span class="detail-value">${formatDate(p.prm_fechaInicio)} - ${formatDate(p.prm_fechaFin)}</span></div>
            <div class="detail-row"><span class="detail-label">Estado Actual:</span> <span class="badge ${p.clase_visual}">${p.estado_visual}</span></div>
        `;

        // Botón de Acción Dinámico (Habilitar/Deshabilitar desde el modal)
        const btnClass = p.prm_estado == 1 ? 'is-active' : 'is-inactive';
        const btnText = p.prm_estado == 1 ? '🔒 Deshabilitar Promoción' : '🔓 Habilitar Promoción';
        
        document.getElementById('viewActions').innerHTML = `
            <button class="btn-status-toggle ${btnClass}" onclick="toggleStatus(${p.prm_id}, ${p.prm_estado})">${btnText}</button>
        `;

        document.getElementById('viewModal').classList.add('active');
    }

    function closeViewModal() { document.getElementById('viewModal').classList.remove('active'); }

    // 2. Toggle Status (Habilitar/Deshabilitar)
    async function toggleStatus(id, currentStatus) {
        const action = currentStatus == 1 ? 'deshabilitar' : 'habilitar';
        if(!confirm(`¿Deseas ${action} esta promoción?`)) return;

        try {
            const res = await fetch(`/admin/promociones/${id}/toggle`, {
                method: 'PATCH',
                headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'}
            });
            const data = await res.json();
            
            if(data.success) {
                alert(data.message);
                location.reload(); 
            } else {
                alert('Error al cambiar estado');
            }
        } catch(e) { console.error(e); }
    }

    // 3. Editar
    function editPromotion(p) {
        editMode = true; currentEditId = p.prm_id;
        document.getElementById('modalTitle').innerText = 'Editar Promoción';
        document.getElementById('prmId').value = p.prm_id;
        document.getElementById('nombre').value = p.prm_nombre;
        document.getElementById('descripcion').value = p.prm_descripcion;
        document.getElementById('tipo').value = p.prm_tipoDescuento;
        document.getElementById('valor').value = p.prm_valorDescuento;
        document.getElementById('fechaInicio').value = p.prm_fechaInicio;
        document.getElementById('fechaFin').value = p.prm_fechaFin;
        document.getElementById('estado').value = p.prm_estado;
        document.getElementById('promoModal').classList.add('active');
    }

    // 4. Guardar (Create/Update)
    async function savePromotion(e) {
        e.preventDefault();
        const fd = new FormData(e.target);
        const data = Object.fromEntries(fd);
        const url = editMode ? `/admin/promociones/${currentEditId}` : '/admin/promociones';
        const method = editMode ? 'PUT' : 'POST';

        try {
            const res = await fetch(url, {
                method: method,
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
                body: JSON.stringify(data)
            });
            if(res.ok) { alert('Guardado correctamente.'); location.reload(); }
            else { const err = await res.json(); alert('Error: ' + JSON.stringify(err.errors || err.message)); }
        } catch(e) { console.error(e); }
    }

    // 5. Eliminar
    async function deletePromotion(id) {
        if(!confirm('¿Eliminar esta promoción?')) return;
        try {
            const res = await fetch(`/admin/promociones/${id}`, { method: 'DELETE', headers: {'X-CSRF-TOKEN': csrfToken} });
            if(res.ok) location.reload();
        } catch(e) { console.error(e); }
    }

    // --- UTILS ---
    function openModal() { document.getElementById('promoForm').reset(); editMode=false; document.getElementById('modalTitle').innerText='Nueva Promoción'; document.getElementById('fechaInicio').value=new Date().toISOString().split('T')[0]; document.getElementById('promoModal').classList.add('active'); }
    function closeModal() { document.getElementById('promoModal').classList.remove('active'); }
    function formatDate(d) { if(!d) return ''; const [y,m,dia] = d.split('-'); return `${dia}/${m}/${y}`; }
    function updateResultsInfo() { document.getElementById('resultsInfo').innerText = `Mostrando ${filteredPromotions.length} resultados`; }
    function updatePagination() { /* Simple Pagination Logic */ }
    function previousPage() { if(currentPage>1){currentPage--; renderTable();} }
    function nextPage() { if(currentPage<totalPages){currentPage++; renderTable();} }
    
    function applyFilters() {
        const text = document.getElementById('searchInput').value.toLowerCase();
        const tipo = document.getElementById('filterTipo').value;
        const estado = document.getElementById('filterEstado').value;
        const fecha = document.getElementById('filterFecha').value;

        filteredPromotions = allPromotions.filter(p => {
            let matchText = p.prm_nombre.toLowerCase().includes(text);
            let matchTipo = tipo === '' || p.prm_tipoDescuento === tipo;
            let matchEstado = estado === '' || p.estado_visual === estado;
            
            // Filtro de fecha simple
            let matchFecha = true;
            const hoy = new Date();
            const inicio = new Date(p.prm_fechaInicio);
            const fin = new Date(p.prm_fechaFin);
            
            if (fecha === 'vigentes') matchFecha = (inicio <= hoy && fin >= hoy);
            if (fecha === 'mes') matchFecha = (inicio.getMonth() === hoy.getMonth() && inicio.getFullYear() === hoy.getFullYear());

            return matchText && matchTipo && matchEstado && matchFecha;
        });
        currentPage = 1; renderTable(); updateStats();
    }

    function updateStats() {
        document.getElementById('statTotal').innerText = allPromotions.length;
        document.getElementById('statActivas').innerText = allPromotions.filter(p => p.estado_visual === 'Activa').length;
    }
    
    function clearFilters() { document.getElementById('searchInput').value=''; document.getElementById('filterTipo').value=''; document.getElementById('filterEstado').value=''; applyFilters(); }
</script>
@endsection