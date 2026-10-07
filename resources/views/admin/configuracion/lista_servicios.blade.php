@extends('layouts.app')

@section('title', 'Gestión de Servicios - StyleNow')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<style>
    /* VARIABLES (Tu diseño original oscuro/dorado) */
    :root {
        --bg-primary: #0a0a0a; --bg-secondary: #1a1a1a; --bg-card: #2a2a2a;
        --text-primary: #ffffff; --text-secondary: #cccccc; --text-muted: #aeaeae;
        --accent-primary: #fad370; --accent-secondary: #eec95c;
        --border-color: rgba(0, 188, 212, 0.3); --hover-bg: rgba(0, 188, 212, 0.1);
        --badge-active-bg: rgba(76, 175, 80, 0.2); --badge-active-color: #4caf50;
        --badge-inactive-bg: rgba(244, 67, 54, 0.2); --badge-inactive-color: #f44336;
        --chart-primary: #ffc107; --chart-secondary: #80DEEA;
        --badge-popular-bg: rgba(255, 193, 7, 0.2); --badge-popular-color: #ffc107;
    }

    /* ESTILOS INTACTOS */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; position: relative; overflow: hidden; }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary)); }
    .stat-icon { width: 50px; height: 50px; background: var(--hover-bg); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 15px; }
    .stat-label { font-size: 13px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; }
    .stat-value { font-size: 28px; font-weight: bold; color: var(--text-primary); }
    .stat-subvalue { font-size: 12px; color: var(--text-muted); margin-top: 5px; }

    .toolbar { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 20px; margin-bottom: 30px; }
    .toolbar-row { display: flex; gap: 15px; flex-wrap: wrap; align-items: center; margin-bottom: 15px; }
    .search-box { flex: 1; min-width: 280px; }
    .search-box input { width: 100%; padding: 12px 15px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 15px; }
    
    .filter-select { padding: 12px 15px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); min-width: 180px; cursor: pointer; }
    .view-all-btn, .clear-filters { padding: 12px 20px; background: var(--hover-bg); border: 2px solid var(--border-color); border-radius: 10px; color: var(--accent-primary); cursor: pointer; font-weight: 600; transition: 0.3s; }
    .view-all-btn:hover, .clear-filters:hover { background: var(--accent-primary); color: #fff; }

    .table-section { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 15px; text-align: left; font-size: 13px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; border-bottom: 2px solid var(--border-color); }
    td { padding: 18px 15px; border-bottom: 1px solid var(--border-color); font-size: 14px; color: var(--text-secondary); }
    tbody tr:hover { background: rgba(250, 211, 112, 0.1); }

    .service-name { font-weight: 600; color: var(--text-primary); display:block; }
    .service-category { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
    .services-count { background: var(--hover-bg); color: var(--accent-primary); padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 600; margin-top: 4px; display: inline-block; }
    .price-tag { font-weight: 600; color: var(--accent-primary); }
    .points { background: var(--hover-bg); color: var(--accent-primary); padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
    .badge-active { background: var(--badge-active-bg); color: var(--badge-active-color); border: 1px solid var(--badge-active-color); }
    .badge-inactive { background: var(--badge-inactive-bg); color: var(--badge-inactive-color); border: 1px solid var(--badge-inactive-color); }
    .badge-popular { background: var(--badge-popular-bg); color: var(--badge-popular-color); border: 1px solid var(--badge-popular-color); }

    .popularity-indicator { display: flex; align-items: center; gap: 8px; }
    .popularity-value { font-size: 12px; color: var(--text-muted); min-width: 30px; }
    .popularity-bar { width: 60px; height: 6px; background: var(--bg-secondary); border-radius: 3px; overflow: hidden; margin-top: 4px; }
    .popularity-fill { height: 100%; background: linear-gradient(90deg, var(--chart-primary), var(--accent-secondary)); }

    .action-buttons { display: flex; gap: 8px; }
    .action-btn { width: 35px; height: 35px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; transition: 0.3s; }
    .btn-edit { background: rgba(33, 150, 243, 0.2); color: #2196f3; }
    .btn-delete { background: rgba(244, 67, 54, 0.2); color: #f44336; }
    .btn-view { background: var(--hover-bg); color: var(--accent-primary); }
    .action-btn:hover { transform: scale(1.1); }

    /* MODAL */
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; }
    .modal.active { display: flex; }
    .modal-content { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 20px; padding: 30px; width: 90%; max-width: 800px; max-height: 90vh; overflow-y: auto; }
    .modal-header { display: flex; justify-content: space-between; margin-bottom: 25px; }
    .modal-title { font-size: 24px; font-weight: bold; color: var(--text-primary); }
    .close-btn { font-size: 28px; cursor: pointer; color: var(--text-muted); }
    
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
    .form-group { margin-bottom: 15px; }
    .form-label { display: block; margin-bottom: 5px; color: var(--text-secondary); font-size: 14px; }
    .form-input, .form-select, .form-textarea { width: 100%; padding: 10px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 8px; color: white; }
    .form-actions { display: flex; gap: 12px; margin-top: 25px; }
    .btn-save { flex: 1; padding: 12px; background: var(--accent-primary); border: none; border-radius: 10px; color: white; font-weight: bold; cursor: pointer; }
    .btn-cancel { flex: 1; padding: 12px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); cursor: pointer; }
    
    .pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 20px; border-top: 2px solid var(--border-color); }
    .pagination-btn { padding: 8px 16px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 8px; color: var(--text-primary); cursor: pointer; }
    .pagination-btn.active { background: var(--accent-primary); color: #fff; border-color: var(--accent-primary); }
</style>
@endpush

@section('topbar-left')
    <h1>✂️ Gestión de Servicios</h1>
    <p>Administra los servicios de NewStyle</p>
@endsection

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">✂️</div>
            <div class="stat-label">Total Servicios</div>
            <div class="stat-value" id="totalServices">0</div>
            <div class="stat-subvalue" id="activeServices">0 activos</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-label">Precio Promedio</div>
            <div class="stat-value" id="avgPrice">$0</div>
            <div class="stat-subvalue" id="priceRange">$0 - $0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⏱️</div>
            <div class="stat-label">Duración Promedio</div>
            <div class="stat-value" id="avgDuration">0 min</div>
            <div class="stat-subvalue">Estimada</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-label">Más Popular</div>
            <div class="stat-value" id="topServiceValue" style="font-size: 18px;">-</div>
            <div class="stat-subvalue" id="topService">Sin datos</div>
        </div>
    </div>

    <div class="toolbar">
        <div class="toolbar-row">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Buscar servicios...">
            </div>
            <button class="view-all-btn" onclick="openModal()">➕ Nuevo Servicio</button>
        </div>
        <div class="toolbar-row">
            <div class="filter-group">
                <select class="filter-select" id="filterCategoria">
                    <option value="">📁 Todas las Categorías</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->cats_nombre }}">{{ $categoria->cats_nombre }}</option>
                    @endforeach
                </select>
                <select class="filter-select" id="filterEstado">
                    <option value="">📈 Estado</option>
                    <option value="1">✅ Activos</option>
                    <option value="0">❌ Inactivos</option>
                </select>
                <button class="clear-filters" onclick="clearFilters()">🔄 Limpiar</button>
            </div>
        </div>
    </div>

    <div class="table-section">
        <div class="section-header">
            <div class="section-title">📋 Lista de Servicios</div>
            <div class="results-info" id="resultsInfo">Mostrando 0 servicios</div>
        </div>

        <div id="loadingIndicator" class="loading" style="display:none;">Cargando servicios...</div>

        <table id="servicesTableContainer">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Servicio</th>
                    <th>Precio</th>
                    <th>Duración</th>
                    <th>Puntos</th>
                    <th>Popularidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="servicesTable"></tbody>
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

    <div class="modal" id="serviceModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Nuevo Servicio</h2>
                <span class="close-btn" onclick="closeModal()">×</span>
            </div>
            
            <form id="serviceForm">
                <input type="hidden" id="serviceId">
                
                <div class="form-group">
                    <label class="form-label">Nombre del Servicio <span>*</span></label>
                    <input type="text" id="nombre" name="srv_nombre" class="form-input" placeholder="Ej: Corte de Cabello Clásico" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Categoría <span>*</span></label>
                        <select class="form-select" id="categoria" name="srv_categoriaId" required>
                            <option value="">Seleccionar...</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->cats_id }}">{{ $categoria->cats_nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Precio ($) <span>*</span></label>
                        <input type="number" id="precio" name="srv_precio" class="form-input" placeholder="0.00" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Duración (min) <span>*</span></label>
                        <input type="number" id="duracion" name="srv_duracionMinutos" class="form-input" placeholder="30" min="5" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Puntos Fidelización</label>
                        <input type="number" id="puntos" name="srv_puntosFidelizacion" class="form-input" placeholder="10" min="0" value="10">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Estado</label>
                    <select class="form-select" id="estado" name="srv_estado">
                        <option value="1">✅ Activo</option>
                        <option value="0">❌ Inactivo</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn-save">Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Inyectamos los datos desde el controlador usando sintaxis segura
    let allServices = <?php echo json_encode($servicios); ?>;
    
    let currentPage = 1, pageSize = 10, totalPages = 1;
    let filteredServices = [...allServices];
    let editMode = false, currentEditId = null;

    document.addEventListener('DOMContentLoaded', () => {
        updateStats();
        renderTable();
        updatePagination();
        setupEventListeners();
    });

    function setupEventListeners() {
        document.getElementById('serviceForm').addEventListener('submit', saveService);
        document.getElementById('searchInput').addEventListener('input', applyFilters);
        document.getElementById('filterCategoria').addEventListener('change', applyFilters);
        document.getElementById('filterEstado').addEventListener('change', applyFilters);
        document.getElementById('pageSize').addEventListener('change', changePageSize);
    }

    // --- ESTADISTICAS (Corregido para comparar con 1) ---
    function updateStats() {
        const total = allServices.length;
        // Comparamos con 1 en lugar de 'A'
        const active = allServices.filter(s => s.srv_estado == 1).length;
        
        const precios = allServices.map(s => parseFloat(s.srv_precio));
        const avgPrice = precios.length ? (precios.reduce((a, b) => a + b, 0) / precios.length) : 0;
        const minPrice = precios.length ? Math.min(...precios) : 0;
        const maxPrice = precios.length ? Math.max(...precios) : 0;
        
        const duraciones = allServices.map(s => parseInt(s.srv_duracionMinutos));
        const avgDuration = duraciones.length ? (duraciones.reduce((a, b) => a + b, 0) / duraciones.length) : 0;

        let topService = { srv_nombre: 'Sin datos', popularidad: 0 };
        if (allServices.length > 0) {
            topService = allServices.reduce((prev, curr) => (prev.popularidad > curr.popularidad) ? prev : curr);
        }

        document.getElementById('totalServices').textContent = total;
        document.getElementById('activeServices').textContent = `${active} activos`;
        document.getElementById('avgPrice').textContent = `$${avgPrice.toFixed(2)}`;
        document.getElementById('priceRange').textContent = `$${minPrice} - $${maxPrice}`;
        document.getElementById('avgDuration').textContent = `${Math.round(avgDuration)} min`;
        document.getElementById('topServiceValue').textContent = topService.srv_nombre;
        document.getElementById('topService').textContent = `${topService.popularidad}% pop.`;
    }

    // --- TABLA Y FILTROS ---
    function renderTable() {
        const tbody = document.getElementById('servicesTable');
        const start = (currentPage - 1) * pageSize;
        const data = filteredServices.slice(start, start + pageSize);
        tbody.innerHTML = '';

        if(data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="no-results">No se encontraron servicios.</td></tr>';
            updateResultsInfo();
            return;
        }

        data.forEach(s => {
            let popColor = s.popularidad >= 80 ? 'badge-popular' : '';
            let popText = s.popularidad >= 80 ? '🔥 Alta' : (s.popularidad >= 50 ? '📈 Media' : '📉 Baja');
            
            // Lógica corregida: 1 es Activo, 0 es Inactivo
            let isActive = (s.srv_estado == 1);
            let stateClass = isActive ? 'badge-active' : 'badge-inactive';
            let stateText = isActive ? '✅ Activo' : '❌ Inactivo';

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>#${s.srv_id}</td>
                <td>
                    <span class="service-name">${s.srv_nombre}</span>
                    <span class="service-category">${s.cats_nombre}</span>
                    <span class="services-count">${s.servicios_realizados} citas</span>
                </td>
                <td><span class="price-tag">$${parseFloat(s.srv_precio).toFixed(2)}</span></td>
                <td><div style="display:flex;align-items:center;gap:5px;">⏱️ ${s.srv_duracionMinutos}m</div></td>
                <td><span class="points">⭐ ${s.srv_puntosFidelizacion}</span></td>
                <td>
                    <div class="popularity-indicator">
                        <span class="popularity-value">${s.popularidad}%</span>
                        <div class="popularity-bar"><div class="popularity-fill" style="width: ${s.popularidad}%"></div></div>
                    </div>
                    <span class="badge ${popColor}" style="margin-top:4px;">${popText}</span>
                </td>
                <td><span class="badge ${stateClass}">${stateText}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn btn-edit" onclick='editService(${JSON.stringify(s)})'>✏️</button>
                        <button class="action-btn btn-delete" onclick="deleteService(${s.srv_id})">🗑️</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
        updateResultsInfo();
    }

    function applyFilters() {
        const text = document.getElementById('searchInput').value.toLowerCase();
        const cat = document.getElementById('filterCategoria').value;
        const est = document.getElementById('filterEstado').value;

        filteredServices = allServices.filter(s => {
            return (s.srv_nombre.toLowerCase().includes(text) || s.cats_nombre.toLowerCase().includes(text)) &&
                   (cat === '' || s.cats_nombre === cat) &&
                   // Filtro Estado corregido: compara con 1 o 0
                   (est === '' || s.srv_estado == est);
        });
        currentPage = 1; renderTable(); updatePagination();
    }

    // --- MODALES Y CRUD ---
    function openModal() {
        document.getElementById('serviceForm').reset();
        editMode = false; currentEditId = null;
        document.getElementById('modalTitle').textContent = 'Nuevo Servicio';
        // Por defecto activo (1)
        document.getElementById('estado').value = '1'; 
        document.getElementById('serviceModal').classList.add('active');
    }

    function closeModal() { document.getElementById('serviceModal').classList.remove('active'); }

    window.editService = function(s) {
        editMode = true; currentEditId = s.srv_id;
        document.getElementById('modalTitle').textContent = 'Editar Servicio';
        
        document.getElementById('serviceId').value = s.srv_id;
        document.getElementById('nombre').value = s.srv_nombre;
        document.getElementById('categoria').value = s.cats_id || s.srv_categoriaId; 
        document.getElementById('precio').value = s.srv_precio;
        document.getElementById('duracion').value = s.srv_duracionMinutos;
        document.getElementById('puntos').value = s.srv_puntosFidelizacion;
        // Asignar estado (asegurando que sea 1 o 0)
        document.getElementById('estado').value = s.srv_estado;

        document.getElementById('serviceModal').classList.add('active');
    }

    async function saveService(e) {
        e.preventDefault();
        const fd = new FormData(e.target);
        const data = Object.fromEntries(fd);
        
        const url = editMode ? `/admin/servicios/${currentEditId}` : '/admin/servicios';
        const method = editMode ? 'PUT' : 'POST';

        try {
            const res = await fetch(url, {
                method: method,
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
                body: JSON.stringify(data)
            });
            
            if(res.ok) {
                alert('Guardado correctamente. La página se recargará.');
                location.reload(); 
            } else {
                const err = await res.json();
                alert('Error: ' + JSON.stringify(err));
            }
        } catch(err) { console.error(err); alert('Error de conexión'); }
    }

    async function deleteService(id) {
        if(!confirm('¿Estás seguro? Esta acción desactivará el servicio.')) return;
        try {
            const res = await fetch(`/admin/servicios/${id}`, {
                method: 'DELETE',
                headers: {'X-CSRF-TOKEN': csrfToken}
            });
            const data = await res.json();
            if(res.ok && data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'No se pudo eliminar'));
            }
        } catch(err) { console.error(err); }
    }

    // --- UTILIDADES ---
    function updateResultsInfo() { document.getElementById('resultsInfo').innerText = `Mostrando ${filteredServices.length} resultados`; }
    function changePageSize() { pageSize = parseInt(document.getElementById('pageSize').value); currentPage = 1; renderTable(); updatePagination(); }
    
    function updatePagination() { 
        totalPages = Math.ceil(filteredServices.length / pageSize);
        document.getElementById('pageNumbers').innerText = `Página ${currentPage} de ${totalPages}`;
        document.getElementById('prevBtn').disabled = currentPage === 1;
        document.getElementById('nextBtn').disabled = currentPage === totalPages || totalPages === 0;
    }
    function previousPage() { if(currentPage>1){currentPage--; renderTable(); updatePagination();} }
    function nextPage() { if(currentPage<totalPages){currentPage++; renderTable(); updatePagination();} }
    function clearFilters() { 
        document.getElementById('searchInput').value = ''; 
        document.getElementById('filterCategoria').value = ''; 
        document.getElementById('filterEstado').value = ''; 
        applyFilters(); 
    }
</script>
@endsection