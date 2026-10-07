@extends('layouts.app')

@section('title', 'Gestión de Usuarios - StyleNow')

@push('styles')
<style>
        :root {
            --bg-primary: #0a0a0a;
            --bg-secondary: #1a1a1a;
            --bg-card: #2a2a2a;
            --text-primary: #ffffff;
            --text-secondary: #cccccc;
            --text-muted: #aeaeae;
            --accent-primary: #fad370;
            --accent-secondary: #0097A7;
            --border-color: rgba(0, 188, 212, 0.3);
            --hover-bg: rgba(0, 188, 212, 0.1);
            --badge-active-bg: rgba(76, 175, 80, 0.2);
            --badge-active-color: #4caf50;
            --badge-inactive-bg: rgba(244, 67, 54, 0.2);
            --badge-inactive-color: #f44336;
            --badge-admin-bg: rgba(156, 39, 176, 0.2);
            --badge-admin-color: #9c27b0;
            --badge-employee-bg: rgba(33, 150, 243, 0.2);
            --badge-employee-color: #2196f3;
            --badge-client-bg: rgba(255, 193, 7, 0.2);
            --badge-client-color: #ffc107;
            --chart-primary: #00BCD4;
            --chart-secondary: #80DEEA;
        }
        [data-theme="light"] {
            --bg-primary: #f5f5f5;
            --bg-secondary: #ffffff;
            --bg-card: #fafafa;
            --text-primary: #000000;
            --text-secondary: #333333;
            --text-muted: #aeaeae;
            --accent-primary: #fad370;
            --accent-secondary: #0097A7;
            --border-color: rgba(0, 188, 212, 0.5);
            --hover-bg: rgba(0, 188, 212, 0.15);
            --badge-active-bg: rgba(76, 175, 80, 0.15);
            --badge-active-color: #2e7d32;
            --badge-inactive-bg: rgba(244, 67, 54, 0.15);
            --badge-inactive-color: #c62828;
            --badge-admin-bg: rgba(156, 39, 176, 0.15);
            --badge-admin-color: #7b1fa2;
            --badge-employee-bg: rgba(33, 150, 243, 0.15);
            --badge-employee-color: #1565c0;
            --badge-client-bg: rgba(255, 193, 7, 0.15);
            --badge-client-color: #ff8f00;
            --chart-primary: #0097A7;
            --chart-secondary: #80DEEA;
        }
     
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-title h1 { font-size: 32px; margin-bottom: 5px; background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .header-subtitle { color: var(--text-muted); font-size: 14px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 20px; }
        .stat-label { font-size: 13px; color: var(--text-muted); margin-bottom: 8px; margin-top: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value { font-size: 28px; font-weight: bold; color: var(--text-primary); }
        .stat-subvalue { font-size: 12px; color: var(--text-muted); margin-top: 5px; }
        .toolbar { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 20px; margin-bottom: 30px; }
        .toolbar-row { display: flex; gap: 15px; flex-wrap: wrap; align-items: center; margin-bottom: 15px; }
        .toolbar-row:last-child { margin-bottom: 0; }
        .search-box { flex: 1; min-width: 280px; position: relative; }
        .search-box input { width: 100%; padding: 12px 15px 12px 45px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 15px; transition: all 0.3s; }
        .search-box input:focus { outline: none; border-color: var(--accent-primary); box-shadow: 0 0 0 3px var(--hover-bg); }
        .search-box::before { content: '🔍'; position: absolute; left: 15px; top: 50%; transform: translateY(-50%); font-size: 18px; color: var(--text-muted); }
        .filter-group { display: flex; gap: 10px; flex-wrap: wrap; }
        .filter-select { padding: 12px 15px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 14px; font-weight: 600; min-width: 180px; }
        .clear-filters, .view-all-btn { padding: 12px 20px; background: transparent; border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-muted); cursor: pointer; font-size: 14px; }
        .clear-filters:hover { border-color: var(--accent-primary); color: var(--accent-primary); }
        .view-all-btn { background: var(--accent-primary); color: #000; border: none; font-weight: bold; }
        
        .table-section { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; overflow-x: auto; }
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .section-title { font-size: 20px; font-weight: bold; color: var(--text-primary); }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 15px; text-align: left; font-size: 13px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; border-bottom: 2px solid var(--border-color); }
        td { padding: 18px 15px; border-bottom: 1px solid var(--border-color); font-size: 14px; color: var(--text-secondary); }
        tbody tr:hover { background: rgba(250, 211, 112, 0.1); }
        .user-name { font-weight: 600; color: var(--text-primary); }
        
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
        .badge-active { background: var(--badge-active-bg); color: var(--badge-active-color); border: 1px solid var(--badge-active-color); }
        .badge-inactive { background: var(--badge-inactive-bg); color: var(--badge-inactive-color); border: 1px solid var(--badge-inactive-color); }
        .badge-role { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; margin-left: 8px; }
        .badge-admin { background: var(--badge-admin-bg); color: var(--badge-admin-color); border: 1px solid var(--badge-admin-color); }
        .badge-employee { background: var(--badge-employee-bg); color: var(--badge-employee-color); border: 1px solid var(--badge-employee-color); }
        .badge-client { background: var(--badge-client-bg); color: var(--badge-client-color); border: 1px solid var(--badge-client-color); }
        
        .action-buttons { display: flex; gap: 8px; }
        .action-btn { width: 35px; height: 35px; border-radius: 8px; border: none; cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center; }
        .btn-view { background: var(--hover-bg); color: var(--accent-primary); }
        .btn-edit { background: rgba(33, 150, 243, 0.2); color: #2196f3; }
        .btn-delete { background: rgba(244, 67, 54, 0.2); color: #f44336; }
        .btn-reset { background: rgba(255, 193, 7, 0.2); color: #ffc107; }
        
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); z-index: 1000; align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 20px; padding: 30px; width: 90%; max-width: 800px; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; margin-bottom: 25px; }
        .modal-title { font-size: 24px; font-weight: bold; color: var(--text-primary); }
        .close-btn { font-size: 28px; cursor: pointer; color: var(--text-muted); }
        
        .form-group { margin-bottom: 20px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: var(--text-secondary); }
        .form-input, .form-select, .form-textarea { width: 100%; padding: 12px 15px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); }
        .form-actions { display: flex; gap: 12px; margin-top: 25px; }
        .btn-cancel { flex: 1; padding: 12px; background: var(--bg-secondary); color: var(--text-primary); border: 2px solid var(--border-color); border-radius: 10px; cursor: pointer; }
        .btn-save { flex: 1; padding: 12px; background: var(--accent-primary); color: #000; border: none; border-radius: 10px; cursor: pointer; font-weight: bold; }
        
        .checkbox-container { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
        .checkbox-item { background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 8px; padding: 8px 12px; display: flex; align-items: center; gap: 8px; cursor: pointer; }
        .checkbox-item:hover { border-color: var(--accent-primary); background: var(--hover-bg); }
        .checkbox-item input { accent-color: var(--accent-primary); transform: scale(1.2); cursor: pointer; }
        .checkbox-item label { color: var(--text-primary); font-size: 14px; cursor: pointer; margin: 0; }
        
        .badge-comision { background: rgba(250, 211, 112, 0.15); color: var(--accent-primary); padding: 4px 8px; border-radius: 12px; border: 1px dashed var(--accent-primary); font-size: 11px; font-weight: bold; margin-top: 4px; display: inline-block;}
</style>
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('topbar-left')
    <h1>👥 Gestión de Usuarios</h1>
    <p>Administra la información de empleados y clientes</p>
@endsection

@section('topbar-actions')
    <div class="user-date">
        <p class="user-greeting">Hoy</p>
        <p class="current-date" id="current-date"></p>
    </div>
@endsection

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-label">Total Usuarios</div>
            <div class="stat-value" id="totalUsuarios">0</div>
            <div class="stat-subvalue" id="activeUsuarios">0 activos</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💼</div>
            <div class="stat-label">Empleados Activos</div>
            <div class="stat-value" id="totalEmpleados">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👤</div>
            <div class="stat-label">Clientes Registrados</div>
            <div class="stat-value" id="totalClientes">0</div>
        </div>
    </div>

    <div class="toolbar">
        <div class="toolbar-row">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Buscar usuarios...">
            </div>
            <button class="view-all-btn" onclick="openModal()">➕ Nuevo Usuario</button>
        </div>
        <div class="toolbar-row">
            <div class="filter-group">
                <select class="filter-select" id="filterRol">
                    <option value="">👥 Todos los Roles</option>
                    <option value="Administrador">Administrador</option>
                    <option value="Estilista">Estilista</option>
                    <option value="Recepcionista">Recepcionista</option>
                    <option value="Cliente">Cliente</option>
                </select>
                <select class="filter-select" id="filterTipo">
                    <option value="">📋 Todos los Tipos</option>
                    <option value="administrador">👑 Administrador</option>
                    <option value="empleado">💼 Empleado</option>
                    <option value="cliente">👤 Cliente</option>
                </select>
                <select class="filter-select" id="filterEstado">
                    <option value="">📈 Todos los Estados</option>
                    <option value="A">✅ Activos</option>
                    <option value="I">❌ Inactivos</option>
                </select>
                <select class="filter-select" id="filterSucursal">
                    <option value="">🏪 Todas las Sucursales</option>
                    </select>
                <button class="clear-filters" onclick="clearFilters()">🔄 Limpiar Filtros</button>
            </div>
        </div>
    </div>

    <div class="table-section">
        <div class="section-header">
            <div class="section-title">📋 Lista de Usuarios</div>
            <div class="results-info" id="resultsInfo">Mostrando 0 usuarios</div>
        </div>
        <div id="loadingIndicator" style="text-align:center; padding:20px; color:var(--accent-primary);">Cargando usuarios...</div>
        
        <table id="usuariosTableContainer">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Contacto</th>
                    <th>Rol / Detalles</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="usuariosTable">
            </tbody>
        </table>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
            <span id="pageNumbers" style="color:var(--text-muted); font-size:14px;"></span>
            <div style="display: flex; gap: 10px;">
                <button class="clear-filters" onclick="previousPage()" id="prevBtn">← Anterior</button>
                <button class="clear-filters" onclick="nextPage()" id="nextBtn">Siguiente →</button>
            </div>
        </div>
    </div>

    <div class="modal" id="usuarioModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Nuevo Usuario</h2>
                <span class="close-btn" onclick="closeModal()">×</span>
            </div>
            
            <form id="usuarioForm">
                <input type="hidden" id="usuarioId" name="usr_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nombre <span>*</span></label>
                        <input type="text" id="nombre" name="usr_nombre" class="form-input" placeholder="Ej: María" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Apellido <span>*</span></label>
                        <input type="text" id="apellido" name="usr_apellido" class="form-input" placeholder="Ej: González" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Cédula <span>*</span></label>
                        <input type="text" id="cedula" name="usr_cedula" class="form-input" placeholder="Ej: 1234567890" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teléfono <span>*</span></label>
                        <input type="text" id="telefono" name="usr_telefono" class="form-input" placeholder="Ej: 0991234567" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Email <span>*</span></label>
                    <input type="email" id="email" name="usr_email" class="form-input" placeholder="Ej: usuario@newstyle.com" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Rol <span>*</span></label>
                        <select class="form-select" id="rol" name="usr_rolId" required onchange="toggleEmpleadoFields()">
                            <option value="">Seleccionar rol...</option>
                            <option value="1">Administrador</option>
                            <option value="2">Estilista (Empleado)</option>
                            <option value="3">Recepcionista (Cliente)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="usr_estado">
                            <option value="A">✅ Activo</option>
                            <option value="I">❌ Inactivo</option>
                        </select>
                    </div>
                </div>

                <div id="empleadoFields" style="display: none; background: rgba(0, 188, 212, 0.05); padding: 20px; border-radius: 10px; border: 1px dashed var(--accent-primary); margin-bottom: 20px;">
                    
                    <div class="form-group">
                        <label class="form-label" style="color: var(--accent-primary);">🏢 Asignar Sucursal <span>*</span></label>
                        <select class="form-select" id="sucursal" name="emp_sucursalId" style="border-color: var(--accent-primary);">
                            <option value="">Seleccionar sucursal...</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" style="color: var(--accent-primary);">💵 Sueldo Base Fijo ($) <span>*</span></label>
                            <input type="number" step="0.01" class="form-input" id="sueldoBase" name="emp_sueldoBase" style="border-color: var(--accent-primary);" value="0.00">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="color: var(--accent-primary);">🛍️ Comisión Productos (%) <span>*</span></label>
                            <input type="number" class="form-input" id="comisionProductos" name="emp_comisionProductos" style="border-color: var(--accent-primary);" value="10">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" style="color: var(--accent-primary);">🎯 Meta Mensual Ventas ($) <span>*</span></label>
                            <input type="number" step="0.01" class="form-input" id="metaMensual" name="emp_metaMensual" style="border-color: var(--accent-primary);" value="0.00">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="color: var(--accent-primary);">🎁 Bono por Meta ($) <span>*</span></label>
                            <input type="number" step="0.01" class="form-input" id="bonoMeta" name="emp_bonoMeta" style="border-color: var(--accent-primary);" value="0.00">
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 5px;">
                        <label class="form-label" style="color: var(--accent-primary);">✂️ Especialidades (Puede elegir varias) <span>*</span></label>
                        <div id="categoriasContainer" class="checkbox-container">
                            </div>
                    </div>

                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Contraseña <span id="passwordRequired">*</span></label>
                        <input type="password" id="password" name="usr_password" class="form-input" placeholder="********">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirmar Contraseña</label>
                        <input type="password" id="confirmPassword" name="usr_password_confirmation" class="form-input" placeholder="********">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Observaciones (Opcional)</label>
                    <textarea id="observaciones" name="observaciones" class="form-textarea" placeholder="Información adicional sobre el usuario..." rows="2"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn-save" id="saveButton">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="detailModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Detalles del Usuario</h2>
                <span class="close-btn" onclick="closeDetailModal()">×</span>
            </div>
            <div id="detailContent"></div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let allUsuarios = [];
    let currentPage = 1, pageSize = 10, totalPages = 1;
    let filteredUsuarios = [];
    let editMode = false;
    let currentEditId = null;
    let categoriasGlobales = []; 

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    document.addEventListener('DOMContentLoaded', function() {
        cargarUsuarios();
        cargarCatalogos(); 
        setupEventListeners();

        const today = new Date();
        document.getElementById('current-date').textContent = today.toLocaleDateString('es-EC', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    });

    async function cargarCatalogos() {
        try {
            let res = await fetch('/admin/usuarios/sucursales');
            let data = await res.json();
            const selectSucursalModal = document.getElementById('sucursal');
            const selectSucursalFiltro = document.getElementById('filterSucursal');
            data.forEach(s => {
                selectSucursalModal.add(new Option(s.nombre, s.id));
                selectSucursalFiltro.add(new Option(s.nombre, s.nombre));
            });

            res = await fetch('/admin/usuarios/categorias');
            categoriasGlobales = await res.json();
            
            const catContainer = document.getElementById('categoriasContainer');
            categoriasGlobales.forEach(c => {
                catContainer.innerHTML += `
                    <div class="checkbox-item">
                        <input type="checkbox" id="cat_${c.id}" name="especialidades[]" value="${c.id}">
                        <label for="cat_${c.id}">${c.nombre}</label>
                    </div>
                `;
            });
        } catch (e) { console.error("Error cargando catálogos", e); }
    }

    async function cargarUsuarios() {
        try {
            document.getElementById('loadingIndicator').style.display = 'block';
            const response = await fetch('/admin/usuarios/data', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            allUsuarios = await response.json();
            filteredUsuarios = [...allUsuarios];
            updateStats(); renderTable(); updatePagination();
        } catch (error) {
            Swal.fire({icon: 'error', title: 'Error BD', text: 'No se pudieron cargar los usuarios.', background: '#2a2a2a', color: '#fff'});
        } finally {
            document.getElementById('loadingIndicator').style.display = 'none';
        }
    }

    function setupEventListeners() {
        document.getElementById('usuarioForm').addEventListener('submit', saveUsuario);
        document.getElementById('searchInput').addEventListener('input', applyFilters);
        document.getElementById('filterTipo').addEventListener('change', applyFilters);
        document.getElementById('filterRol').addEventListener('change', applyFilters);
        document.getElementById('filterEstado').addEventListener('change', applyFilters);
        document.getElementById('filterSucursal').addEventListener('change', applyFilters);
    }

    function toggleEmpleadoFields() {
        const rolSelect = document.getElementById('rol');
        const empleadoFields = document.getElementById('empleadoFields');
        const sucursalSelect = document.getElementById('sucursal');
        
        const sb = document.getElementById('sueldoBase');
        const cp = document.getElementById('comisionProductos');
        const mm = document.getElementById('metaMensual');
        const bm = document.getElementById('bonoMeta');

        if (rolSelect.value === "2") { 
            empleadoFields.style.display = 'block';
            sucursalSelect.required = true;
            sb.required = true; cp.required = true; mm.required = true; bm.required = true;
        } else {
            empleadoFields.style.display = 'none';
            sucursalSelect.required = false;
            sb.required = false; cp.required = false; mm.required = false; bm.required = false;
            
            sucursalSelect.value = '';
            sb.value = '0.00'; cp.value = '10'; mm.value = '0.00'; bm.value = '0.00';
            document.querySelectorAll('input[name="especialidades[]"]').forEach(cb => cb.checked = false);
        }
    }

    function updateStats() {
        document.getElementById('totalUsuarios').textContent = allUsuarios.length;
        document.getElementById('activeUsuarios').textContent = `${allUsuarios.filter(u => u.usr_estado === 'A').length} activos`;
        document.getElementById('totalEmpleados').textContent = allUsuarios.filter(u => u.tipo_usuario === 'empleado' && u.usr_estado === 'A').length;
        document.getElementById('totalClientes').textContent = allUsuarios.filter(u => u.tipo_usuario === 'cliente' && u.usr_estado === 'A').length;
    }

    function getTipoUsuarioClass(tipo) {
        switch(tipo) { case 'empleado': return 'badge-employee'; case 'cliente': return 'badge-client'; case 'administrador': return 'badge-admin'; default: return ''; }
    }
    function getTipoUsuarioText(tipo) {
        switch(tipo) { case 'empleado': return '💼 Empleado'; case 'cliente': return '👤 Cliente'; case 'administrador': return '👑 Admin'; default: return '❓ N/A'; }
    }

    function renderTable() {
        const tableBody = document.getElementById('usuariosTable');
        const startIndex = (currentPage - 1) * pageSize;
        const endIndex = startIndex + pageSize;
        const usuariosToShow = filteredUsuarios.slice(startIndex, endIndex);
        tableBody.innerHTML = '';

        if (usuariosToShow.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:20px; color:var(--text-muted);">No se encontraron usuarios</td></tr>`;
            return;
        }

        usuariosToShow.forEach(usuario => {
            const tipoClase = getTipoUsuarioClass(usuario.tipo_usuario);
            const tipoTexto = getTipoUsuarioText(usuario.tipo_usuario);
            
            let detallesHTML = '';
            if (usuario.tipo_usuario === 'empleado') {
                const espBadge = usuario.especialidades && usuario.especialidades.length > 0 ? ` | ${usuario.especialidades.length} Esp.` : '';
                const financieroTxt = `Sueldo: $${usuario.emp_sueldoBase || '0.00'} | Prod: ${usuario.emp_comisionProductos || '0'}%`;
                detallesHTML = `
                    <br><small style="color:#aaa;">📍 ${usuario.sucursal || 'Global'}${espBadge}</small>
                    <br><span class="badge-comision">💰 ${financieroTxt}</span>
                `;
            }

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>#${usuario.usr_id}</td>
                <td><span class="user-name">${usuario.usr_nombre} ${usuario.usr_apellido}</span></td>
                <td><div style="font-size: 14px; color: var(--text-muted);">📞 ${usuario.usr_telefono || 'N/A'}<br>📧 ${usuario.usr_email}</div></td>
                <td>
                    <span style="font-weight: 600; color: var(--text-primary);">${usuario.rol_nombre || 'Sin rol'}</span>
                    <span class="badge-role ${tipoClase}">${tipoTexto}</span>
                    ${detallesHTML}
                </td>
                <td><span class="badge ${usuario.usr_estado === 'A' ? 'badge-active' : 'badge-inactive'}">${usuario.usr_estado === 'A' ? '✅ Activo' : '❌ Inactivo'}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn btn-view" onclick="viewUsuario(${usuario.usr_id})" title="Ver Detalles">👁️</button>
                        <button class="action-btn btn-edit" onclick="editUsuario(${usuario.usr_id})" title="Editar">✏️</button>
                        <button class="action-btn btn-delete" onclick="deleteUsuario(${usuario.usr_id})" title="Eliminar">🗑️</button>
                        ${usuario.tipo_usuario === 'empleado' ? `<button class="action-btn btn-reset" onclick="resetPassword(${usuario.usr_id})" title="Reset Pass">🔑</button>` : ''}
                    </div>
                </td>
            `;
            tableBody.appendChild(row);
        });
        updateResultsInfo();
    }

    function applyFilters() {
        const searchText = document.getElementById('searchInput').value.toLowerCase().trim();
        const tipoFilter = document.getElementById('filterTipo').value;
        const rolFilter = document.getElementById('filterRol').value;
        const estadoFilter = document.getElementById('filterEstado').value;
        const sucursalFilter = document.getElementById('filterSucursal').value;

        filteredUsuarios = allUsuarios.filter(u => {
            if (searchText) {
                const text = `${u.usr_nombre} ${u.usr_apellido} ${u.usr_email} ${u.usr_cedula}`.toLowerCase();
                if (!text.includes(searchText)) return false;
            }
            if (tipoFilter && u.tipo_usuario !== tipoFilter) return false;
            if (rolFilter && u.rol_nombre !== rolFilter) return false;
            if (estadoFilter && u.usr_estado !== estadoFilter) return false;
            if (sucursalFilter && u.sucursal !== sucursalFilter) return false;
            return true;
        });

        currentPage = 1; renderTable(); updatePagination();
    }

    function clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('filterTipo').value = '';
        document.getElementById('filterRol').value = '';
        document.getElementById('filterEstado').value = '';
        document.getElementById('filterSucursal').value = '';
        applyFilters();
    }

    function updateResultsInfo() {
        const total = filteredUsuarios.length;
        const start = (currentPage - 1) * pageSize + 1;
        const end = Math.min(start + pageSize - 1, total);
        const txt = total === 0 ? 'No hay usuarios' : `Mostrando ${start}-${end} de ${total}`;
        document.getElementById('resultsInfo').textContent = txt;
    }

    function updatePagination() {
        totalPages = Math.ceil(filteredUsuarios.length / pageSize) || 1;
        document.getElementById('pageNumbers').innerText = `Página ${currentPage} de ${totalPages}`;
        document.getElementById('prevBtn').disabled = currentPage === 1;
        document.getElementById('nextBtn').disabled = currentPage === totalPages;
    }

    function previousPage() { if (currentPage > 1) { currentPage--; renderTable(); updatePagination(); } }
    function nextPage() { if (currentPage < totalPages) { currentPage++; renderTable(); updatePagination(); } }

    window.editUsuario = function(id) {
        try {
            const usuario = allUsuarios.find(u => u.usr_id == id);
            if (usuario) openModal(usuario);
        } catch (e) { console.error("Error al editar:", e); }
    };

    window.openModal = function(usuario = null) {
        const modal = document.getElementById('usuarioModal');
        const title = document.getElementById('modalTitle');
        const passwordLabel = document.getElementById('passwordRequired');

        document.querySelectorAll('input[name="especialidades[]"]').forEach(cb => cb.checked = false);

        if (usuario) {
            editMode = true; currentEditId = usuario.usr_id;
            title.textContent = 'Editar Usuario'; 
            passwordLabel.textContent = '(opcional)';
            
            document.getElementById('usuarioId').value = usuario.usr_id;
            document.getElementById('nombre').value = usuario.usr_nombre;
            document.getElementById('apellido').value = usuario.usr_apellido;
            document.getElementById('cedula').value = usuario.usr_cedula;
            document.getElementById('telefono').value = usuario.usr_telefono || '';
            document.getElementById('email').value = usuario.usr_email;
            document.getElementById('rol').value = usuario.usr_rolId || '';
            document.getElementById('estado').value = usuario.usr_estado || 'A';
            document.getElementById('password').value = '';
            document.getElementById('confirmPassword').value = '';
            
            if(document.getElementById('observaciones')) {
                document.getElementById('observaciones').value = usuario.observaciones || '';
            }
            
            if(usuario.usr_rolId == 2) { 
                document.getElementById('sucursal').value = usuario.emp_sucursalId || ''; 
                
                // CARGAMOS LA ESTRUCTURA FINANCIERA
                document.getElementById('sueldoBase').value = usuario.emp_sueldoBase || '0.00'; 
                document.getElementById('comisionProductos').value = usuario.emp_comisionProductos || '10'; 
                document.getElementById('metaMensual').value = usuario.emp_metaMensual || '0.00'; 
                document.getElementById('bonoMeta').value = usuario.emp_bonoMeta || '0.00'; 
                
                if(usuario.especialidades && Array.isArray(usuario.especialidades)) {
                    usuario.especialidades.forEach(catId => {
                        const cb = document.getElementById(`cat_${catId}`);
                        if(cb) cb.checked = true;
                    });
                }
            }
            toggleEmpleadoFields(); 
        } else {
            editMode = false; currentEditId = null;
            title.textContent = 'Nuevo Usuario'; passwordLabel.textContent = '*';
            document.getElementById('usuarioForm').reset();
            document.getElementById('estado').value = 'A';
            toggleEmpleadoFields(); 
        }
        modal.classList.add('active');
    };

    window.closeModal = function() { document.getElementById('usuarioModal').classList.remove('active'); document.getElementById('usuarioForm').reset(); };

    window.viewUsuario = function(id) {
        const usuario = allUsuarios.find(u => u.usr_id == id);
        if (!usuario) return;

        const tipoClase = getTipoUsuarioClass(usuario.tipo_usuario);
        const tipoTexto = getTipoUsuarioText(usuario.tipo_usuario);

        const html = `
            <div style="padding: 20px;">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px;">
                    <div style="width: 80px; height: 80px; background: var(--hover-bg); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 32px;">👤</div>
                    <div>
                        <h3 style="font-size: 22px; margin-bottom: 5px; color: var(--text-primary);">${usuario.usr_nombre} ${usuario.usr_apellido}</h3>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <span class="badge ${usuario.usr_estado === 'A' ? 'badge-active' : 'badge-inactive'}">${usuario.usr_estado === 'A' ? '✅ Activo' : '❌ Inactivo'}</span>
                            <span class="badge-role ${tipoClase}">${tipoTexto}</span>
                            <span style="background: var(--hover-bg); padding: 6px 12px; border-radius: 20px; font-size: 12px;">${usuario.rol_nombre || 'Sin rol'}</span>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div>
                        <h4 style="font-size: 14px; color: var(--text-muted); margin-bottom: 10px; text-transform: uppercase;">Información Personal</h4>
                        <div style="display: flex; flex-direction: column; gap: 8px; font-size:14px; color:var(--text-secondary);">
                            <div><span style="color: var(--text-muted);">🆔</span> Cédula: ${usuario.usr_cedula}</div>
                            <div><span style="color: var(--text-muted);">📞</span> Teléfono: ${usuario.usr_telefono || 'No registrado'}</div>
                            <div><span style="color: var(--text-muted);">📧</span> Email: ${usuario.usr_email}</div>
                            <div><span style="color: var(--text-muted);">📅</span> Registro: ${usuario.usr_fechaRegistro}</div>
                        </div>
                    </div>
                    <div>
                        <h4 style="font-size: 14px; color: var(--text-muted); margin-bottom: 10px; text-transform: uppercase;">Detalles del Contrato</h4>
                        <div style="display: flex; flex-direction: column; gap: 8px; font-size:14px; color:var(--text-secondary);">
                            <div><span style="color: var(--text-muted);">💼</span> Tipo: ${tipoTexto}</div>
                            ${usuario.tipo_usuario === 'empleado' ? `
                                <div><span style="color: var(--text-muted);">🏢</span> Sucursal: ${usuario.emp_sucursalId || 'Global'}</div>
                                <div><span style="color: var(--text-muted);">💵</span> Sueldo Base: $${usuario.emp_sueldoBase || '0.00'}</div>
                                <div><span style="color: var(--text-muted);">🛍️</span> Com. Productos: ${usuario.emp_comisionProductos || '0'}%</div>
                                <div><span style="color: var(--text-muted);">🎯</span> Meta: $${usuario.emp_metaMensual || '0.00'} | Bono: $${usuario.emp_bonoMeta || '0.00'}</div>
                            ` : ''}
                        </div>
                    </div>
                </div>

                <div style="margin-top: 25px; display: flex; gap: 10px;">
                    <button style="flex:1; padding:12px; background:var(--accent-primary); color:#000; font-weight:bold; border:none; border-radius:8px; cursor:pointer;" onclick="editUsuario(${usuario.usr_id}); closeDetailModal();">✏️ Editar Usuario</button>
                    <button style="flex:1; padding:12px; background:var(--bg-secondary); color:var(--text-primary); border:2px solid var(--border-color); border-radius:8px; cursor:pointer;" onclick="closeDetailModal()">Cerrar</button>
                </div>
            </div>
        `;

        document.getElementById('detailContent').innerHTML = html;
        document.getElementById('detailModal').classList.add('active');
    };

    window.closeDetailModal = function() { document.getElementById('detailModal').classList.remove('active'); };

    async function saveUsuario(event) {
        event.preventDefault();
        const password = document.getElementById('password').value.trim();
        const confirm = document.getElementById('confirmPassword').value.trim();

        if (editMode) {
            if (password && password !== confirm) { Swal.fire('Error', 'Las contraseñas no coinciden', 'error'); return; }
            if (password && password.length < 6) { Swal.fire('Error', 'Mínimo 6 caracteres en la clave', 'error'); return; }
        } else {
            if (!password) { Swal.fire('Error', 'La contraseña es obligatoria', 'error'); return; }
            if (password !== confirm) { Swal.fire('Error', 'Las contraseñas no coinciden', 'error'); return; }
            if (password.length < 6) { Swal.fire('Error', 'Mínimo 6 caracteres', 'error'); return; }
        }

        const formData = new FormData(document.getElementById('usuarioForm'));
        const data = Object.fromEntries(formData.entries());
        if (!password) delete data.usr_password;

        const especialidadesSeleccionadas = Array.from(document.querySelectorAll('input[name="especialidades[]"]:checked')).map(el => el.value);
        data.especialidades = especialidadesSeleccionadas;

        if (data.usr_rolId == "2" && especialidadesSeleccionadas.length === 0) {
            Swal.fire('Atención', 'Debes seleccionar al menos una especialidad para el empleado', 'warning');
            return;
        }

        const url = editMode ? `/admin/usuarios/${currentEditId}` : '/admin/usuarios';
        const method = editMode ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify(data)
            });
            const json = await response.json();

            if (!response.ok) {
                let msj = json.message || 'Error al guardar';
                if(json.errors) msj = Object.values(json.errors).flat().join('<br>');
                Swal.fire({ icon: 'error', title: 'Error', html: msj, background: '#2a2a2a', color: '#fff' });
                return;
            }

            Swal.fire({ icon: 'success', title: '¡Éxito!', text: editMode ? 'Usuario actualizado' : 'Usuario creado', background: '#2a2a2a', color: '#fff' });
            closeModal();
            await cargarUsuarios();
        } catch (err) { Swal.fire({ icon: 'error', title: 'Error de Red', text: 'Revisa tu base de datos', background: '#2a2a2a', color: '#fff' }); }
    }

    window.deleteUsuario = async function(id) {
        Swal.fire({
            title: '¿Eliminar usuario?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#f44336', confirmButtonText: 'Sí, eliminar', background: '#2a2a2a', color: '#fff'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/admin/usuarios/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken }});
                    if (!response.ok) throw new Error();
                    Swal.fire({ icon: 'success', title: 'Eliminado', background: '#2a2a2a', color: '#fff' });
                    await cargarUsuarios();
                } catch (err) { Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar', background: '#2a2a2a', color: '#fff' }); }
            }
        });
    };

    window.resetPassword = async function(id) {
        if (!confirm('¿Resetear la contraseña? Se generará una temporal.')) return;
        try {
            const response = await fetch(`/admin/usuarios/${id}/reset-password`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }});
            const json = await response.json();
            if (!response.ok) throw new Error(json.message || 'Error');
            Swal.fire({icon: 'success', title: 'Reseteada', text: `Nueva clave: ${json.temporal}`, background: '#2a2a2a', color: '#fff'});
        } catch (err) { Swal.fire('Error', 'No se pudo resetear la contraseña', 'error'); }
    };
</script>
@endsection