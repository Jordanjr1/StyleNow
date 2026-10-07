@extends('layouts.app')

@section('title', 'Gestión de Citas - StyleNow')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    :root {
        --bg-primary: #0a0a0a; --bg-secondary: #1a1a1a; --bg-card: #2a2a2a;
        --text-primary: #ffffff; --text-secondary: #cccccc; --text-muted: #888888;
        --accent-primary: #FF5722; --accent-secondary: #E64A19;
        --border-color: rgba(255, 87, 34, 0.3); --hover-bg: rgba(255, 87, 34, 0.1);
        
        --badge-pending-bg: rgba(255, 193, 7, 0.2); --badge-pending-color: #ffc107;
        --badge-confirmed-bg: rgba(76, 175, 80, 0.2); --badge-confirmed-color: #4caf50;
        --badge-completed-bg: rgba(158, 158, 158, 0.2); --badge-completed-color: #9e9e9e;
        --badge-cancelled-bg: rgba(244, 67, 54, 0.2); --badge-cancelled-color: #f44336;
        --badge-progreso-bg: rgba(250, 211, 112, 0.2); --badge-progreso-color: #fad370;
        --badge-porcobrar-bg: rgba(33, 150, 243, 0.2); --badge-porcobrar-color: #2196f3;
    }

    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; position: relative; overflow: hidden; }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary)); }
    .stat-label { font-size: 13px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; }
    .stat-value { font-size: 28px; font-weight: bold; color: var(--text-primary); }

    .toolbar { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 20px; margin-bottom: 30px; }
    .toolbar-row { display: flex; gap: 15px; flex-wrap: wrap; align-items: center; margin-bottom: 15px; }
    .toolbar-row:last-child { margin-bottom: 0; }
    .search-box { flex: 1; min-width: 280px; }
    .search-box input { width: 100%; padding: 12px 15px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); }
    .filter-select { padding: 12px 15px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 10px; color: var(--text-primary); min-width: 150px;}
    .view-all-btn, .calendar-btn { padding: 12px 20px; background: var(--hover-bg); border: 2px solid var(--border-color); border-radius: 10px; color: var(--accent-primary); cursor: pointer; font-weight: 600; }
    
    .table-section { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 15px; padding: 25px; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 15px; text-align: left; font-size: 13px; color: var(--text-muted); text-transform: uppercase; border-bottom: 2px solid var(--border-color); }
    td { padding: 18px 15px; border-bottom: 1px solid var(--border-color); font-size: 14px; color: var(--text-secondary); }
    tbody tr:hover { background: var(--hover-bg); }
    
    .badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
    .badge-Pendiente { background: var(--badge-pending-bg); color: var(--badge-pending-color); }
    .badge-Confirmada { background: var(--badge-confirmed-bg); color: var(--badge-confirmed-color); }
    .badge-Completada { background: var(--badge-completed-bg); color: var(--badge-completed-color); }
    .badge-Cancelada { background: var(--badge-cancelled-bg); color: var(--badge-cancelled-color); }
    .badge-EnProgreso { background: var(--badge-progreso-bg); color: var(--badge-progreso-color); }
    .badge-PorCobrar { background: var(--badge-porcobrar-bg); color: var(--badge-porcobrar-color); }
    
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; }
    .modal.active { display: flex; }
    .modal-content { background: var(--bg-card); padding: 30px; border-radius: 20px; width: 90%; max-width: 700px; border: 2px solid var(--border-color); max-height: 90vh; overflow-y: auto; }
    .modal-header { display: flex; justify-content: space-between; margin-bottom: 25px; }
    .modal-title { font-size: 24px; font-weight: bold; color: var(--text-primary); }
    .close-btn { font-size: 28px; cursor: pointer; color: var(--text-muted); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
    .form-group { margin-bottom: 15px; }
    .form-label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: var(--text-secondary); }
    .form-input, .form-select, .form-textarea { width: 100%; padding: 12px 15px; background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 10px; color: white; }
    .form-actions { display: flex; gap: 12px; margin-top: 25px; }
    .btn-save { width: 100%; padding: 12px; background: var(--accent-primary); border: none; border-radius: 10px; color: white; font-weight: bold; cursor: pointer; }
    .btn-cancel { width: 100%; padding: 12px; background: #444; border: none; border-radius: 10px; color: white; cursor: pointer; }
    .action-btn { background:none; border:none; cursor:pointer; font-size:18px; margin-right:5px; }
    
    .services-container { background: var(--bg-secondary); border: 2px solid var(--border-color); border-radius: 10px; padding: 15px; max-height: 250px; overflow-y: auto; }
    .service-category-group { margin-bottom: 15px; }
    .service-category-title { font-size: 13px; color: var(--accent-primary); margin-bottom: 10px; text-transform: uppercase; font-weight: bold; border-bottom: 1px solid var(--border-color); padding-bottom: 5px; }
    .service-checkbox-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; border-radius: 6px; transition: background 0.2s; }
    .service-checkbox-item:hover { background: var(--hover-bg); }
    .service-checkbox-label { display: flex; align-items: center; gap: 10px; cursor: pointer; flex: 1; font-size: 14px; color: var(--text-primary); }
    .totals-summary { display: flex; justify-content: space-around; background: rgba(255, 87, 34, 0.05); padding: 15px; border-radius: 10px; margin-top: 15px; border: 1px dashed var(--accent-primary); }
    .total-item { text-align: center; }
    .total-value { font-size: 24px; font-weight: bold; color: var(--accent-primary); }
    .total-value.price { color: #4caf50; }

    /* ESTILOS PARA EL PUNTO DE VENTA (POS) */
    .btn-checkout { background: #2196f3; color: white; padding: 6px 12px; border-radius: 8px; border: none; font-weight: bold; cursor: pointer; transition: 0.3s; margin-left: 10px;}
    .btn-checkout:hover { background: #1976d2; transform: scale(1.05);}
    .pos-cart { background: #1a1a1a; padding: 15px; border-radius: 10px; border: 1px solid #4caf50; min-height: 100px; margin-bottom: 15px;}
    .pos-item { display: flex; justify-content: space-between; border-bottom: 1px dashed #333; padding: 8px 0; font-size: 14px;}
    .pos-total-row { display: flex; justify-content: space-between; font-size: 18px; font-weight: bold; color: #4caf50; margin-top: 15px; padding-top: 10px; border-top: 2px solid #4caf50;}
</style>
@endpush

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Citas Hoy</div>
            <div class="stat-value" id="citasHoy">0</div>
        </div>
        <div class="stat-card" style="border-color: #2196f3;">
            <div class="stat-label">Pendientes por Cobrar (Caja)</div>
            <div class="stat-value" id="citasConfirmadas" style="color: #2196f3;">0</div>
        </div>
    </div>

    <div class="toolbar">
        <div class="toolbar-row">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Buscar cliente...">
            </div>
            <select class="filter-select" id="filterEstado">
                <option value="">Todos los Estados</option>
                <option value="Pendiente">Pendiente</option>
                <option value="Confirmada">Confirmada</option>
                <option value="En Progreso">En Progreso</option>
                <option value="Por Cobrar">Por Cobrar (Caja)</option>
                <option value="Completada">Completada</option>
                <option value="Cancelada">Cancelada</option>
            </select>
            <button class="view-all-btn" onclick="openModal()">➕ Nueva Cita</button>
            <button class="view-all-btn" onclick="ejecutarMarketing()" style="background: rgba(156, 39, 176, 0.1); border-color: #9c27b0; color: #9c27b0; margin-right: 10px;" title="Analiza las citas antiguas y envía correos de recordatorio">
                🤖 Enviar Recordatorios Automáticos
            </button>
        </div>
    </div>

    <div class="table-section" id="tableView">
        <div class="section-header" style="display:flex; justify-content:space-between; margin-bottom:15px;">
            <div class="section-title" style="color:var(--text-primary); font-weight:bold; font-size:18px;">📋 Lista de Citas</div>
            <div class="results-info" id="resultsInfo" style="color:var(--text-muted);">Mostrando 0 citas</div>
        </div>
        <div id="loadingIndicator" style="text-align:center; padding:20px; color:var(--accent-primary); font-weight:bold;">🔄 Cargando base de datos...</div>
        
        <table id="citasTableContainer">
            <thead>
                <tr><th>ID</th><th>Cliente / Servicio</th><th>Fecha y Hora</th><th>Empleado</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody id="citasTable"></tbody>
        </table>
        
        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
            <button class="view-all-btn" style="padding: 8px 15px; font-size:12px;" onclick="previousPage()" id="prevBtn">← Anterior</button>
            <span id="pageNumbers" style="color:var(--text-muted); align-self:center; font-size:14px;"></span>
            <button class="view-all-btn" style="padding: 8px 15px; font-size:12px;" onclick="nextPage()" id="nextBtn">Siguiente →</button>
        </div>
    </div>

    <div class="modal" id="posModal">
        <div class="modal-content" style="max-width: 500px; border-color: #4caf50;">
            <div class="modal-header">
                <h2 class="modal-title" style="color: #4caf50;">🛒 Caja / Facturación</h2>
                <span class="close-btn" onclick="closePosModal()">×</span>
            </div>
            
            <div style="color:var(--text-muted); font-size:14px; margin-bottom: 10px;">Cliente: <strong id="posCliente" style="color:white;"></strong></div>
            
            <div class="form-group mb-3">
                <label class="form-label" style="color: var(--accent-primary); font-weight: bold;">✂️ Servicios Realizados</label>
                <div id="posServiciosChecklist" class="services-container" style="background: #121212; border-color: #4caf50; padding: 10px;">
                    </div>
                <small class="text-muted" style="font-size: 12px;">Desmarca los servicios que el cliente NO se realizó.</small>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">🎁 Aplicar Promoción a los Servicios</label>
                <select id="posPromocion" class="form-select" onchange="calcularTotalPos()">
                    <option value="">Sin promoción</option>
                    </select>
            </div>

            <div class="pos-cart" id="posCartContainer">
                </div>

            <div class="form-row" style="align-items: end;">
                <div class="form-group" style="margin: 0;">
                    <label class="form-label">📦 Vender Producto Extra (Opcional)</label>
                    <select id="posAddProduct" class="form-select">
                        <option value="">Buscar producto...</option>
                    </select>
                </div>
                <button class="btn-view" style="padding: 12px; border-radius: 8px; background: var(--hover-bg); border: 1px solid #4caf50; color: white;" onclick="addProductToCart()">➕ Añadir</button>
            </div>

            <br>
            <div class="form-group">
                <label class="form-label">Método de Pago *</label>
                <select id="posMetodoPago" class="form-select" style="border-color: #4caf50; font-weight: bold;">
                    <option value="Efectivo">💵 Efectivo</option>
                    <option value="Tarjeta">💳 Tarjeta de Crédito/Débito</option>
                    <option value="Transferencia">🏦 Transferencia Bancaria</option>
                </select>
            </div>

            <div class="pos-total-row" style="font-size: 14px; border-top: none; color: #aaa; margin-top: 10px;">
                <span>Subtotal (Servicios + Productos):</span>
                <span id="posSubtotal">$0.00</span>
            </div>
            
            <div class="pos-total-row" style="font-size: 14px; border-top: none; color: #4caf50; padding-top: 0;">
                <span>Abono Pagado Previamente:</span>
                <span id="posAbonoPrevio">-$0.00</span>
            </div>

            <div class="pos-total-row" style="border-top: 1px dashed #4caf50; padding-top: 10px;">
                <span>SALDO A COBRAR:</span>
                <span id="posTotalFinal">$0.00</span>
            </div>
            <div style="text-align: right; color:var(--text-muted); font-size: 12px; margin-top:5px;">
                Incluye IVA. El cliente ganará <b id="posPuntosGana" style="color:var(--accent-primary);">0</b> Puntos StyleNow.
            </div>

            <div class="form-actions" style="margin-top: 30px;">
                <button type="button" class="btn-cancel" onclick="closePosModal()">Cancelar</button>
                <button type="button" class="btn-save" style="background: #4caf50; color: white;" onclick="processCheckout()">🧾 Cobrar y Completar</button>
            </div>
        </div>
    </div>

    <div class="modal" id="citaModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Nueva Cita</h2>
                <span class="close-btn" onclick="closeModal()">×</span>
            </div>
            <form id="citaForm">
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Cliente <span>*</span></label><select id="cliente" name="cit_clienteId" class="form-select" required></select></div>
                    <div class="form-group"><label class="form-label">Teléfono</label><input type="text" id="telefonoCliente" class="form-input" readonly placeholder="Autocompletado..."></div>
                </div>

                <div class="form-row">
                    <div class="form-group"><label class="form-label">Sucursal <span>*</span></label><select id="sucursal" name="cit_sucursalId" class="form-select" required></select></div>
                    <div class="form-group"><label class="form-label">Empleado <span>*</span></label><select id="empleado" name="cit_empleadoId" class="form-select" required disabled><option value="">Seleccione Sucursal primero</option></select></div>
                </div>

                <div class="form-group">
                    <label class="form-label" style="color: var(--accent-primary);">✂️ Seleccionar Servicios <span>*</span></label>
                    <div id="serviciosContainer" class="services-container"><p style="color:var(--text-muted); text-align:center;">Seleccione un empleado.</p></div>
                </div>

                <div class="totals-summary">
                    <div class="total-item"><div class="total-label">⏱️ Tiempo</div><div class="total-value" id="totalTiempo">0 min</div></div>
                    <div class="total-item"><div class="total-label">💰 Subtotal</div><div class="total-value price" id="totalPrecio">$0.00</div></div>
                    <input type="hidden" id="precioBaseHidden" value="0">
                </div>

                <div class="form-row" style="margin-top: 20px;">
                    <div class="form-group">
                        <label class="form-label">🎁 Promoción</label>
                        <select id="promocion" name="cit_promocionId" class="form-select"><option value="">Ninguna promoción...</option></select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Precio Final ($)</label>
                        <input type="text" id="precioFinalCalculado" name="cit_precio" class="form-input" readonly style="color: #4caf50; font-weight: bold;">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Abono Inicial ($)</label>
                        <input type="number" id="abonoCita" name="cit_abono" class="form-input" step="0.01" min="0" value="0.00">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group"><label class="form-label">Fecha <span>*</span></label><input type="text" id="fechaCita" class="form-input flatpickr" required></div>
                    <div class="form-group"><label class="form-label">Hora <span>*</span></label><input type="text" id="horaCita" class="form-input flatpickr-time" required></div>
                </div>

                <div class="form-group">
                    <label class="form-label">Estado <span>*</span></label>
                    <select id="estadoCita" name="cit_estadoCita" class="form-select" required>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Confirmada">Confirmada</option>
                        <option value="En Progreso">En Progreso</option>
                        <option value="Por Cobrar">Por Cobrar (Caja)</option>
                        <option value="Completada">Completada</option>
                        <option value="Cancelada">Cancelada</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn-save">Guardar Agenda</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let allCitas = [], filteredCitas = [], catalogoProductos = [];
    let currentPage = 1, pageSize = 10, totalPages = 1;
    let editingCitaId = null;

    // Variables Caja POS
    let posCitaActiva = null;
    let posCarrito = [];
    let catalogoServiciosCaja = []; // Para tener los precios reales al cobrar

    document.addEventListener('DOMContentLoaded', () => {
        cargarCitas();
        cargarCatalogosIniciales();
        cargarProductosCaja(); 
        cargarServiciosCaja(); // Carga de catálogo global para la caja
        setupEventListeners();
        flatpickr(".flatpickr", { locale: "es", minDate: "today", dateFormat: "Y-m-d" });
        flatpickr(".flatpickr-time", { enableTime: true, noCalendar: true, dateFormat: "H:i", minTime: "09:00", maxTime: "19:00" });
    });

    async function cargarCitas() {
        try {
            document.getElementById('loadingIndicator').style.display = 'block';
            const res = await fetch('/admin/citas/data');
            if(!res.ok) throw new Error("Status: " + res.status);
            allCitas = await res.json();
            filteredCitas = [...allCitas];
            renderTable(); updateStats();
        } catch(e) { 
            document.getElementById('citasTable').innerHTML = `<tr><td colspan="6" style="text-align:center; color:#f44336; padding:20px;">⚠️ Error de conexión con la BD.</td></tr>`;
        } finally { document.getElementById('loadingIndicator').style.display = 'none'; }
    }

    async function cargarCatalogosIniciales() {
        const h = { 'X-CSRF-TOKEN': csrfToken };
        const fill = (u, id, fn) => fetch(u, {headers:h}).then(r=>r.json()).then(d => {
            const el = document.getElementById(id); el.innerHTML = '<option value="">Seleccionar...</option>';
            d.forEach(i => { let o = new Option(i.nombre, i.id); if(fn) fn(o, i); el.add(o); });
        });
        fill('/admin/citas/clientes', 'cliente', (o,i) => o.dataset.telefono = i.telefono);
        fill('/admin/citas/sucursales', 'sucursal');
        
        fetch('/admin/citas/promociones-activas', {headers: h}).then(r=>r.json()).then(data => {
            const sel = document.getElementById('promocion');
            data.forEach(p => {
                let o = new Option(`${p.nombre} (-${p.valor})`, p.id);
                o.dataset.tipo = p.tipo; o.dataset.valor = p.valor; sel.add(o);
            });
        });
    }

    async function cargarProductosCaja() {
        try {
            const res = await fetch('/admin/citas/productos');
            catalogoProductos = await res.json();
        } catch(e) { console.log("Error cargando productos para caja"); }
    }

    // ==========================================
    // LÓGICA DEL PUNTO DE VENTA (POS CAJA) ACTUALIZADA
    // ==========================================
    async function cargarServiciosCaja() {
        try {
            const res = await fetch('/admin/citas/servicios');
            catalogoServiciosCaja = await res.json();
            
            // Llenar select de promociones para el modal de Caja
            const resPromo = await fetch('/admin/citas/promociones-activas', {headers: {'X-CSRF-TOKEN': csrfToken}});
            const promos = await resPromo.json();
            const selPromo = document.getElementById('posPromocion');
            promos.forEach(p => {
                let o = new Option(`${p.nombre} (-${p.valor})`, p.id);
                o.dataset.tipo = p.tipo; o.dataset.valor = p.valor;
                selPromo.add(o);
            });
        } catch(e) { console.log("Error cargando servicios/promos para caja"); }
    }

    window.openCheckoutModal = function(citaStr) {
        posCitaActiva = JSON.parse(citaStr);
        document.getElementById('posCliente').innerText = posCitaActiva.cliente_nombre;
        document.getElementById('posPromocion').value = posCitaActiva.cit_promocionId || '';
        
        // 1. Llenar Select de Productos
        const selProd = document.getElementById('posAddProduct');
        selProd.innerHTML = '<option value="">Buscar producto...</option>';
        catalogoProductos.filter(p => p.sucursal_id == posCitaActiva.cit_sucursalId)
                         .forEach(p => selProd.add(new Option(`📦 ${p.nombre} ($${parseFloat(p.precio).toFixed(2)}) - Stock: ${p.stock}`, p.id)));

        // 2. Generar Checklist de Servicios Agendados
        const checklistCont = document.getElementById('posServiciosChecklist');
        checklistCont.innerHTML = '';
        let serviciosIds = posCitaActiva.cit_servicios_ids ? posCitaActiva.cit_servicios_ids.split(',') : [posCitaActiva.cit_servicioId.toString()];
        
        serviciosIds.forEach(sId => {
            let srvInfo = catalogoServiciosCaja.find(s => s.id == sId);
            if(srvInfo) {
                checklistCont.innerHTML += `
                    <div class="service-checkbox-item" style="border-bottom: 1px dashed #333; padding: 10px;">
                        <label class="service-checkbox-label" style="width: 100%; display: flex; justify-content: space-between;">
                            <div>
                                <input type="checkbox" class="pos-service-cb" value="${srvInfo.id}" data-precio="${srvInfo.precio}" checked onchange="calcularTotalPos()">
                                <span style="margin-left: 8px;">✂️ ${srvInfo.nombre}</span>
                            </div>
                            <strong style="color: #4caf50;">$${parseFloat(srvInfo.precio).toFixed(2)}</strong>
                        </label>
                    </div>
                `;
            }
        });

        posCarrito = []; // Vaciamos el carrito de productos
        calcularTotalPos(); // Calculamos el total inicial
        document.getElementById('posModal').classList.add('active');
    }

    window.closePosModal = function() {
        document.getElementById('posModal').classList.remove('active');
        posCitaActiva = null; posCarrito = [];
    }

    // Calcular el total leyendo los checkboxes y el carrito de productos
    window.calcularTotalPos = function() {
        // 1. Sumar servicios marcados
        let subtotalServicios = 0;
        document.querySelectorAll('.pos-service-cb:checked').forEach(cb => {
            subtotalServicios += parseFloat(cb.dataset.precio);
        });

        // 2. Aplicar Promociones solo a los servicios
        let totalServiciosDescuento = subtotalServicios;
        const selPromo = document.getElementById('posPromocion');
        if (selPromo.selectedIndex > 0 && subtotalServicios > 0) {
            const opt = selPromo.options[selPromo.selectedIndex];
            const tipoPromo = opt.dataset.tipo.toLowerCase(); 
            const valPromo = parseFloat(opt.dataset.valor) || 0;
            
            if (tipoPromo === 'porcentaje') totalServiciosDescuento -= (subtotalServicios * (valPromo / 100));
            else if (tipoPromo === 'fijo ($)' || tipoPromo === 'temporada') totalServiciosDescuento -= valPromo;
            else if (tipoPromo === '2x1') totalServiciosDescuento /= 2;
        }
        totalServiciosDescuento = Math.max(0, totalServiciosDescuento);

        // 3. Sumar productos del carrito y renderizarlos
        let totalProductos = 0;
        const cartCont = document.getElementById('posCartContainer');
        cartCont.innerHTML = posCarrito.length > 0 ? '<h6 style="color:#aaa; border-bottom:1px solid #333; padding-bottom:5px;">Productos Extra:</h6>' : '';
        
        posCarrito.forEach((item, index) => {
            let subtProd = item.precio * item.cantidad;
            totalProductos += subtProd;
            cartCont.innerHTML += `
                <div class="pos-item">
                    <div style="flex:1;"><b>${item.nombre}</b> <br><small style="color:#aaa;">${item.cantidad} x $${item.precio.toFixed(2)}</small></div>
                    <div style="font-weight:bold; width: 60px; text-align:right;">$${subtProd.toFixed(2)}</div>
                    <button type="button" onclick="removeFromCart(${index})" style="background:none;border:none;color:#f44336;cursor:pointer;margin-left:10px;">✖</button>
                </div>
            `;
        });

        let subtotalGeneral = totalServiciosDescuento + totalProductos;
        
        // Obtener el abono que ya pagó el cliente
        let abonoPagado = parseFloat(posCitaActiva.cit_abono || 0);

        // El total a pagar ahora es el subtotal menos el abono
        let totalFinal = subtotalGeneral - abonoPagado;
        if (totalFinal < 0) totalFinal = 0; 

        document.getElementById('posSubtotal').innerText = `$${subtotalGeneral.toFixed(2)}`;
        document.getElementById('posAbonoPrevio').innerText = `-$${abonoPagado.toFixed(2)}`;
        document.getElementById('posTotalFinal').innerText = `$${totalFinal.toFixed(2)}`;
        document.getElementById('posPuntosGana').innerText = Math.floor(subtotalGeneral); // Puntos sobre el total, no solo el saldo
    }

    window.addProductToCart = function() {
        const prodId = document.getElementById('posAddProduct').value;
        if(!prodId) return;
        
        const producto = catalogoProductos.find(p => p.id == prodId);
        const existe = posCarrito.find(item => item.id == prodId);
        
        if(existe) {
            if(existe.cantidad < producto.stock) existe.cantidad++;
            else { Swal.fire('Stock Insuficiente', 'No hay más unidades en inventario', 'warning'); return; }
        } else {
            posCarrito.push({ id: producto.id, nombre: `📦 ${producto.nombre}`, precio: parseFloat(producto.precio || 0), cantidad: 1 });
        }
        
        document.getElementById('posAddProduct').value = ""; 
        calcularTotalPos();
    }

    window.removeFromCart = function(index) {
        posCarrito.splice(index, 1);
        calcularTotalPos();
    }

    window.processCheckout = async function() {
        // Extraer IDs de servicios que quedaron checkeados
        let serviciosCobrar = [];
        document.querySelectorAll('.pos-service-cb:checked').forEach(cb => serviciosCobrar.push(cb.value));

        // Formatear array de productos
        const productosCobrar = posCarrito.map(p => {
            return { id: p.id, nombre: p.nombre, cantidad: p.cantidad, subtotal: p.precio * p.cantidad }
        });

        if(serviciosCobrar.length === 0 && productosCobrar.length === 0) {
            Swal.fire('Atención', 'Debe cobrar al menos un servicio o producto.', 'warning'); return;
        }

        const data = {
            metodo_pago: document.getElementById('posMetodoPago').value,
            promocion_id: document.getElementById('posPromocion').value,
            servicios: serviciosCobrar,
            productos: productosCobrar
        };

        try {
            Swal.fire({title: 'Procesando Pago...', allowOutsideClick: false, didOpen: () => Swal.showLoading(), background: '#151515', color: '#fff'});
            
            const res = await fetch(`/admin/citas/${posCitaActiva.cit_id}/checkout`, {
                method: 'POST',
                headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':csrfToken },
                body: JSON.stringify(data)
            });
            const responseData = await res.json();

            if(res.ok && responseData.success) {
                closePosModal(); 
                cargarCitas(); 
                cargarProductosCaja();
                
                Swal.fire({ 
                    icon: 'success', 
                    title: '¡Cobro Exitoso!', 
                    html: `Factura: <b>${responseData.factura}</b><br>El cliente ganó ${responseData.puntos} puntos.<br><br><span style="color:#aaa; font-size:0.9rem;">Se ha enviado una copia a su correo electrónico.</span>`, 
                    confirmButtonText: '🖨️ Imprimir Factura',
                    showCancelButton: true,
                    cancelButtonText: 'Cerrar',
                    confirmButtonColor: '#4caf50', 
                    cancelButtonColor: '#333',
                    background: '#1a1a1a', 
                    color: '#ffffff' 
                }).then((result) => {
                    if (result.isConfirmed && responseData.factura_url) {
                        window.open(responseData.factura_url, '_blank');
                    }
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Error en Caja', text: responseData.message, background: '#1a1a1a', color: '#ffffff' });
            }
        } catch(e) { 
            Swal.fire({ icon: 'error', title: 'Falla del Servidor', text: 'Verifica tu conexión a la base de datos.', background: '#1a1a1a', color: '#ffffff'}); 
        }
    }

    // ==========================================
    // LÓGICA DE CITAS (CREAR / EDITAR)
    // ==========================================
    async function cargarDependenciasPorSucursal(sucId, empId = null) {
        const empSel = document.getElementById('empleado'); const srvCont = document.getElementById('serviciosContainer');
        if(!sucId) { empSel.disabled=true; srvCont.innerHTML='<p style="color:var(--text-muted);text-align:center;">Seleccione un empleado.</p>'; return; }
        empSel.disabled = false;
        const res = await fetch(`/admin/citas/empleados?sucursal_id=${sucId}`, {headers: {'X-CSRF-TOKEN': csrfToken}});
        const empleados = await res.json();
        empSel.innerHTML = '<option value="">Seleccionar Empleado...</option>';
        empleados.forEach(i => empSel.add(new Option(i.nombre, i.id)));
        if(empId) empSel.value = empId;
    }

    async function cargarServiciosPorEmpleado(empId, srvPre = []) {
        const srvCont = document.getElementById('serviciosContainer');
        if(!empId) return;
        srvCont.innerHTML = '<p style="text-align:center;color:var(--accent-primary);">Cargando especialidades...</p>';
        const res = await fetch(`/admin/citas/servicios?empleado_id=${empId}`, {headers: {'X-CSRF-TOKEN': csrfToken}});
        const servicios = await res.json();
        
        let html = '';
        servicios.forEach(srv => {
            const isChecked = srvPre.includes(srv.id.toString()) ? 'checked' : '';
            html += `<div class="service-checkbox-item"><label class="service-checkbox-label"><input type="checkbox" name="servicios[]" value="${srv.id}" data-precio="${srv.precio}" data-duracion="${srv.duracion}" class="service-cb" onchange="calcularTotales()" ${isChecked}><span>${srv.nombre}</span></label><div class="service-meta">⏱️ ${srv.duracion}m | 💰 $${parseFloat(srv.precio).toFixed(2)}</div></div>`;
        });
        srvCont.innerHTML = html || '<p style="color:#f44336;text-align:center;">Empleado sin servicios.</p>';
        calcularTotales();
    }

    window.calcularTotales = function() {
        let t = 0, p = 0;
        document.querySelectorAll('.service-cb:checked').forEach(cb => { t += parseInt(cb.dataset.duracion)||0; p += parseFloat(cb.dataset.precio)||0; });
        document.getElementById('totalTiempo').textContent = `${t} min`; document.getElementById('totalPrecio').textContent = `$${p.toFixed(2)}`;
        document.getElementById('precioBaseHidden').value = p; calcularPrecioConPromocion();
    }

    function calcularPrecioConPromocion() {
        let pBase = parseFloat(document.getElementById('precioBaseHidden').value) || 0;
        const sel = document.getElementById('promocion'); let pFinal = pBase;
        if (sel.selectedIndex > 0 && pBase > 0) {
            const opt = sel.options[sel.selectedIndex]; const tipo = opt.dataset.tipo; const val = parseFloat(opt.dataset.valor) || 0;
            if (tipo === 'Porcentaje' || tipo === 'porcentaje') pFinal -= (pBase * (val / 100));
            else if (tipo === 'Fijo ($)' || tipo === 'Temporada') pFinal -= val;
            else if (tipo === '2x1') pFinal /= 2;
        }
        document.getElementById('precioFinalCalculado').value = (pFinal < 0 ? 0 : pFinal).toFixed(2);
    }

    function setupEventListeners() {
        document.getElementById('cliente').addEventListener('change', function() { this.options[this.selectedIndex].dataset.telefono ? document.getElementById('telefonoCliente').value = this.options[this.selectedIndex].dataset.telefono : null; });
        document.getElementById('sucursal').addEventListener('change', function() { cargarDependenciasPorSucursal(this.value); });
        document.getElementById('empleado').addEventListener('change', function() { cargarServiciosPorEmpleado(this.value); });
        document.getElementById('promocion').addEventListener('change', calcularPrecioConPromocion);
        document.getElementById('citaForm').addEventListener('submit', saveCita);
        document.getElementById('searchInput').addEventListener('input', debounce(applyFilters, 300));
        document.getElementById('filterEstado').addEventListener('change', applyFilters);
    }

    function renderTable() {
        const tbody = document.getElementById('citasTable');
        const start = (currentPage - 1) * pageSize; const data = filteredCitas.slice(start, start + pageSize);
        tbody.innerHTML = '';
        if(data.length === 0) { tbody.innerHTML='<tr><td colspan="6" style="text-align:center; padding:20px; color:var(--text-muted);">No hay citas registradas.</td></tr>'; return; }

        data.forEach(c => {
            const fechaCompleta = c.cit_fechaCita || 'Sin_Fecha 00:00:00'; const partes = fechaCompleta.split(' ');
            const estado = c.cit_estadoCita || 'Pendiente'; 
            const estadoClase = estado.replace(/\s+/g, ''); 
            const dTotal = c.cit_duracionTotal ? `${c.cit_duracionTotal}m` : '30m';
            const precioDisplay = c.cit_precio ? ` | 💰 $${parseFloat(c.cit_precio).toFixed(2)}` : '';

            let botonCobrar = '';
            if (estado === 'Confirmada' || estado === 'Por Cobrar' || estado === 'PorCobrar') {
                const citaJson = JSON.stringify(c).replace(/'/g, "&#39;");
                botonCobrar = `<button class="btn-checkout" onclick='openCheckoutModal(\`${citaJson}\`)' title="Cobrar Cita en Caja">💰 Cobrar</button>`;
            }

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>#${c.cit_id}</td>
                <td><strong>${c.cliente_nombre || 'Desconocido'}</strong><br><small style="color:var(--accent-primary)">${c.servicio_nombre} (⏱️ ${dTotal}${precioDisplay})</small></td>
                <td>${partes[0]} ${partes[1] ? partes[1].substring(0,5) : ''}</td><td>${c.empleado_nombre || 'N/A'}</td>
                <td><span class="badge badge-${estadoClase}">${estado}</span></td>
                <td>
                    <button class="action-btn" style="color:#2196f3; display:inline;" onclick='editCita(${JSON.stringify(c)})' title="Editar">✏️</button>
                    <button class="action-btn" style="color:#f44336; display:inline;" onclick="deleteCita(${c.cit_id})" title="Eliminar">🗑️</button>
                    ${botonCobrar}
                </td>
            `;
            tbody.appendChild(tr);
        });
        document.getElementById('resultsInfo').innerText = `Mostrando ${data.length} de ${filteredCitas.length}`; updatePagination();
    }

    function openModal() { document.getElementById('citaForm').reset(); editingCitaId = null; document.getElementById('empleado').disabled = true; document.getElementById('serviciosContainer').innerHTML = '<p style="color:var(--text-muted); text-align:center;">Seleccione un empleado.</p>'; document.getElementById('modalTitle').innerText='Nueva Cita'; document.getElementById('citaModal').classList.add('active'); }
    function closeModal() { document.getElementById('citaModal').classList.remove('active'); }
    
    window.editCita = async function(c) {
        editingCitaId = c.cit_id; document.getElementById('cliente').value = c.cit_clienteId; document.getElementById('cliente').dispatchEvent(new Event('change'));
        document.getElementById('estadoCita').value = c.cit_estadoCita || 'Pendiente';
        const partes = (c.cit_fechaCita || '').split(' '); document.getElementById('fechaCita').value = partes[0] || ''; document.getElementById('horaCita').value = partes[1] ? partes[1].substring(0,5) : '';
        document.getElementById('promocion').value = c.cit_promocionId || '';
        document.getElementById('abonoCita').value = c.cit_abono || '0.00';
        document.getElementById('sucursal').value = c.cit_sucursalId;
        await cargarDependenciasPorSucursal(c.cit_sucursalId, c.cit_empleadoId);
        let srvSeleccionados = c.cit_servicios_ids ? c.cit_servicios_ids.split(',') : [c.cit_servicioId.toString()];
        await cargarServiciosPorEmpleado(c.cit_empleadoId, srvSeleccionados);
        document.getElementById('modalTitle').innerText='Editar Cita'; document.getElementById('citaModal').classList.add('active');
    }

    async function saveCita(e) {
        e.preventDefault();
        if(document.querySelectorAll('.service-cb:checked').length === 0) { Swal.fire('Falta Servicio', 'Selecciona un servicio.', 'warning'); return; }
        const fd = new FormData(e.target); fd.append('cit_fechaCita', `${document.getElementById('fechaCita').value} ${document.getElementById('horaCita').value}:00`);
        const data = {}; fd.forEach((v, k) => { if (k === 'servicios[]') { if (!data['servicios']) data['servicios'] = []; data['servicios'].push(v); } else data[k] = v; });
        try {
            const res = await fetch(editingCitaId ? `/admin/citas/${editingCitaId}` : '/admin/citas', { method: editingCitaId ? 'PUT' : 'POST', headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':csrfToken }, body: JSON.stringify(data) });
            const resp = await res.json();
            if(res.ok && resp.success) { closeModal(); cargarCitas(); Swal.fire('¡Éxito!', 'Cita guardada', 'success'); } 
            else Swal.fire('Aviso', resp.message || 'Error', 'warning');
        } catch (error) { Swal.fire('Error', 'Falla del Servidor.', 'error'); }
    }

    async function deleteCita(id) {
        if(await Swal.fire({ title: '¿Eliminar cita?', icon: 'warning', showCancelButton: true }).then(r=>r.isConfirmed)){
            try { await fetch(`/admin/citas/${id}`, {method:'DELETE', headers:{'X-CSRF-TOKEN':csrfToken}}); cargarCitas(); } catch(e) {}
        }
    }
    
    async function ejecutarMarketing() {
        Swal.fire({
            title: 'Analizando Base de Datos...',
            text: 'El algoritmo está buscando clientes que necesiten un retoque hoy. Esto puede tomar unos segundos.',
            allowOutsideClick: false,
            background: '#1a1a1a', color: '#fff',
            didOpen: () => Swal.showLoading()
        });

        try {
            const res = await fetch('/admin/citas/ejecutar-marketing', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            });
            const data = await res.json();
            
            if(data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Marketing Ejecutado',
                    text: data.message,
                    background: '#1a1a1a', color: '#fff', confirmButtonColor: '#9c27b0'
                });
            }
        } catch(e) {
            Swal.fire('Error', 'Falla en el servidor.', 'error');
        }
    }

    function debounce(f, w) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => f(...a), w); }; }
    function applyFilters() { 
        const t = document.getElementById('searchInput').value.toLowerCase(), e = document.getElementById('filterEstado').value;
        filteredCitas = allCitas.filter(c => (c.cliente_nombre||'').toLowerCase().includes(t) && (e === '' || c.cit_estadoCita === e)); renderTable(); 
    }
    function updateStats() { 
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('citasHoy').innerText = allCitas.filter(c => c.cit_fechaCita && c.cit_fechaCita.startsWith(today)).length;
        document.getElementById('citasConfirmadas').innerText = allCitas.filter(c => c.cit_estadoCita === 'Confirmada' || c.cit_estadoCita === 'Por Cobrar').length;
    }
    function updatePagination() { totalPages = Math.ceil(filteredCitas.length / pageSize) || 1; document.getElementById('pageNumbers').innerText = `Página ${currentPage} de ${totalPages}`; document.getElementById('prevBtn').disabled = currentPage === 1; document.getElementById('nextBtn').disabled = currentPage === totalPages; }
    function previousPage() { if(currentPage>1){currentPage--; renderTable();} } function nextPage() { if(currentPage<totalPages){currentPage++; renderTable();} }
</script>
@endsection