@extends('layouts.app')

@section('title', 'Agendar Cita - StyleNow')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="sucursal-id" content="{{ $sucursalSeleccionada ? $sucursalSeleccionada->suc_id : '' }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    :root {
        --bg-main: #0a0a0a; --bg-card: #151515; --bg-hover: #1f1f1f;
        --dorado: #fad370; --text-main: #ffffff; --text-muted: #888888;
        --border-color: #2a2a2a;
    }

    /* ESTILOS DEL MENÚ LATERAL */
    .sidebar-nav { padding: 1rem 0; }
    .nav-section { margin-bottom: 1.5rem; }
    .nav-section-title { padding: 0 1.5rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 700; }
    .nav-item { display: flex; align-items: center; gap: 1rem; padding: 0.9rem 1.5rem; color: var(--text-primary); text-decoration: none; transition: all 0.3s ease; cursor: pointer; position: relative; }
    .nav-item:hover { background: rgba(250, 211, 112, 0.1); border-left: 3px solid var(--dorado); padding-left: calc(1.5rem - 3px); }
    .nav-item.active { background: rgba(250, 211, 112, 0.15); border-left: 3px solid var(--dorado); padding-left: calc(1.5rem - 3px); color: var(--dorado); }
    .nav-icon { font-size: 1.2rem; width: 24px; text-align: center; }
    .nav-text { flex: 1; font-size: 0.95rem; font-weight: 500; }

    /* =========================================
       ✨ PANTALLA SELECCIÓN SUCURSAL INTEGRADA ✨
       ========================================= */
    .loc-view {
        background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 3rem; margin-bottom: 2rem; animation: fadeIn 0.4s ease;
    }
    .loc-title { font-family: 'Abril Fatface', cursive; font-size: 2.5rem; color: white; margin-bottom: 10px; }
    .loc-subtitle { color: var(--text-muted); font-size: 1rem; margin-bottom: 2.5rem; }
    
    .loc-tabs { display: flex; gap: 10px; margin-bottom: 2rem; border-bottom: 1px solid #333; padding-bottom: 15px; overflow-x: auto;}
    .loc-tab { background: transparent; color: #888; border: 1px solid transparent; font-size: 0.95rem; cursor: pointer; padding: 8px 20px; border-radius: 20px; transition: 0.3s; white-space: nowrap;}
    .loc-tab:hover { color: white; }
    .loc-tab.active { background: rgba(250, 211, 112, 0.1); color: var(--dorado); border-color: var(--dorado); font-weight: bold; }
    
    .loc-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
    .loc-card { background: #111; border: 1px solid #2a2a2a; border-radius: 12px; padding: 25px; cursor: pointer; transition: 0.3s; position: relative; display: flex; flex-direction: column; justify-content: space-between;}
    .loc-card:hover { border-color: var(--dorado); transform: translateY(-5px); box-shadow: 0 10px 30px rgba(250,211,112,0.15); background: #151515;}
    .loc-card-title { font-size: 1.1rem; font-weight: bold; color: white; margin-bottom: 5px; padding-right: 30px;}
    .loc-card-desc { color: var(--text-muted); font-size: 0.85rem; margin-bottom: 15px;}
    .loc-card-icon { position: absolute; right: 20px; top: 20px; font-size: 1.5rem; opacity: 0.3; transition: 0.3s;}
    .loc-card:hover .loc-card-icon { opacity: 1; transform: scale(1.2); }
    .loc-btn-select { background: rgba(250, 211, 112, 0.1); color: var(--dorado); padding: 8px; border-radius: 8px; text-align: center; font-weight: bold; font-size: 0.85rem; border: 1px solid var(--dorado); transition: 0.3s;}
    .loc-card:hover .loc-btn-select { background: var(--dorado); color: black;}

    /* WIZARD NORMAL */
    .wizard-header { margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;}
    .wizard-title { font-family: 'Abril Fatface', cursive; font-size: 2rem; color: var(--text-main); margin-bottom: 0.3rem;}
    .wizard-subtitle { color: var(--text-muted); font-size: 0.95rem; }
    .btn-change-loc { background: transparent; border: 1px solid #444; color: white; padding: 8px 16px; border-radius: 8px; cursor: pointer; transition: 0.3s; font-size: 0.85rem; text-decoration: none; display: flex; align-items: center; gap: 5px;}
    .btn-change-loc:hover { border-color: var(--dorado); color: var(--dorado); }

    /* STEPS INDICATORS */
    .steps-container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2.5rem; }
    .step-box { background: var(--bg-card); border-radius: 12px; padding: 1.2rem; border-top: 3px solid transparent; transition: 0.3s; opacity: 0.5; border: 1px solid var(--border-color);}
    .step-box.active { border-color: #4ade80; opacity: 1; background: linear-gradient(180deg, rgba(74,222,128,0.05) 0%, rgba(21,21,21,1) 100%); }
    .step-box.completed { border-color: var(--dorado); opacity: 1; cursor: pointer; }
    .step-num { background: rgba(255,255,255,0.1); width: 25px; height: 25px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-bottom: 10px; color: white; font-size: 0.8rem;}
    .step-box.active .step-num { background: #4ade80; color: #000; }
    .step-box.completed .step-num { background: var(--dorado); color: #000; }
    .step-title { font-weight: bold; font-size: 1rem; color: white; margin-bottom: 3px;}
    .step-desc { font-size: 0.8rem; color: var(--text-muted); }

    /* LAYOUT PRINCIPAL */
    .booking-layout { display: grid; grid-template-columns: 1fr 320px; gap: 2rem; align-items: start; }
    .step-content { display: none; animation: fadeIn 0.4s ease; }
    .step-content.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .section-heading { font-family: 'Abril Fatface', cursive; font-size: 1.5rem; color: white; margin-bottom: 1.5rem; }

    /* GRID DE SELECCIÓN */
    .selection-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.2rem; }
    .card-select { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; cursor: pointer; transition: 0.3s; position: relative; }
    .card-select:hover { border-color: var(--dorado); background: var(--bg-hover); }
    
    .card-select.selected { border-color: var(--dorado); box-shadow: 0 0 0 1px var(--dorado); background: rgba(250, 211, 112, 0.05); }
    .check-icon { position: absolute; top: 1rem; right: 1rem; width: 22px; height: 22px; border-radius: 50%; border: 2px solid var(--text-muted); display: flex; align-items: center; justify-content: center; color: transparent; transition: 0.3s;}
    .card-select.selected .check-icon { background: var(--dorado); border-color: var(--dorado); color: #000; }
    
    .card-title { font-size: 1.05rem; font-weight: bold; color: white; margin-bottom: 5px; margin-top: 5px;}
    .card-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 12px; margin-top: 12px;}
    .card-price { font-size: 1.2rem; font-weight: bold; color: var(--dorado); }
    .card-time { font-size: 0.8rem; color: var(--text-muted); }

    .stylist-avatar { width: 50px; height: 50px; background: #333; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; margin: 0 auto 10px; }

    /* FECHA Y HORA */
    .datetime-container { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; }
    .hours-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: 10px; margin-top: 15px;}
    .hour-btn { background: #1a1a1a; border: 1px solid #333; color: white; padding: 12px; border-radius: 8px; text-align: center; cursor: pointer; transition: 0.3s; font-weight: bold; font-size: 0.95rem; }
    .hour-btn:hover:not(:disabled) { background: var(--bg-hover); border-color: var(--dorado); transform: translateY(-2px); }
    .hour-btn.selected { background: var(--dorado); color: #000; border-color: var(--dorado); transform: translateY(-2px); }

    .form-group { margin-bottom: 1.2rem; }
    .form-label { display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-input, .form-textarea { width: 100%; background: #111; border: 1px solid #333; border-radius: 8px; padding: 12px; color: white; font-family: 'Poppins'; outline: none; transition: 0.3s; }
    .form-input:focus, .form-textarea:focus { border-color: var(--dorado); }

    /* PANEL DERECHO: RESUMEN */
    .summary-panel { background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color); padding: 1.5rem; position: sticky; top: 20px; }
    .summary-title { font-family: 'Abril Fatface', cursive; font-size: 1.3rem; color: white; margin-bottom: 1.5rem; border-bottom: 1px dashed #333; padding-bottom: 10px;}
    
    .summary-item { margin-bottom: 1.2rem; }
    .s-label { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 3px; text-transform: uppercase; letter-spacing: 0.5px;}
    .s-value { font-size: 0.95rem; font-weight: 600; color: white; }
    
    .service-list { display: flex; flex-direction: column; gap: 5px; }
    .service-badge { background: #222; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; display: inline-block; width: fit-content; border: 1px solid #333;}

    .summary-total { border-top: 1px solid var(--border-color); padding-top: 1.2rem; margin-top: 1.2rem; display: flex; justify-content: space-between; align-items: flex-end;}
    .total-val { font-size: 2rem; font-weight: bold; color: var(--dorado); line-height: 1; font-family: 'Abril Fatface', cursive;}

    .nav-buttons { display: flex; gap: 15px; margin-top: 2rem; justify-content: flex-end;}
    .btn-next { background: var(--dorado); color: #000; font-weight: bold; padding: 12px 30px; border-radius: 8px; border: none; cursor: pointer; transition: 0.3s; font-size: 0.95rem; }
    .btn-next:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(250,211,112,0.3); }
    .btn-back { background: transparent; color: white; border: 1px solid #444; padding: 12px 25px; border-radius: 8px; cursor: pointer; transition: 0.3s; font-size: 0.95rem;}
    .btn-back:hover { border-color: white; }

    .mobile-total-bar { display: none; }

    /* ETIQUETAS Y FILTROS */
    .service-category-badge { display: inline-block; background: rgba(250, 211, 112, 0.1); color: var(--dorado); font-size: 0.6rem; padding: 3px 8px; border-radius: 4px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
    
    .category-filters { display: flex; gap: 8px; margin-bottom: 20px; overflow-x: auto; padding-bottom: 10px; }
    .category-filters::-webkit-scrollbar { height: 4px; }
    .category-filters::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
    .cat-filter-btn { background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-muted); padding: 6px 15px; border-radius: 20px; font-size: 0.8rem; cursor: pointer; transition: 0.3s; white-space: nowrap; font-family: 'Poppins'; }
    .cat-filter-btn:hover { border-color: var(--dorado); color: white; }
    .cat-filter-btn.active { background: var(--dorado); color: #000; border-color: var(--dorado); font-weight: bold; }

    @keyframes pulseGlow { 0% { transform: scale(1); } 50% { transform: scale(1.05); text-shadow: 0 0 15px var(--dorado); } 100% { transform: scale(1); } }
    .pulse-update { animation: pulseGlow 0.4s ease; }

    /* FLATPICKR CUSTOM */
    .flatpickr-calendar.inline { width: 100% !important; max-width: 100% !important; background: transparent !important; border: none !important; padding: 0 !important; box-shadow: none !important; font-family: 'Poppins', sans-serif !important; }
    .flatpickr-months { margin-bottom: 15px !important; }
    .flatpickr-month { color: white !important; height: 50px !important; }
    .flatpickr-current-month { font-family: 'Abril Fatface', cursive !important; font-size: 1.8rem !important; color: var(--dorado) !important; display: flex !important; align-items: center !important; justify-content: center !important; }
    .flatpickr-current-month .numInputWrapper:hover { background: rgba(255,255,255,0.05) !important; border-radius: 8px;}
    span.flatpickr-weekday { color: var(--text-muted) !important; font-weight: bold !important; font-size: 0.85rem !important; text-transform: uppercase !important; }
    .flatpickr-days { width: 100% !important; }
    .dayContainer { width: 100% !important; min-width: 100% !important; max-width: 100% !important; display: grid !important; grid-template-columns: repeat(7, 1fr) !important; gap: 5px !important; justify-content: center !important; }
    .flatpickr-day { width: 100% !important; max-width: 100% !important; height: 45px !important; line-height: 45px !important; border-radius: 8px !important; color: white !important; font-size: 1rem !important; font-weight: 500 !important; border: 1px solid #222 !important; transition: all 0.3s ease !important; display: flex !important; align-items: center !important; justify-content: center !important; background: #111 !important; }
    .flatpickr-day:hover:not(.flatpickr-disabled) { border-color: var(--dorado) !important; color: var(--dorado) !important; }
    .flatpickr-day.selected { background: var(--dorado) !important; color: #000 !important; font-weight: bold !important; border-color: var(--dorado) !important; }
    .flatpickr-day.today { border: 1px dashed var(--dorado) !important; color: var(--dorado) !important; background: rgba(250,211,112,0.05) !important;}
    .flatpickr-day.flatpickr-disabled { color: #444 !important; border-color: transparent !important; background: transparent !important;}
    .flatpickr-prev-month, .flatpickr-next-month { fill: var(--dorado) !important; background: rgba(250, 211, 112, 0.1) !important; border-radius: 8px !important; height: 35px !important; width: 35px !important; display: flex !important; align-items: center !important; justify-content: center !important; transition: 0.3s !important; top: 15px !important;}
    .flatpickr-prev-month:hover, .flatpickr-next-month:hover { background: var(--dorado) !important; fill: #000 !important; }

    @media (max-width: 1024px) {
        .booking-layout { grid-template-columns: 1fr; }
        .summary-panel { display: none; }
        .mobile-total-bar { display: flex; position: fixed; bottom: 0; left: 0; width: 100%; background: #111; border-top: 1px solid #333; padding: 15px 20px; z-index: 1000; justify-content: space-between; align-items: center; box-shadow: 0 -10px 20px rgba(0,0,0,0.5);}
        .mobile-total-bar .m-price { font-size: 1.5rem; font-weight: bold; color: var(--dorado); line-height: 1;}
        .mobile-total-bar .m-time { font-size: 0.8rem; color: var(--text-muted); }
    }
    @media (max-width: 768px) {
        .steps-container { grid-template-columns: 1fr 1fr; }
    }
</style>
@endpush

@section('sidebar-cliente')
<nav class="sidebar-nav">
    <div class="nav-section">
        <p class="nav-section-title">PRINCIPAL</p>
        <a href="/cliente/dashboard" class="nav-item">
            <span class="nav-icon">🏠</span><span class="nav-text">Mi Panel</span>
        </a>
    </div>

    <div class="nav-section">
        <p class="nav-section-title">RESERVAS</p>
        <a href="/cliente/citas/nueva" class="nav-item active">
            <span class="nav-icon">➕</span><span class="nav-text">Nueva Cita</span>
        </a>
        <a href="/cliente/mis-citas" class="nav-item">
            <span class="nav-icon">📅</span><span class="nav-text">Mis Citas</span>
        </a>
        <a href="/cliente/historial" class="nav-item">
            <span class="nav-icon">📋</span><span class="nav-text">Historial</span>
        </a>
    </div>

    <div class="nav-section">
        <p class="nav-section-title">BENEFICIOS</p>
        <a href="/cliente/puntos" class="nav-item">
            <span class="nav-icon">⭐</span><span class="nav-text">Mis Puntos</span>
        </a>
        <a href="/cliente/promo-vista" class="nav-item">
            <span class="nav-icon">🎁</span><span class="nav-text">Promociones</span>
        </a>
    </div>

    <div class="nav-section">
        <p class="nav-section-title">CUENTA</p>
        <a href="/cliente/perfil-vista" class="nav-item">
            <span class="nav-icon">👤</span><span class="nav-text">Mi Perfil</span>
        </a>
    </div>
</nav>
@endsection

@section('topbar-left')
    @if(!$sucursalSeleccionada)
        <h1 style="font-family: 'Abril Fatface', cursive; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.3rem;">Nueva Cita</h1>
        <p style="color: var(--text-secondary); font-size: 0.95rem;">Selecciona tu ubicación para empezar.</p>
    @else
        <h1 style="font-family: 'Abril Fatface', cursive; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.3rem;">Agendar Cita</h1>
        <p style="color: var(--text-secondary); font-size: 0.95rem;">Reserva en <span style="color: var(--dorado); font-weight:bold;">{{ $sucursalSeleccionada->suc_nombre }}</span>.</p>
    @endif
@endsection

@section('content')

@if(!$sucursalSeleccionada)
<div class="loc-view">
    <h2 class="loc-title">📍 ¿Dónde te encuentras?</h2>
    <p class="loc-subtitle">Para mostrarte los servicios y profesionales disponibles, selecciona la sucursal más cercana a ti.</p>
    
    <div class="loc-tabs">
        <button class="loc-tab active" onclick="filtrarZonas('Todas', this)">Todas las zonas</button>
        @foreach($sucursalesAgrupadas->keys() as $zona)
            <button class="loc-tab" onclick="filtrarZonas('{{ $zona }}', this)">Sector {{ $zona ?? 'General' }}</button>
        @endforeach
    </div>

    <div class="loc-grid">
        @foreach($sucursalesAgrupadas as $zona => $sucursales)
            @foreach($sucursales as $suc)
<div class="loc-card zona-{{ Str::slug($zona ?? 'general') }}" 
    data-id="{{ $suc->suc_id }}"
    onclick="seleccionarSucursal(this.dataset.id)">                    <div class="loc-card-icon">🏬</div>
                    <div>
                        <div class="loc-card-title">{{ $suc->suc_nombre }}</div>
                        <div class="loc-card-desc">📍 Dirección: Zona {{ $suc->suc_direccion ?? 'No especificada' }}</div>
                    </div>
                    <div class="loc-btn-select">
                        Seleccionar Sucursal →
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
</div>

<script>
    function filtrarZonas(zona, btn) {
        document.querySelectorAll('.loc-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');

        document.querySelectorAll('.loc-card').forEach(card => {
            if (zona === 'Todas') {
                card.style.display = 'flex';
            } else {
                if (card.classList.contains('zona-' + zona.toLowerCase().replace(/ /g, '-'))) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            }
        });
    }

    function seleccionarSucursal(id) {
        window.location.href = '/cliente/citas/nueva?sucursal_id=' + id;
    }
</script>
@endif

@if($sucursalSeleccionada)
<div class="wizard-header">
    <div style="flex:1;">
        </div>
    <a href="/cliente/citas/nueva" class="btn-change-loc"><span>📍</span> Cambiar Sucursal</a>
</div>

<div class="steps-container">
    <div class="step-box active" id="indicator-1" onclick="goToStep(1)">
        <div class="step-num">1</div>
        <div class="step-title">Servicios</div>
        <div class="step-desc">Elige qué deseas</div>
    </div>
    <div class="step-box" id="indicator-2">
        <div class="step-num">2</div>
        <div class="step-title">Estilista</div>
        <div class="step-desc">Quien te atenderá</div>
    </div>
    <div class="step-box" id="indicator-3">
        <div class="step-num">3</div>
        <div class="step-title">Disponibilidad</div>
        <div class="step-desc">Fecha y hora</div>
    </div>
    <div class="step-box" id="indicator-4">
        <div class="step-num">4</div>
        <div class="step-title">Confirmar</div>
        <div class="step-desc">Resumen y pago</div>
    </div>
</div>

<div class="booking-layout">
    <div class="wizard-content">
        
        <div id="step-1" class="step-content active">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2 class="section-heading" style="margin:0;">Selecciona tus Servicios</h2>
                <span style="color:var(--text-muted); font-size:12px; font-weight:bold; text-transform:uppercase;" id="contador-servicios">0 seleccionados</span>
            </div>

            @php
                $categoriasUnicas = collect($servicios)->unique('srv_categoriaId');
            @endphp
            <div class="category-filters">
                <button class="cat-filter-btn active" onclick="filterCategory('all', this)">✨ Todos</button>
                @foreach($categoriasUnicas as $cat)
                    <button class="cat-filter-btn" 
                        data-id="{{ $cat->srv_categoriaId }}"
                        onclick="filterCategory(this.dataset.id, this)">🏷️ {{ $cat->categoria_nombre }}</button>                
                @endforeach
            </div>
            
            <div class="selection-grid">
                @foreach($servicios as $srv)
                    <div class="card-select service-card-item" 
                         id="srv-{{ $srv->srv_id }}"
                         data-id="{{ $srv->srv_id }}"
                         data-nombre="{{ $srv->srv_nombre }}"
                         data-precio="{{ $srv->srv_precio }}"
                         data-duracion="{{ $srv->srv_duracion ?? 30 }}"
                         data-categoria="{{ $srv->srv_categoriaId }}"
                         onclick="toggleService(this.dataset.id, this.dataset.nombre, this.dataset.precio, this.dataset.duracion, this.dataset.categoria)">
                        <div class="check-icon">✓</div>
                        
                        <div class="service-category-badge">{{ $srv->categoria_nombre }}</div>
                        
                        <div class="card-title">{{ $srv->srv_nombre }}</div>
                        <div class="card-footer">
                            <div class="card-price">${{ number_format($srv->srv_precio, 2) }}</div>
                            <div class="card-time">⏱️ {{ $srv->srv_duracion ?? 30 }} min</div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="nav-buttons">
                <div></div> <button class="btn-next" onclick="nextStep(2)" id="btn-next-1" disabled style="opacity: 0.5;">Continuar →</button>
            </div>
        </div>

        <div id="step-2" class="step-content">
            <h2 class="section-heading">Elige tu Profesional</h2>
            <div class="selection-grid" id="stylist-grid">
                <div class="card-select emp-card text-center" id="emp-0" onclick="selectStylist(0, 'Cualquier Profesional')" style="text-align: center;">
                    <div class="stylist-avatar">🏢</div>
                    <div class="card-title" style="margin-top: 15px;">Cualquiera Disponible</div>
                    <div class="card-desc" style="color:var(--text-muted); font-size:0.8rem; margin-top:5px;">El sistema buscará el primer turno libre.</div>
                </div>
                
                @foreach($empleados as $emp)
                    <div class="card-select emp-card text-center" 
                         id="emp-{{ $emp->emp_id }}"
                         data-id="{{ $emp->emp_id }}"
                         data-nombre="{{ $emp->usr_nombre . ' ' . $emp->usr_apellido }}"
                         data-categorias="{{ json_encode($emp->categorias) }}"
                         onclick="selectStylist(this.dataset.id, this.dataset.nombre)"
                         style="text-align: center;">
                        <div class="stylist-avatar">{{ substr($emp->usr_nombre, 0, 1) }}{{ substr($emp->usr_apellido, 0, 1) }}</div>
                        <div class="card-title" style="margin-top: 15px;">{{ $emp->usr_nombre }} {{ $emp->usr_apellido }}</div>
                        <div class="card-desc" style="color: var(--dorado); font-size:0.8rem; margin-top:5px; font-weight:bold;">✨ Especialista</div>
                    </div>
                @endforeach
            </div>
            <div class="nav-buttons">
                <button class="btn-back" onclick="goToStep(1)">← Volver</button>
                <button class="btn-next" onclick="nextStep(3)" id="btn-next-2" disabled style="opacity: 0.5;">Continuar →</button>
            </div>
        </div>

        <div id="step-3" class="step-content">
            <h2 class="section-heading">Elige Fecha y Hora</h2>
            <p style="color: var(--dorado); margin-bottom: 20px; font-weight: bold; font-size:0.9rem;">⏱️ Buscando espacio exacto para: <span id="time-warning">0</span> min.</p>
            
            <div class="datetime-container">
                <div style="margin-bottom: 20px;">
                    <input type="hidden" id="datePicker" class="form-input">
                </div>
                
                <hr style="border: 0; border-top: 1px dashed #333; margin: 30px 0;">
                
                <label class="form-label" style="color: white;"><span style="color: var(--dorado);">●</span> Horarios Disponibles</label>
                <div id="hours-container" class="hours-grid">
                    <p style="color: var(--text-muted); font-size: 0.85rem; grid-column: span 3; margin-top: 10px;">Selecciona una fecha en el calendario arriba.</p>
                </div>
            </div>
            <div class="nav-buttons">
                <button class="btn-back" onclick="goToStep(2)">← Volver</button>
                <button class="btn-next" onclick="nextStep(4)" id="btn-next-3" disabled style="opacity: 0.5;">Continuar →</button>
            </div>
        </div>

        <div id="step-4" class="step-content">
            <h2 class="section-heading">Último Paso</h2>
            <div class="datetime-container">
                <div class="form-group">
                    <label class="form-label">Tus Datos de Reserva</label>
                    <input type="text" class="form-input" value="{{ $user->usr_nombre }} {{ $user->usr_apellido }} ({{ $user->usr_email }})" readonly style="color:#888;">
                </div>
                
                <div class="form-group" style="margin-top: 25px; border-top: 1px solid #222; padding-top: 25px;">
                    <label class="form-label">¿Tienes un código promocional?</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="promoCodeInput" class="form-input" placeholder="Ej: DESC10" style="font-family: monospace; text-transform: uppercase;">
                        <button type="button" onclick="aplicarPromo()" style="background: rgba(250, 211, 112, 0.1); color: var(--dorado); border: 1px solid var(--dorado); padding: 0 20px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s;" onmouseover="this.style.background='var(--dorado)'; this.style.color='black';" onmouseout="this.style.background='rgba(250, 211, 112, 0.1)'; this.style.color='var(--dorado)';">Verificar</button>
                    </div>
                    <div id="promoMessage" style="font-size: 0.85rem; margin-top: 8px; font-weight: bold;"></div>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label class="form-label">Notas para el estilista (Opcional)</label>
                    <textarea id="notasInput" class="form-textarea" placeholder="Ej: Tengo el cabello muy largo..." rows="2"></textarea>
                </div>
            </div>
            <div class="nav-buttons">
                <button class="btn-back" onclick="goToStep(3)">← Volver</button>
                <button class="btn-next" onclick="submitBooking()" style="background: #4ade80;">✅ Confirmar Reserva</button>
            </div>
        </div>

    </div>

    <div>
        <div class="summary-panel">
            <h3 class="summary-title">📄 Tu Reserva</h3>
            
            <div class="summary-item">
                <div class="s-label">Sucursal:</div>
                <div class="s-value" style="color: var(--dorado);">{{ $sucursalSeleccionada->suc_nombre }}</div>
            </div>

            <div class="summary-item">
                <div class="s-label">Servicios:</div>
                <div class="service-list" id="sum-servicios"><span style="color:#666; font-size:13px;">Ninguno</span></div>
            </div>
            
            <div class="summary-item">
                <div class="s-label">Profesional:</div>
                <div class="s-value" id="sum-estilista" style="font-size: 0.9rem;">No seleccionado</div>
            </div>
            
            <div class="summary-item">
                <div class="s-label">Horario:</div>
                <div class="s-value" id="sum-fecha" style="font-size: 0.9rem;">No seleccionado</div>
                <div class="s-value" id="sum-hora" style="color:var(--dorado); font-size: 0.9rem;"></div>
            </div>
            
            <div class="summary-total">
                <div>
                    <div class="s-label">Tiempo aprox.</div>
                    <div class="s-value" id="sum-duracion" style="color:#aaa;">0 min</div>
                </div>
                <div style="text-align: right;">
                    <div class="s-label">Total del Servicio</div>
                    <div id="descuentoRow" style="display: none; color: #4ade80; font-size: 0.75rem; margin-bottom: 2px; font-weight:bold;">
                        Descuento: <span id="valorDescuentoText">-$0.00</span>
                    </div>
                    <div class="total-val" id="sum-precio" style="font-size: 1.5rem;">$0.00</div>

                    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #444;">
                        <div class="s-label" style="color: var(--dorado);">Abono Inicial Requerido (20%)</div>
                        <div class="total-val" id="sum-abono" style="font-size: 1.8rem; color: #4ade80;">$0.00</div>
                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 5px;">Saldo a pagar en local: <span id="sum-saldo" style="font-weight:bold; color:white;">$0.00</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mobile-total-bar">
    <div>
        <div class="m-price" id="mobile-precio">$0.00</div>
        <div class="m-time" id="mobile-duracion">0 min</div>
    </div>
</div>
@endif

@endsection

@section('scripts')
@if($sucursalSeleccionada)
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>

<script>
    let sucursalMeta = document.querySelector('meta[name="sucursal-id"]');
    
    let booking = {
        sucursal_id: sucursalMeta ? sucursalMeta.content : null,
        servicios: [], 
        precio_total: 0,
        duracion_total: 0,
        empleado_id: null,
        empleado_nombre: '',
        fecha: null,
        hora: null,
        notas: '',
        abono: 0 // Añadimos el abono al estado de la reserva
    };

    let promoActiva = { aplicada: false, id: null, tipo: '', valor: 0 };
    let precioOriginal = 0; 

    async function aplicarPromo() {
        const codigo = document.getElementById('promoCodeInput').value.trim();
        const msgDiv = document.getElementById('promoMessage');
        
        if(!codigo) {
            msgDiv.innerHTML = '<span style="color: #ef4444;">Ingresa un código primero.</span>';
            return;
        }

        msgDiv.innerHTML = '<span style="color: #888;">Validando...</span>';

        try {
            const res = await fetch('/cliente/agendar/validar-promo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ codigo: codigo })
            });
            
            const data = await res.json();

            if(data.success) {
                promoActiva = { aplicada: true, id: data.id, tipo: data.tipo, valor: data.valor };
                msgDiv.innerHTML = `<span style="color: #4ade80;">✅ ¡Código válido! Descuento aplicado.</span>`;
                document.getElementById('promoCodeInput').disabled = true;
                recalcularTotales(); 
            } else {
                msgDiv.innerHTML = `<span style="color: #ef4444;">❌ ${data.message}</span>`;
                promoActiva = { aplicada: false, id: null, tipo: '', valor: 0 };
                recalcularTotales();
            }
        } catch (e) {
            msgDiv.innerHTML = '<span style="color: #ef4444;">❌ Error de conexión.</span>';
        }
    }

    function filterCategory(catId, btnElement) {
        document.querySelectorAll('.cat-filter-btn').forEach(btn => btn.classList.remove('active'));
        btnElement.classList.add('active');

        document.querySelectorAll('.service-card-item').forEach(card => {
            if (catId === 'all' || card.dataset.categoria == catId) {
                card.style.display = 'block';
                card.style.animation = 'none'; 
                card.offsetHeight; 
                card.style.animation = 'fadeIn 0.4s ease';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function goToStep(step) {
        document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.step-box').forEach(el => el.classList.remove('active'));
        document.getElementById(`step-${step}`).classList.add('active');
        document.getElementById(`indicator-${step}`).classList.add('active');
    }

    function nextStep(step) {
        if (step === 2) {
            let categoriasRequeridas = [...new Set(booking.servicios.map(s => parseInt(s.categoria, 10)))];
            let estilistasDisponibles = 0;

            document.querySelectorAll('.emp-card').forEach(card => {
                if(card.id === 'emp-0') return; 

                let especialidadesRaw = JSON.parse(card.getAttribute('data-categorias') || '[]');
                let especialidades = especialidadesRaw.map(Number);
                
                let puedeRealizarTodo = categoriasRequeridas.every(cat => especialidades.includes(cat));

                if (puedeRealizarTodo) {
                    card.style.display = 'block';
                    estilistasDisponibles++;
                } else {
                    card.style.display = 'none';
                    card.classList.remove('selected');
                }
            });

            document.getElementById('emp-0').style.display = estilistasDisponibles > 0 ? 'block' : 'none';

            if (estilistasDisponibles === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin especialistas',
                    text: 'No hay un estilista en esta sucursal que realice TODOS estos servicios seleccionados.',
                    background: '#151515', color: '#fff', confirmButtonColor: '#fad370'
                });
                return; 
            }
        }

        document.getElementById(`indicator-${step-1}`).classList.add('completed');
        goToStep(step);
    }

    function toggleService(id, nombre, precio, duracion, categoriaId) {
        const card = document.getElementById(`srv-${id}`);
        const index = booking.servicios.findIndex(s => s.id === id);

        if (index > -1) {
            booking.servicios.splice(index, 1);
            card.classList.remove('selected');
        } else {
            booking.servicios.push({ 
                id: id, nombre: nombre, precio: parseFloat(precio), 
                duracion: parseInt(duracion, 10), categoria: parseInt(categoriaId, 10) 
            });
            card.classList.add('selected');
        }
        recalcularTotales();
    }

    function recalcularTotales() {
        precioOriginal = booking.servicios.reduce((sum, s) => sum + s.precio, 0);
        booking.duracion_total = booking.servicios.reduce((sum, s) => sum + s.duracion, 0);

        let precioFinal = precioOriginal;
        let rebaja = 0;

        if (promoActiva.aplicada && precioOriginal > 0) {
            if (promoActiva.tipo === 'Porcentaje') {
                rebaja = (precioOriginal * promoActiva.valor) / 100;
            } else if (promoActiva.tipo === 'Fijo') {
                rebaja = promoActiva.valor;
            }
            
            precioFinal = precioOriginal - rebaja;
            if (precioFinal < 0) precioFinal = 0; 
            
            document.getElementById('descuentoRow').style.display = 'block';
            document.getElementById('valorDescuentoText').innerText = '-$' + rebaja.toFixed(2);
            
            document.getElementById('sum-precio').innerHTML = `<span style="font-size: 1.1rem; color: #888; text-decoration: line-through; margin-right: 8px;">$${precioOriginal.toFixed(2)}</span>$${precioFinal.toFixed(2)}`;
            document.getElementById('mobile-precio').innerHTML = `<span style="font-size: 1rem; color: #888; text-decoration: line-through; margin-right: 5px;">$${precioOriginal.toFixed(2)}</span>$${precioFinal.toFixed(2)}`;
        } else {
            document.getElementById('descuentoRow').style.display = 'none';
            document.getElementById('sum-precio').innerText = '$' + precioFinal.toFixed(2);
            document.getElementById('mobile-precio').innerText = '$' + precioFinal.toFixed(2);
        }

        booking.precio_total = precioFinal;

        // Calcular el abono (20%) y el saldo
        let abonoRequerido = precioFinal * 0.20;
        let saldoPendiente = precioFinal - abonoRequerido;
        booking.abono = abonoRequerido; // Guardar en el objeto booking

        // Actualizar UI del abono
        let sumAbonoEl = document.getElementById('sum-abono');
        let sumSaldoEl = document.getElementById('sum-saldo');
        if(sumAbonoEl) sumAbonoEl.innerText = '$' + abonoRequerido.toFixed(2);
        if(sumSaldoEl) sumSaldoEl.innerText = '$' + saldoPendiente.toFixed(2);

        const listaDiv = document.getElementById('sum-servicios');
        if (booking.servicios.length > 0) {
            listaDiv.innerHTML = booking.servicios.map(s => `<span class="service-badge">${s.nombre}</span>`).join('');
        } else {
            listaDiv.innerHTML = '<span style="color:#666; font-size:13px;">Ninguno</span>';
        }

        const precioTotalEl = document.getElementById('sum-precio');
        precioTotalEl.classList.remove('pulse-update'); void precioTotalEl.offsetWidth; precioTotalEl.classList.add('pulse-update');

        const mobilePrecioEl = document.getElementById('mobile-precio');
        mobilePrecioEl.classList.remove('pulse-update'); void mobilePrecioEl.offsetWidth; mobilePrecioEl.classList.add('pulse-update');

        document.getElementById('sum-duracion').innerText = booking.duracion_total + ' min';
        document.getElementById('contador-servicios').innerText = booking.servicios.length + ' seleccionado(s)';
        document.getElementById('time-warning').innerText = booking.duracion_total;
        document.getElementById('mobile-duracion').innerText = `${booking.duracion_total} min`;

        const btn = document.getElementById('btn-next-1');
        if (booking.servicios.length > 0) {
            btn.disabled = false; btn.style.opacity = '1';
        } else {
            btn.disabled = true; btn.style.opacity = '0.5';
        }

        if(booking.fecha) resetFecha();
    }

    function selectStylist(id, nombre) {
        // 1. Quitar la selección visual de todas las tarjetas
        document.querySelectorAll('#step-2 .card-select').forEach(el => el.classList.remove('selected'));
        
        let cardActiva = document.getElementById(`emp-${id}`);
        
        // 2. Si eligió "Cualquiera Disponible" (ID 0)
        if (id == 0 || id === '0') {
            // Buscamos todas las tarjetas de estilistas reales (excluyendo la de "cualquiera")
            let tarjetas = Array.from(document.querySelectorAll('.emp-card:not(#emp-0)'));
            
            // Encontramos la primera que sí esté visible (que pasó el filtro de servicios)
            let primerDisponible = tarjetas.find(card => card.style.display === 'block');
            
            if (primerDisponible) {
                // MÁGIA: Reemplazamos el ID 0 por el ID real del primer estilista
                id = primerDisponible.dataset.id;
                
                // Mantenemos seleccionada visualmente la tarjeta de "Cualquiera" para no confundir al usuario
                cardActiva = document.getElementById('emp-0');
            }
        }

        // 3. Pintar de dorado la tarjeta activa
        if(cardActiva) {
            cardActiva.classList.add('selected');
        }

        // 4. Guardar los datos reales para la Base de Datos
        booking.empleado_id = id;
        booking.empleado_nombre = nombre;
        document.getElementById('sum-estilista').innerText = nombre;
        
        // Activar el botón de continuar
        const btn = document.getElementById('btn-next-2');
        btn.disabled = false; 
        btn.style.opacity = '1';

        // Resetear fecha si ya había elegido una con otro empleado
        if(booking.fecha) resetFecha();
    }

    function resetFecha() {
        booking.hora = null;
        document.getElementById('sum-hora').innerText = "";
        document.getElementById('btn-next-3').disabled = true;
        document.getElementById('btn-next-3').style.opacity = '0.5';
        fetchAvailableHours(booking.fecha);
    }

    flatpickr("#datePicker", {
        locale: "es", minDate: "today", inline: true, disableMobile: "true",
        onChange: function(selectedDates, dateStr) {
            booking.fecha = dateStr;
            const formatOptions = { weekday: 'long', day: 'numeric', month: 'long'};
            document.getElementById('sum-fecha').innerText = selectedDates[0].toLocaleDateString('es-ES', formatOptions);
            resetFecha();
        }
    });

    async function fetchAvailableHours(dateStr) {
        const container = document.getElementById('hours-container');
        container.innerHTML = '<p style="color:#fad370; margin-top:10px; font-size:0.9rem;">Calculando espacios libres...</p>';

        try {
            const res = await fetch(`/cliente/agendar/horas-disponibles`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ fecha: dateStr, empleado_id: booking.empleado_id, duracion: booking.duracion_total })
            });
            const data = await res.json();
            
            if (data.success && data.horas.length > 0) {
                container.innerHTML = '';
                data.horas.forEach(hora => {
                    const btn = document.createElement('div');
                    btn.className = 'hour-btn'; btn.innerText = hora;
                    btn.onclick = () => selectHour(hora, btn);
                    container.appendChild(btn);
                });
            } else {
                container.innerHTML = `<p style="color:#ef4444; grid-column:span 3; margin-top:10px; font-size:0.9rem;">No hay un bloque continuo de ${booking.duracion_total} min este día.</p>`;
            }
        } catch (error) { container.innerHTML = '<p style="color:#ef4444; margin-top:10px;">Error de red.</p>'; }
    }

    function selectHour(hora, btnElement) {
        document.querySelectorAll('.hour-btn').forEach(el => el.classList.remove('selected'));
        btnElement.classList.add('selected');
        
        booking.hora = hora;
        document.getElementById('sum-hora').innerText = "a las " + hora;
        
        const btn = document.getElementById('btn-next-3');
        btn.disabled = false; btn.style.opacity = '1';
    }

    async function submitBooking() {
        booking.notas = document.getElementById('notasInput').value;

        const payload = {
            sucursal_id: booking.sucursal_id, empleado_id: booking.empleado_id, empleado_nombre: booking.empleado_nombre,
            fecha: booking.fecha, hora: booking.hora, precio_total: booking.precio_total, duracion_total: booking.duracion_total,
            promocion_id: promoActiva.id, notas: booking.notas, servicios_ids: booking.servicios.map(s => s.id),
            nombres_servicios: booking.servicios.map(s => s.nombre),
            abono: booking.abono // <-- ENVIAR EL ABONO AL CONTROLADOR
        };

        try {
            Swal.fire({title: 'Reservando...', text: 'Validando disponibilidad.', allowOutsideClick: false, didOpen: () => Swal.showLoading(), background: '#151515', color: '#fff'});
            
            const res = await fetch(`/cliente/agendar/guardar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            
            if (res.ok && data.success) {
                Swal.fire({
                    icon: 'success', title: '¡Cita Confirmada!', text: 'Te esperamos en la sucursal seleccionada.',
                    background: '#151515', color: '#fff', confirmButtonColor: '#4ade80'
                }).then(() => { window.location.href = '/cliente/mis-citas'; });
            } else {
                Swal.fire({icon: 'error', title: 'Ups', text: data.message || 'Ese horario se acaba de ocupar.', background: '#151515', color: '#fff', confirmButtonColor: '#fad370'});
            }
        } catch (error) { Swal.fire({icon: 'error', title: 'Error', text: 'Falla en la conexión.', background: '#151515', color: '#fff'}); }
    }
</script>
@endif
@endsection