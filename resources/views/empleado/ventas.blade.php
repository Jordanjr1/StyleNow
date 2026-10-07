@extends('layouts.app')

@section('title', 'Venta de Productos - StyleNow')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">

<style>
    /* Estilos del sidebar-nav (Compartidos) */
    .sidebar-nav { padding: 2rem 0; }
    .nav-section { margin-bottom: 1.5rem; }
    .nav-section-title { padding: 0 1.5rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 700; }
    .nav-item { display: flex; align-items: center; gap: 1rem; padding: 0.9rem 1.5rem; color: var(--text-primary); text-decoration: none; transition: all 0.3s ease; cursor: pointer; position: relative; }
    .nav-item:hover { background: rgba(250, 211, 112, 0.1); border-left: 3px solid var(--dorado); padding-left: calc(1.5rem - 3px); }
    .nav-item.active { background: rgba(250, 211, 112, 0.15); border-left: 3px solid var(--dorado); padding-left: calc(1.5rem - 3px); color: var(--dorado); }
    .nav-icon { font-size: 1.2rem; width: 24px; text-align: center; }
    .nav-text { flex: 1; font-size: 0.95rem; font-weight: 500; }

    /* CONTENEDOR PRINCIPAL */
    .empleado-container {
        --dorado: #fad370; --bg-card: #1a1a1a; --bg-secondary: #121212;
        --text-primary: #ffffff; --text-secondary: #a0a0a0; --border-color: #2a2a2a;
        --hover-bg: #252525;
        font-family: 'Poppins', sans-serif; max-width: 1200px; margin: 0 auto;
    }

    /* GRID DE ESTADÍSTICAS */
    .ventas-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 30px; }
    .stat-card { background: linear-gradient(145deg, #1e1e1e, #121212); padding: 25px; border-radius: 16px; border: 1px solid var(--border-color); display: flex; flex-direction: column; position: relative; overflow: hidden;}
    .stat-card::after { content: ''; position: absolute; right: -20px; bottom: -20px; width: 100px; height: 100px; background: radial-gradient(circle, rgba(250,211,112,0.1) 0%, transparent 70%); border-radius: 50%; pointer-events: none;}
    .stat-title { font-size: 13px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-bottom: 5px; }
    .stat-value { font-size: 2.2rem; font-weight: 800; color: #fff; }
    .stat-value.gold { color: var(--dorado); }
    .stat-value.green { color: #4ade80; }

    /* LAYOUT PRINCIPAL: FORMULARIO + HISTORIAL */
    .ventas-layout { display: grid; grid-template-columns: 350px 1fr; gap: 25px; align-items: start; }

    /* FORMULARIO DE VENTA */
    .pos-card { background: var(--bg-card); border-radius: 16px; border: 1px solid var(--border-color); padding: 25px; position: sticky; top: 20px;}
    .pos-header { font-size: 1.2rem; font-weight: bold; color: var(--dorado); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; border-bottom: 1px dashed var(--border-color); padding-bottom: 15px;}
    
    .form-group { margin-bottom: 15px; }
    .form-label { display: block; font-size: 12px; color: var(--text-secondary); margin-bottom: 8px; text-transform: uppercase; font-weight: 600;}
    .form-select, .form-input { width: 100%; padding: 12px 15px; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 10px; color: white; font-family: 'Poppins'; outline: none; transition: 0.3s; }
    .form-select:focus, .form-input:focus { border-color: var(--dorado); }
    
    .checkout-box { background: rgba(74, 222, 128, 0.05); border: 1px solid rgba(74, 222, 128, 0.3); border-radius: 12px; padding: 20px; margin-top: 25px; text-align: center; }
    .checkout-label { font-size: 13px; color: var(--text-secondary); margin-bottom: 5px; }
    .checkout-total { font-size: 2.5rem; font-weight: bold; color: #4ade80; margin-bottom: 15px; line-height: 1;}
    .checkout-comision { font-size: 13px; color: var(--dorado); font-weight: 600; display: flex; justify-content: center; align-items: center; gap: 5px;}
    
    .btn-sell { width: 100%; padding: 14px; background: #4ade80; color: #000; font-weight: bold; border: none; border-radius: 10px; cursor: pointer; transition: 0.3s; font-size: 15px; margin-top: 15px; }
    .btn-sell:hover { transform: translateY(-2px); box-shadow: 0 8px 15px rgba(74, 222, 128, 0.3); }
    .btn-sell:disabled { background: #333; color: #666; cursor: not-allowed; box-shadow: none; transform: none;}

    /* TABLA DE HISTORIAL Y FILTROS */
    .history-card { background: var(--bg-card); border-radius: 16px; border: 1px solid var(--border-color); overflow: hidden; }
    .history-header { padding: 20px 25px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;}
    .history-title { font-size: 1.1rem; font-weight: bold; color: white; display: flex; align-items: center; gap: 8px;}
    
    .filter-group { display: flex; gap: 10px; }
    .filter-input { padding: 8px 12px; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 8px; color: white; font-size: 12px; outline: none; transition: 0.3s;}
    .filter-input:focus { border-color: var(--dorado); }

    .tabla-head { display: grid; grid-template-columns: 1.5fr 100px 100px 100px 60px; padding: 15px 25px; background: var(--bg-secondary); font-weight: 600; font-size: 12px; color: var(--text-secondary); text-transform: uppercase; border-bottom: 2px solid var(--border-color); }
    .tabla-row { display: grid; grid-template-columns: 1.5fr 100px 100px 100px 60px; padding: 18px 25px; border-bottom: 1px solid var(--border-color); align-items: center; transition: 0.3s;}
    .tabla-row:hover { background: var(--hover-bg); border-left: 2px solid var(--dorado); padding-left: 23px;}
    .tabla-row:last-child { border-bottom: none; }
    
    .t-prod { font-weight: 600; color: white; display: flex; flex-direction: column; gap: 3px; font-size: 14px;}
    .t-client { font-size: 12px; color: #888; font-weight: normal; margin-top: 2px; }
    .t-date { font-size: 12px; color: var(--text-secondary); }
    .t-total { font-weight: bold; color: #4ade80; font-size: 14px;}
    .t-com { font-weight: bold; color: var(--dorado); font-size: 14px;}
    
    .btn-icon { background: rgba(250, 211, 112, 0.1); color: var(--dorado); border: 1px solid rgba(250, 211, 112, 0.3); border-radius: 8px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s; font-size: 16px;}
    .btn-icon:hover { background: var(--dorado); color: #000; }

    /* MODAL TIPO RECIBO (TICKET) */
    .emp-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.85); z-index: 1000; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(8px); opacity: 0; transition: opacity 0.3s ease;}
    .emp-modal.active { display: flex; opacity: 1; }
    .emp-modal-content { background: var(--bg-card); border-radius: 16px; width: 100%; max-width: 400px; border: 1px solid var(--border-color); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.7); transform: translateY(20px); transition: transform 0.3s; overflow: hidden;}
    .emp-modal.active .emp-modal-content { transform: translateY(0); }
    
    .receipt-box { padding: 30px; background: #fff; color: #000; font-family: 'Courier New', Courier, monospace; position: relative; }
    .receipt-box::before { content: ''; position: absolute; top: -5px; left: 0; width: 100%; height: 10px; background: radial-gradient(circle, transparent 4px, var(--bg-card) 5px) repeat-x; background-size: 12px 10px; }
    .receipt-header { text-align: center; border-bottom: 2px dashed #ccc; padding-bottom: 15px; margin-bottom: 15px; }
    .receipt-logo { font-size: 24px; font-weight: bold; font-family: 'Abril Fatface', cursive; margin-bottom: 5px; }
    .receipt-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; font-weight: bold; }
    .receipt-total { border-top: 2px dashed #ccc; padding-top: 15px; margin-top: 15px; font-size: 18px; font-weight: bold; display: flex; justify-content: space-between; }
    
    .modal-footer { padding: 15px 20px; background: var(--bg-secondary); display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color);}

    @media (max-width: 900px) {
        .ventas-layout { grid-template-columns: 1fr; }
        .pos-card { position: relative; top: 0; }
        .ventas-stats { grid-template-columns: 1fr; }
        .tabla-head { display: none; }
        .tabla-row { grid-template-columns: 1fr 1fr; gap: 10px; padding: 15px; border-left: 3px solid var(--dorado); background: var(--bg-secondary); margin-bottom: 10px; border-radius: 10px;}
        .t-date, .t-total, .t-com { text-align: left; }
        .btn-icon { width: 100%; margin-top: 10px; }
    }
</style>
@endpush

@section('sidebar-empleado')
<nav class="sidebar-nav">
    <div class="nav-section">
        <p class="nav-section-title">Principal</p>
        <a href="{{ route('empleado.dashboard') }}" class="nav-item"><span class="nav-icon">🏠</span><span class="nav-text">Mi Panel</span></a>
    </div>
    <div class="nav-section">
        <p class="nav-section-title">Citas</p>
        <a href="{{ url('/empleado/Citas_pendients') }}" class="nav-item"><span class="nav-icon">📋</span><span class="nav-text">Citas Pendientes</span></a>
        <a href="{{ url('/empleado/Citas_atendidas') }}" class="nav-item"><span class="nav-icon">✅</span><span class="nav-text">Citas Atendidas</span></a>
        <a href="{{ route('empleado.historial') }}" class="nav-item"><span class="nav-icon">📖</span><span class="nav-text">Historial Completo</span></a>
    </div>
    <div class="nav-section">
        <p class="nav-section-title">Desempeño</p>
        <a href="{{ route('empleado.comisiones') }}" class="nav-item"><span class="nav-icon">💰</span><span class="nav-text">Mis Comisiones</span></a>
        <a href="{{ route('empleado.calificaciones') }}" class="nav-item"><span class="nav-icon">⭐</span><span class="nav-text">Calificaciones</span></a>
    </div>
    <div class="nav-section">
        <p class="nav-section-title">Inventario</p>
        <a href="{{ route('empleado.reportar.falta') }}" class="nav-item"><span class="nav-icon">📦</span><span class="nav-text">Reportar Falta</span></a>
        <a href="{{ route('empleado.ventas') }}" class="nav-item active"><span class="nav-icon">💵</span><span class="nav-text">Ventas</span></a>
    </div>
    <div class="nav-section">
        <p class="nav-section-title">Cuenta</p>
        <a href="/empleado/perfil" class="nav-item"><span class="nav-icon">👤</span><span class="nav-text">Mi Perfil</span></a>
    </div>
</nav>
@endsection

@section('topbar-left')
    <h1 style="font-family: 'Abril Fatface', cursive; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.3rem;">Retail / Ventas</h1>
    <p style="color: var(--text-secondary); font-size: 0.95rem;">Punto de venta y control de comisiones por retail</p>
@endsection

@section('content')
<div class="empleado-container">

    <div class="ventas-stats">
        <div class="stat-card">
            <div class="stat-title">Productos Vendidos (Mes)</div>
            <div class="stat-value">{{ $estadisticas['total_items_mes'] }} <span style="font-size: 14px; color: #888; font-weight: normal;">uds</span></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Ingreso Generado</div>
            <div class="stat-value green">${{ number_format($estadisticas['total_dinero_mes'], 2) }}</div>
        </div>
        <div class="stat-card" style="border-color: var(--dorado);">
            <div class="stat-title" style="color: var(--dorado);">Mi Comisión Extra</div>
            <div class="stat-value gold">+ ${{ number_format($estadisticas['total_comision_mes'], 2) }}</div>
        </div>
    </div>

    <div class="ventas-layout">
        
        <div class="pos-card">
            <div class="pos-header"><span>🛒</span> Punto de Venta</div>
            <form id="formVenta">
                <div class="form-group">
                    <label class="form-label">Cliente (Opcional)</label>
                    <select id="clienteSelect" class="form-select">
                        <option value="">👤 Público General / De paso</option>
                        @foreach($clientes as $cli)
                            <option value="{{ $cli->cli_id }}">{{ $cli->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Producto a vender</label>
                    <select id="productoSelect" class="form-select" required onchange="calcularTotal()">
                        <option value="" data-precio="0" data-stock="0">-- Escanear o buscar --</option>
                        @foreach($productos as $prod)
                            <option value="{{ $prod->prd_id }}" data-precio="{{ $prod->prd_precioVenta }}" data-stock="{{ $prod->prd_stockActual }}">
                                {{ $prod->prd_nombre }} - ${{ number_format($prod->prd_precioVenta, 2) }} (Stock: {{ $prod->prd_stockActual }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Cantidad</label>
                    <input type="number" id="cantidadInput" class="form-input" min="1" value="1" required oninput="calcularTotal()">
                    <div id="stockWarning" style="color: #ef4444; font-size: 11px; margin-top: 5px; display: none;">Supera el stock actual en vitrina</div>
                </div>

                <div class="checkout-box">
                    <div class="checkout-label">Total a cobrar:</div>
                    <div class="checkout-total" id="displayTotal">$0.00</div>
                    <div class="checkout-comision"><span>💰</span> Comisión base: <span id="displayComision">+$0.00</span></div>
                </div>

                <button type="submit" class="btn-sell" id="btnSubmit" disabled>💵 Registrar Pago</button>
            </form>
        </div>

        <div class="history-card">
            <div class="history-header">
                <div class="history-title"><span>📋</span> Reporte de Ventas</div>
                <div class="filter-group">
                    <input type="text" id="filtroTexto" class="filter-input" placeholder="🔍 Buscar cliente o prod..." style="width: 180px;">
                    <input type="text" id="filtroFecha" class="filter-input" placeholder="📅 Elegir fecha" style="width: 130px; cursor: pointer;">
                </div>
            </div>
            
            <div class="tabla-head">
                <div>Detalle</div>
                <div>Fecha</div>
                <div>Cobrado</div>
                <div>Comisión</div>
                <div style="text-align:center;">Ticket</div>
            </div>
            
            <div id="historialContenedor" style="max-height: 500px; overflow-y: auto;">
                @forelse($historialVentas as $venta)
                    @php
                        $fechaData = \Carbon\Carbon::parse($venta->ven_fecha)->format('Y-m-d');
                        $fechaVisual = \Carbon\Carbon::parse($venta->ven_fecha)->format('d/m/Y');
                        $horaVisual = \Carbon\Carbon::parse($venta->ven_fecha)->format('H:i');
                        $searchData = strtolower($venta->prd_nombre . ' ' . ($venta->cliente_nombre ?? 'público general'));
                        
                        // Preparar datos para el modal del ticket
                        $ticketData = json_encode([
                            'id' => $venta->ven_id,
                            'fecha' => $fechaVisual . ' ' . $horaVisual,
                            'cliente' => $venta->cliente_nombre ?? 'Público General',
                            'producto' => $venta->prd_nombre,
                            'cantidad' => $venta->ven_cantidad,
                            'precio_u' => $venta->ven_precioUnitario,
                            'total' => $venta->ven_total,
                            'comision' => $venta->ven_comisionEmpleado
                        ]);
                    @endphp
                    
                    <div class="tabla-row" data-fecha="{{ $fechaData }}" data-search="{{ $searchData }}">
                        <div class="t-prod">
                            {{ Str::limit($venta->prd_nombre, 30) }}
                            <span class="t-client">👤 {{ $venta->cliente_nombre ?? 'Público General' }}</span>
                        </div>
                        <div class="t-date">
                            {{ $fechaVisual }}<br><span style="font-size:10px;">{{ $horaVisual }}</span>
                        </div>
                        <div class="t-total">${{ number_format($venta->ven_total, 2) }}</div>
                        <div class="t-com">+ ${{ number_format($venta->ven_comisionEmpleado, 2) }}</div>
                        <div style="display:flex; justify-content:center;">
<button class="btn-icon" data-ticket="{{ json_encode($ticketData) }}" onclick="abrirTicket(JSON.parse(this.dataset.ticket))" title="Ver Recibo">👁️</button>                        </div>
                    </div>
                @empty
                    <div class="tabla-row" style="grid-template-columns: 1fr; text-align:center; padding: 40px;">
                        <span style="color: var(--text-muted);">No has registrado ventas.</span>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

<div class="emp-modal" id="modalTicket">
    <div class="emp-modal-content">
        <div class="receipt-box" id="printArea">
            <div class="receipt-header">
                <div class="receipt-logo">StyleNow</div>
                <div>Comprobante de Venta Retail</div>
                <div id="tk-fecha" style="font-size: 12px; color: #666; margin-top: 5px;">--</div>
            </div>
            
            <div style="margin-bottom: 20px; font-size: 13px;">
                <div><b>Ticket #:</b> <span id="tk-id"></span></div>
                <div><b>Atendido por:</b> {{ Auth::user()->usr_nombre }}</div>
                <div><b>Cliente:</b> <span id="tk-cliente"></span></div>
            </div>
            
            <div class="receipt-row" style="color: #666; border-bottom: 1px solid #ddd; padding-bottom: 5px;">
                <span>CANT / DESCRIPCIÓN</span>
                <span>IMPORTE</span>
            </div>
            
            <div class="receipt-row" style="margin-top: 10px;">
                <span id="tk-desc"></span>
                <span id="tk-total"></span>
            </div>
            <div style="font-size: 12px; color: #888; margin-bottom: 10px;">
                (Precio Unitario: <span id="tk-precio-u"></span>)
            </div>
            
            <div class="receipt-total">
                <span>TOTAL VENTA</span>
                <span id="tk-gran-total"></span>
            </div>
            
            <div style="text-align: center; font-size: 11px; color: #888; margin-top: 20px;">
                ¡Gracias por su compra!<br>Los productos cosméticos no tienen cambio.
            </div>
        </div>
        
        <div class="modal-footer">
            <div style="color: var(--dorado); font-weight: bold; font-size: 12px;">Comisión generada: <span id="tk-comision"></span></div>
            <div style="display: flex; gap: 10px;">
                <button onclick="window.print()" style="background:none; border:1px solid #444; color:#fff; padding:6px 12px; border-radius:6px; cursor:pointer;">🖨️ Imprimir</button>
                <button onclick="cerrarTicket()" style="background:var(--hover-bg); border:none; color:#fff; padding:6px 12px; border-radius:6px; cursor:pointer;">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>

<script>
    // 1. INICIALIZAR CALENDARIO Y FILTROS
    flatpickr("#filtroFecha", {
        locale: "es",
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr, instance) {
            filtrarTabla();
        }
    });

    document.getElementById('filtroTexto').addEventListener('input', filtrarTabla);

    function filtrarTabla() {
        const textVal = document.getElementById('filtroTexto').value.toLowerCase();
        const dateVal = document.getElementById('filtroFecha').value;
        const rows = document.querySelectorAll('.tabla-row');

        rows.forEach(row => {
            if(!row.hasAttribute('data-search')) return; // Evitar la fila vacía
            
            const matchText = row.getAttribute('data-search').includes(textVal);
            const matchDate = dateVal === "" || row.getAttribute('data-fecha') === dateVal;
            
            row.style.display = (matchText && matchDate) ? 'grid' : 'none';
        });
    }

    // 2. LÓGICA DEL PUNTO DE VENTA
    const cliSelect = document.getElementById('clienteSelect');
    const select = document.getElementById('productoSelect');
    const input = document.getElementById('cantidadInput');
    const displayTotal = document.getElementById('displayTotal');
    const displayComision = document.getElementById('displayComision');
    const btnSubmit = document.getElementById('btnSubmit');
    const warning = document.getElementById('stockWarning');

    function calcularTotal() {
        const selectedOption = select.options[select.selectedIndex];
        const precio = parseFloat(selectedOption.getAttribute('data-precio') || 0);
        const stockMaximo = parseInt(selectedOption.getAttribute('data-stock') || 0);
        const cantidad = parseInt(input.value || 0);

        if (precio > 0 && cantidad > 0) {
            if(cantidad > stockMaximo) {
                warning.style.display = 'block';
                btnSubmit.disabled = true;
                displayTotal.innerText = '$0.00';
                displayComision.innerText = '+$0.00';
            } else {
                warning.style.display = 'none';
                btnSubmit.disabled = false;
                
                const total = precio * cantidad;
                const comision = total * 0.10; 
                
                displayTotal.innerText = '$' + total.toFixed(2);
                displayComision.innerText = '+$' + comision.toFixed(2);
            }
        } else {
            btnSubmit.disabled = true;
            displayTotal.innerText = '$0.00';
            displayComision.innerText = '+$0.00';
            warning.style.display = 'none';
        }
    }

    document.getElementById('formVenta').addEventListener('submit', async function(e) {
        e.preventDefault();
        const nombreProducto = select.options[select.selectedIndex].text.split('-')[0].trim();
        const totalText = displayTotal.innerText;
        const nombreCliente = cliSelect.value ? cliSelect.options[cliSelect.selectedIndex].text : 'Público General';

        const result = await Swal.fire({
            title: '¿Confirmar Venta?',
            html: `Vas a registrar la venta de <b>${input.value}x ${nombreProducto}</b><br>Para: <b>${nombreCliente}</b><br><br>Cóbrale al cliente: <b style="color:#4ade80; font-size:24px;">${totalText}</b>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4ade80',
            cancelButtonColor: '#333',
            confirmButtonText: '<span style="color:#000; font-weight:bold;">Cobrar y Registrar</span>',
            cancelButtonText: 'Cancelar',
            background: '#1a1a1a', color: '#fff'
        });

        if (result.isConfirmed) {
            try {
                Swal.fire({title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
                const res = await fetch(`/empleado/ventas`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ producto_id: select.value, cantidad: input.value, cliente_id: cliSelect.value })
                });

                const data = await res.json();
                
                if (res.ok && data.success) {
                    Swal.fire({
                        icon: 'success', title: '¡Venta Exitosa!', text: `Ganaste una comisión de $${parseFloat(data.comision).toFixed(2)}`,
                        background: '#1a1a1a', color: '#fff', confirmButtonColor: '#fad370'
                    }).then(() => window.location.reload());
                } else { Swal.fire('Error', data.message, 'error'); }
            } catch (error) { Swal.fire('Error', 'Falla en la conexión.', 'error'); }
        }
    });

    // 3. LÓGICA DEL MODAL DE TICKET
    function abrirTicket(data) {
        document.getElementById('tk-id').innerText = 'VN-00' + data.id;
        document.getElementById('tk-fecha').innerText = data.fecha;
        document.getElementById('tk-cliente').innerText = data.cliente;
        document.getElementById('tk-desc').innerText = data.cantidad + 'x ' + data.producto;
        document.getElementById('tk-precio-u').innerText = '$' + parseFloat(data.precio_u).toFixed(2);
        document.getElementById('tk-total').innerText = '$' + parseFloat(data.total).toFixed(2);
        document.getElementById('tk-gran-total').innerText = '$' + parseFloat(data.total).toFixed(2);
        document.getElementById('tk-comision').innerText = '+$' + parseFloat(data.comision).toFixed(2);
        
        document.getElementById('modalTicket').classList.add('active');
    }

    function cerrarTicket() {
        document.getElementById('modalTicket').classList.remove('active');
    }

    // Imprimir solo el ticket (opcional)
    window.addEventListener('beforeprint', () => {
        document.body.style.visibility = 'hidden';
        document.getElementById('printArea').style.visibility = 'visible';
        document.getElementById('printArea').style.position = 'absolute';
        document.getElementById('printArea').style.left = '0';
        document.getElementById('printArea').style.top = '0';
    });
    window.addEventListener('afterprint', () => {
        document.body.style.visibility = 'visible';
        document.getElementById('printArea').style.position = 'relative';
    });
</script>
@endsection