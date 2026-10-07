@extends('layouts.app')

@section('title', 'Gestión de Categorías - StyleNow')

@push('styles')
<style>
    :root {
        --bg-primary: #0a0a0a;
        --bg-secondary: #1a1a1a;
        --bg-card: #2a2a2a;
        --text-primary: #ffffff;
        --text-secondary: #cccccc;
        --text-muted: #888888;
        --accent-primary: #FAD370;
        --accent-secondary: #45a049;
        --accent-warning: #ff9800;
        --accent-danger: #f44336;
        --border-color: rgba(76, 175, 80, 0.3);
        --hover-bg: rgba(250, 211, 112, 0.1);
        --badge-active-bg: rgba(76, 175, 80, 0.2);
        --badge-active-color: #4caf50;
        --badge-inactive-bg: rgba(244, 67, 54, 0.2);
        --badge-inactive-color: #f44336;
        --tab-active-bg: rgba(250, 211, 112, 0.1);
        --tab-inactive-bg: var(--bg-secondary);
    }

    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; position: relative; overflow: hidden; transition: all 0.3s; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(76, 175, 80, 0.2); }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary)); }
    .stat-icon { width: 50px; height: 50px; background: var(--hover-bg); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 15px; }
    .stat-label { font-size: 13px; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-value { font-size: 28px; font-weight: bold; color: var(--text-primary); }
    .stat-subvalue { font-size: 12px; color: var(--text-muted); margin-top: 5px; }

    .tabs { display: flex; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 15px; overflow: hidden; margin-bottom: 30px; }
    .tab { flex: 1; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s; font-weight: 600; font-size: 16px; border-right: 2px solid var(--border-color); color: var(--text-secondary); }
    .tab.active { background: var(--tab-active-bg); color: var(--accent-primary); }
    .tab:last-child { border-right: none; }

    .toolbar { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
    .search-box { flex: 1; min-width: 280px; position: relative; }
    .search-box input { width: 100%; padding: 12px 15px 12px 45px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 15px; transition: all 0.3s; }
    .search-box input:focus { outline: none; border-color: var(--accent-primary); box-shadow: 0 0 0 3px var(--hover-bg); }
    .search-box::before { content: '🔍'; position: absolute; left: 15px; top: 50%; transform: translateY(-50%); font-size: 18px; color: var(--text-muted); }
    
    .add-btn { padding: 12px 24px; background: var(--accent-primary); border: none; border-radius: 10px; color: #000; cursor: pointer; transition: all 0.3s; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
    .add-btn:hover { background: var(--accent-secondary); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3); }

    .table-section { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 15px; text-align: left; font-size: 13px; color: var(--text-muted); text-transform: uppercase; border-bottom: 2px solid var(--border-color); }
    td { padding: 18px 15px; border-bottom: 1px solid var(--border-color); color: var(--text-secondary); }
    
    .badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-active { background: var(--badge-active-bg); color: var(--badge-active-color); border: 1px solid var(--badge-active-color); }
    .badge-inactive { background: var(--badge-inactive-bg); color: var(--badge-inactive-color); border: 1px solid var(--badge-inactive-color); }
    .badge-asociados { background: rgba(255, 152, 0, 0.1); color: var(--accent-warning); padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; border: 1px solid var(--accent-warning); }
    .badge-comision { background: rgba(0, 188, 212, 0.1); color: #00BCD4; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; border: 1px dashed #00BCD4; }

    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 1000; align-items: center; justify-content: center; }
    .modal.active { display: flex; }
    .modal-content { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 20px; padding: 30px; width: 90%; max-width: 500px; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    .modal-title { font-size: 24px; font-weight: bold; color: var(--text-primary); }
    .close-btn { font-size: 28px; cursor: pointer; color: var(--text-muted); transition: all 0.3s; }
    .close-btn:hover { color: var(--accent-primary); transform: rotate(90deg); }
    
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: var(--text-secondary); }
    .form-input, .form-select, .form-textarea { width: 100%; padding: 12px 15px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); font-size: 14px; transition: all 0.3s; }
    .form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: var(--accent-primary); box-shadow: 0 0 0 3px var(--hover-bg); }
    .form-textarea { resize: vertical; min-height: 80px; }
    
    .form-actions { display: flex; gap: 12px; margin-top: 25px; }
    .btn-cancel { flex: 1; padding: 12px; background: var(--bg-secondary); color: var(--text-primary); border: 2px solid var(--border-color); border-radius: 10px; cursor: pointer; font-weight: 600; transition: all 0.3s; }
    .btn-cancel:hover { background: var(--bg-primary); }
    .btn-save { flex: 1; padding: 12px; background: var(--accent-primary); color: #000; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; transition: all 0.3s; }
    .btn-save:hover { background: var(--accent-secondary); }
    
    .tipo-indicator { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; margin-left: 10px; }
    .tipo-producto { background: rgba(33, 150, 243, 0.2); color: #2196f3; border: 1px solid #2196f3; }
    .tipo-servicio { background: rgba(156, 39, 176, 0.2); color: #9c27b0; border: 1px solid #9c27b0; }
    
    .btn-action { background: none; border: none; cursor: pointer; font-size: 18px; padding: 5px 8px; transition: all 0.3s; }
    .btn-action:hover { transform: scale(1.2); }
    .btn-disabled { opacity: 0.3; cursor: not-allowed; }
    .btn-disabled:hover { transform: none; }
</style>
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-label">Categorías Productos</div>
        <div class="stat-value">{{ $stats['total_p'] ?? 0 }}</div>
        <div class="stat-subvalue">{{ $stats['activas_p'] ?? 0 }} activas</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✂️</div>
        <div class="stat-label">Categorías Servicios</div>
        <div class="stat-value">{{ $stats['total_s'] ?? 0 }}</div>
        <div class="stat-subvalue">{{ $stats['activas_s'] ?? 0 }} activas</div>
    </div>
</div>

<div class="tabs">
    <div class="tab active" onclick="switchTab('productos')" id="tabProductos">
        📦 Categorías de Productos
    </div>
    <div class="tab" onclick="switchTab('servicios')" id="tabServicios">
        ✂️ Categorías de Servicios
    </div>
</div>

<div class="toolbar">
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Buscar categorías por nombre...">
    </div>
    <div style="color: var(--accent-primary); font-size: 18px; font-weight: bold;" id="currentTitle">
        📦 Listado Productos
    </div>
    <button class="add-btn" onclick="btnNuevaCategoria()">
        <span>➕</span> Nueva Categoría
    </button>
</div>

<div class="table-section" id="tableViewProductos">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Categoría</th>
                <th>Descripción</th>
                <th>Elementos Asociados</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tableBodyProductos">
            @forelse($categoriasProductos as $cat)
            <tr>
                <td>#{{ $cat->catp_id }}</td>
                <td>
                    <strong>{{ $cat->catp_nombre }}</strong>
                    <span class="tipo-indicator tipo-producto">📦 Producto</span>
                </td>
                <td>{{ $cat->catp_descripcion ?? 'Sin descripción' }}</td>
                <td>
                    @if($cat->items_asociados > 0)
                        <span class="badge-asociados">📦 {{ $cat->items_asociados }} Productos</span>
                    @else
                        <span style="color: var(--text-muted); font-size:12px;">Ninguno</span>
                    @endif
                </td>
                <td>
                    <span class="badge {{ $cat->catp_estado == '1' ? 'badge-active' : 'badge-inactive' }}">
                        {{ $cat->catp_estado == '1' ? 'Activa' : 'Inactiva' }}
                    </span>
                </td>
                <td>
                    <button class="btn-action" 
                            data-tipo="producto"
                            data-id="{{ $cat->catp_id }}"
                            data-nombre="{{ $cat->catp_nombre }}"
                            data-descripcion="{{ $cat->catp_descripcion ?? '' }}"
                            data-estado="{{ $cat->catp_estado }}"
                            onclick="editarCat(this)"
                            title="Editar">
                        ✏️
                    </button>
                    
                    @if($cat->items_asociados > 0)
                        <button class="btn-action btn-disabled" 
                                onclick="alert('No puedes eliminar esta categoría porque tiene {{ $cat->items_asociados }} producto(s) asignado(s). Cambia esos productos de categoría primero.')" 
                                title="No se puede eliminar (En uso)">
                            🗑️
                        </button>
                    @else
                        <button class="btn-action" 
                                data-tipo="producto"
                                data-id="{{ $cat->catp_id }}"
                                onclick="eliminarCat(this)"
                                title="Eliminar">
                            🗑️
                        </button>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center; padding:30px; color: var(--text-muted);">No hay categorías de productos.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="table-section" id="tableViewServicios" style="display: none;">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Categoría</th>
                <th>Descripción</th>
                <th>Comisión</th>
                <th>Elementos Asociados</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tableBodyServicios">
            @forelse($categoriasServicios as $cats)
            <tr>
                <td>#{{ $cats->cats_id }}</td>
                <td>
                    <strong>{{ $cats->cats_nombre }}</strong>
                    <span class="tipo-indicator tipo-servicio">✂️ Servicio</span>
                </td>
                <td>{{ $cats->cats_descripcion ?? 'Sin descripción' }}</td>
                <td>
                    <span class="badge-comision">💰 Paga {{ $cats->cats_porcentajeComision ?? 20 }}%</span>
                </td>
                <td>
                    @if($cats->items_asociados > 0)
                        <span class="badge-asociados">✂️ {{ $cats->items_asociados }} Servicios</span>
                    @else
                        <span style="color: var(--text-muted); font-size:12px;">Ninguno</span>
                    @endif
                </td>
                <td>
                    <span class="badge {{ $cats->cats_estado == '1' ? 'badge-active' : 'badge-inactive' }}">
                        {{ $cats->cats_estado == '1' ? 'Activa' : 'Inactiva' }}
                    </span>
                </td>
                <td>
                    <button class="btn-action" 
                            data-tipo="servicio"
                            data-id="{{ $cats->cats_id }}"
                            data-nombre="{{ $cats->cats_nombre }}"
                            data-descripcion="{{ $cats->cats_descripcion ?? '' }}"
                            data-estado="{{ $cats->cats_estado }}"
                            data-comision="{{ $cats->cats_porcentajeComision ?? 20 }}"
                            onclick="editarCat(this)"
                            title="Editar">
                        ✏️
                    </button>
                    
                    @if($cats->items_asociados > 0)
                        <button class="btn-action btn-disabled" 
                                onclick="alert('No puedes eliminar esta categoría porque tiene {{ $cats->items_asociados }} servicio(s) asignado(s). Cambia esos servicios de categoría primero.')" 
                                title="No se puede eliminar (En uso)">
                            🗑️
                        </button>
                    @else
                        <button class="btn-action" 
                                data-tipo="servicio"
                                data-id="{{ $cats->cats_id }}"
                                onclick="eliminarCat(this)"
                                title="Eliminar">
                            🗑️
                        </button>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center; padding:30px; color: var(--text-muted);">No hay categorías de servicios.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="modal" id="categoriaModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="modalTitle">Nueva Categoría</h2>
            <span class="close-btn" onclick="closeModal()">×</span>
        </div>
        <form action="{{ route('categorias.store') }}" method="POST" id="formCategoria">
            @csrf
            <input type="hidden" name="catp_id" id="input_catp_id">
            <input type="hidden" name="cats_id" id="input_cats_id">

            <div class="form-group">
                <label class="form-label">Tipo de Categoría *</label>
                <select class="form-select" name="tipo_categoria_db" id="select_tipo_db" required onchange="toggleComisionField()">
                    <option value="producto">📦 Categoría de Producto</option>
                    <option value="servicio">✂️ Categoría de Servicio</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Nombre *</label>
                <input type="text" name="catp_nombre" id="input_nombre" class="form-input" required>
            </div>

            <div class="form-group" id="div_comision" style="display: none; background: rgba(0, 188, 212, 0.05); padding: 15px; border-radius: 10px; border: 1px dashed var(--accent-primary);">
                <label class="form-label" style="color: var(--accent-primary);">💰 Porcentaje de Comisión al Empleado (%) *</label>
                <small style="color: var(--text-muted); display: block; margin-bottom: 8px;">Ej: Básica (20), Técnica (30), Premium (40)</small>
                <input type="number" name="cats_porcentajeComision" id="input_comision" class="form-input" min="0" max="100" value="20" style="border-color: var(--accent-primary);">
            </div>

            <div class="form-group">
                <label class="form-label">Descripción</label>
                <textarea name="catp_descripcion" id="input_descripcion" class="form-textarea"></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Estado</label>
                <select name="catp_estado" id="input_estado" class="form-select">
                    <option value="1">Activa</option>
                    <option value="0">Inactiva</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
                <button type="submit" class="btn-save">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchTab(tab) {
        const isProd = tab === 'productos';
        document.getElementById('tabProductos').classList.toggle('active', isProd);
        document.getElementById('tabServicios').classList.toggle('active', !isProd);
        
        document.getElementById('tableViewProductos').style.display = isProd ? 'block' : 'none';
        document.getElementById('tableViewServicios').style.display = isProd ? 'none' : 'block';
        
        document.getElementById('currentTitle').innerText = isProd ? '📦 Listado Productos' : '✂️ Listado Servicios';
        
        document.getElementById('searchInput').value = '';
        filterTable();
    }

    function toggleComisionField() {
        const tipo = document.getElementById('select_tipo_db').value;
        const divComision = document.getElementById('div_comision');
        
        if (tipo === 'servicio') {
            divComision.style.display = 'block';
        } else {
            divComision.style.display = 'none';
        }
    }

    function btnNuevaCategoria() {
        document.getElementById('formCategoria').reset();
        document.getElementById('input_catp_id').value = '';
        document.getElementById('input_cats_id').value = '';
        document.getElementById('input_comision').value = '20'; // Por defecto 20%
        document.getElementById('modalTitle').innerText = 'Nueva Categoría';
        
        const tabActivo = document.getElementById('tabProductos').classList.contains('active') ? 'producto' : 'servicio';
        document.getElementById('select_tipo_db').value = tabActivo;
        
        toggleComisionField();
        document.getElementById('categoriaModal').classList.add('active');
    }

    function editarCat(btn) {
        document.getElementById('formCategoria').reset();
        document.getElementById('input_catp_id').value = '';
        document.getElementById('input_cats_id').value = '';
        document.getElementById('modalTitle').innerText = 'Editar Categoría';
        
        const tipo = btn.dataset.tipo || btn.getAttribute('data-tipo');
        const id = btn.dataset.id || btn.getAttribute('data-id');
        const nombre = btn.dataset.nombre || btn.getAttribute('data-nombre');
        const descripcion = btn.dataset.descripcion || btn.getAttribute('data-descripcion');
        const estado = btn.dataset.estado || btn.getAttribute('data-estado');
        const comision = btn.dataset.comision || btn.getAttribute('data-comision');

        document.getElementById('select_tipo_db').value = tipo;
        document.getElementById('input_nombre').value = nombre;
        document.getElementById('input_descripcion').value = descripcion || '';
        document.getElementById('input_estado').value = estado;
        
        if(tipo === 'producto') {
            document.getElementById('input_catp_id').value = id;
        } else {
            document.getElementById('input_cats_id').value = id;
            document.getElementById('input_comision').value = comision || 20;
        }
        
        toggleComisionField();
        document.getElementById('categoriaModal').classList.add('active');
    }

    function eliminarCat(btn) {
        const tipo = btn.dataset.tipo || btn.getAttribute('data-tipo');
        const id = btn.dataset.id || btn.getAttribute('data-id');
        
        if(!tipo || !id) {
            alert('Error: No se pudo identificar la categoría a eliminar.');
            return;
        }

        if(confirm(`¿Estás seguro de que deseas eliminar permanentemente esta categoría de ${tipo}?`)) {
            window.location.href = `/admin/categorias/delete/${tipo}/${id}`;
        }
    }

    function closeModal() { 
        document.getElementById('categoriaModal').classList.remove('active'); 
    }

    document.getElementById('categoriaModal').addEventListener('click', function(e) {
        if(e.target === this) {
            closeModal();
        }
    });

    function filterTable() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        const isProd = document.getElementById('tabProductos').classList.contains('active');
        const tbody = isProd ? document.getElementById('tableBodyProductos') : document.getElementById('tableBodyServicios');
        const rows = tbody.getElementsByTagName('tr');

        for(let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const text = row.textContent.toLowerCase();
            
            if(text.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }

    document.getElementById('searchInput').addEventListener('keyup', filterTable);
</script>
@endsection