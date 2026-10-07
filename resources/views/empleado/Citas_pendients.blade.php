@extends('layouts.app')

@section('title', 'Mis Citas Programadas - StyleNow')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    /* Estilos del sidebar-nav */
    .sidebar-nav { padding: 2rem 0; }
    .nav-section { margin-bottom: 1.5rem; }
    .nav-section-title { padding: 0 1.5rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 700; }
    .nav-item { display: flex; align-items: center; gap: 1rem; padding: 0.9rem 1.5rem; color: var(--text-primary); text-decoration: none; transition: all 0.3s ease; cursor: pointer; position: relative; }
    .nav-item:hover { background: rgba(250, 211, 112, 0.1); border-left: 3px solid var(--dorado); padding-left: calc(1.5rem - 3px); }
    .nav-item.active { background: rgba(250, 211, 112, 0.15); border-left: 3px solid var(--dorado); padding-left: calc(1.5rem - 3px); color: var(--dorado); }
    .nav-icon { font-size: 1.2rem; width: 24px; text-align: center; }
    .nav-text { flex: 1; font-size: 0.95rem; font-weight: 500; }
    .nav-badge { background: #ff4444; color: white; font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 10px; font-weight: 700; }

    /* VARIABLES DE TU DISEÑO PARA EL CENTRO */
    .empleado-container {
        --dorado: #fad370;
        --bg-card: #1a1a1a;
        --bg-secondary: #121212;
        --text-primary: #ffffff;
        --text-secondary: #a0a0a0;
        --border-color: #2a2a2a;
        --hover-bg: #252525;
        --shadow-lg: 0 8px 24px rgba(250, 211, 112, 0.2);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-family: 'Poppins', sans-serif;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Stats Grid */
    .empleado-container .emp-stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
    .empleado-container .emp-stat-card { background: var(--bg-card); padding: 1.5rem; border-radius: 15px; border: 1px solid var(--border-color); transition: var(--transition); position: relative; overflow: hidden; }
    .empleado-container .emp-stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: var(--dorado); }
    .empleado-container .emp-stat-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); }
    .empleado-container .emp-stat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .empleado-container .emp-stat-icon { width: 45px; height: 45px; border-radius: 10px; background: rgba(250, 211, 112, 0.15); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .empleado-container .emp-stat-value { font-size: 2rem; font-weight: 700; color: var(--dorado); margin-bottom: 0.3rem; }
    .empleado-container .emp-stat-label { font-size: 0.9rem; color: var(--text-secondary); }

    /* Navigation Tabs */
    .empleado-container .emp-nav-tabs { display: flex; gap: 4px; background: var(--bg-card); padding: 8px; border-radius: 12px; margin-bottom: 30px; border: 1px solid var(--border-color); width: fit-content;}
    .empleado-container .emp-nav-tab { padding: 12px 24px; border: none; background: none; color: var(--text-secondary); font-size: 14px; font-weight: 500; cursor: pointer; border-radius: 8px; transition: var(--transition); display: flex; align-items: center; gap: 8px; text-decoration: none; }
    .empleado-container .emp-nav-tab:hover { color: var(--text-primary); background: var(--hover-bg); }
    .empleado-container .emp-nav-tab.active { background: var(--dorado); color: #0a0a0a; font-weight: bold;}

    /* Filters */
    .empleado-container .emp-filters { display: flex; gap: 15px; margin-bottom: 25px; flex-wrap: wrap; }
    .empleado-container .emp-filter-select { padding: 12px 16px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; color: var(--text-primary); min-width: 200px; font-weight: 500; transition: var(--transition); }
    .empleado-container .emp-filter-select:focus { outline: none; border-color: var(--dorado); }

    /* Citas Grid */
    .empleado-container .emp-citas-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }
    .empleado-container .emp-cita-card { background: var(--bg-card); border-radius: 15px; padding: 20px; border: 1px solid var(--border-color); transition: var(--transition); position: relative; }
    .empleado-container .emp-cita-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); border-color: var(--dorado); }
    .empleado-container .emp-cita-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color); }
    
    .empleado-container .emp-cita-hora-container { display: flex; flex-direction: column; }
    .empleado-container .emp-cita-fecha { font-size: 0.8rem; color: var(--dorado); opacity: 0.8; font-weight: 600; text-transform: uppercase; }
    .empleado-container .emp-cita-hora { font-weight: 700; color: var(--text-primary); font-size: 24px; line-height: 1.1; }
    
    .empleado-container .emp-cita-estado { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
    .empleado-container .emp-estado-confirmada { background: rgba(74, 222, 128, 0.2); color: #4ade80; border: 1px solid #4ade80; }
    .empleado-container .emp-estado-pendiente { background: rgba(250, 211, 112, 0.2); color: var(--dorado); border: 1px solid var(--dorado); }
    .empleado-container .emp-estado-por-cobrar { background: rgba(33, 150, 243, 0.2); color: #2196f3; border: 1px solid #2196f3; }
    .empleado-container .emp-estado-progreso { background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid #f59e0b; }
    
    .empleado-container .emp-cita-cliente { font-weight: 600; margin-bottom: 8px; font-size: 18px; color: var(--text-primary); }
    .empleado-container .emp-cita-servicio { color: var(--text-secondary); margin-bottom: 12px; display: flex; align-items: center; gap: 6px; font-size: 14px;}
    .empleado-container .emp-cita-info { display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px; padding: 10px; background: var(--bg-secondary); border-radius: 8px; }
    .empleado-container .emp-cita-info-row { display: flex; justify-content: space-between; align-items: center;}
    .empleado-container .emp-cita-info-item { display: flex; align-items: center; gap: 6px; font-size: 14px; color: var(--text-secondary); }
    .empleado-container .emp-cita-precio { font-weight: 700; color: #4ade80; }
    .empleado-container .emp-cita-comision { font-weight: bold; color: var(--dorado); font-size: 15px;}
    
    .empleado-container .emp-cita-acciones { display: flex; gap: 10px; }
    .empleado-container .emp-btn { padding: 10px 20px; border: none; border-radius: 12px; cursor: pointer; font-weight: 600; transition: var(--transition); flex: 1; font-size: 14px; text-align: center;}
    .empleado-container .emp-btn-primary { background: var(--dorado); color: #0a0a0a; }
    .empleado-container .emp-btn-primary:hover { background: #f4c430; transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,0.4); }
    .empleado-container .emp-btn-secondary { background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border-color); }
    .empleado-container .emp-btn-secondary:hover { background: var(--hover-bg); border-color: var(--dorado); }
    
    /* BOTÓN DESHABILITADO */
    .empleado-container .emp-btn-disabled { background: #333333; color: #777777; cursor: not-allowed; border: 1px solid #444444; opacity: 0.6; }
    .empleado-container .emp-btn-disabled:hover { transform: none; box-shadow: none; background: #333333; color: #777777; border-color: #444444; }

    .empleado-container .emp-empty-state { grid-column: 1 / -1; text-align: center; padding: 60px 20px; }
    .empleado-container .emp-empty-icon { font-size: 80px; margin-bottom: 20px; opacity: 0.5; }
    .empleado-container .emp-empty-title { font-size: 24px; font-weight: 600; margin-bottom: 10px; color: var(--text-primary); }
    .empleado-container .emp-empty-text { color: var(--text-secondary); }

    /* Modal Premium */
    .emp-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.85); z-index: 1000; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(8px); font-family: 'Poppins', sans-serif; opacity: 0; transition: opacity 0.3s ease;}
    .emp-modal.active { display: flex; opacity: 1; }
    
    .emp-modal-content { background: linear-gradient(145deg, #1e1e1e, #121212); border-radius: 24px; padding: 0; width: 100%; max-width: 420px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7), 0 0 0 1px rgba(250, 211, 112, 0.1); transform: translateY(20px); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .emp-modal.active .emp-modal-content { transform: translateY(0); }

    .emp-modal-header { background: rgba(250, 211, 112, 0.05); padding: 20px 25px; border-bottom: 1px solid rgba(250, 211, 112, 0.1); display: flex; justify-content: space-between; align-items: center; }
    .emp-modal-title { font-family: 'Abril Fatface', cursive; font-size: 1.4rem; color: #ffffff; margin: 0; }
    
    .emp-close-btn { width: 30px; height: 30px; border-radius: 50%; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; font-size: 18px; cursor: pointer; color: #a0a0a0; transition: all 0.3s; }
    .emp-close-btn:hover { background: rgba(244, 67, 54, 0.2); color: #f44336; transform: rotate(90deg); }
    
    .emp-modal-body { padding: 25px; }

    .modern-client-card { display: flex; align-items: center; gap: 15px; background: #252525; padding: 15px; border-radius: 16px; margin-bottom: 20px; border: 1px solid #333;}
    .client-avatar { width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--dorado), #d4af37); color: #000; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px; box-shadow: 0 4px 10px rgba(250, 211, 112, 0.3);}
    
    .modern-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
    .info-pill { background: #2a2a2a; padding: 12px 15px; border-radius: 12px; display: flex; flex-direction: column; gap: 5px; border: 1px solid #333;}
    .info-pill-label { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 0.5px;}
    .info-pill-value { font-size: 14px; color: #fff; font-weight: 600;}
    
    .modern-service-list { background: #252525; border-radius: 16px; padding: 15px; margin-bottom: 20px; border: 1px solid #333;}
    
    .finance-card { background: linear-gradient(145deg, rgba(250, 211, 112, 0.1), rgba(250, 211, 112, 0.02)); border: 1px dashed var(--dorado); border-radius: 16px; padding: 20px; margin-bottom: 20px;}
    
    .emp-modal-actions { display: flex; gap: 12px;}

    @media (max-width: 1024px) { .empleado-container .emp-stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) { .empleado-container .emp-stats-grid { grid-template-columns: 1fr; } .empleado-container .emp-citas-grid { grid-template-columns: 1fr; } .empleado-container .emp-nav-tabs { flex-wrap: wrap; } }
</style>
@endpush

@section('sidebar-empleado')
<nav class="sidebar-nav">
    <div class="nav-section">
        <p class="nav-section-title">Principal</p>
        <a href="{{ route('empleado.dashboard') }}" class="nav-item {{ request()->routeIs('empleado.dashboard') || request()->is('empleado/dashboard') ? 'active' : '' }}">
            <span class="nav-icon">🏠</span><span class="nav-text">Mi Panel</span>
        </a>
    </div>
    
    <div class="nav-section">
        <p class="nav-section-title">Citas</p>
        <a href="{{ url('/empleado/Citas_pendients') }}" class="nav-item {{ request()->is('empleado/Citas_pendients') ? 'active' : '' }}">
            <span class="nav-icon">📋</span><span class="nav-text">Citas Pendientes</span>
            <span class="nav-badge">{{ $estadisticas['total_citas'] ?? 0 }}</span>
        </a>
        <a href="/empleado/Citas_atendidas" class="nav-item {{ request()->is('empleado/Citas_atendidas*') ? 'active' : '' }}">
            <span class="nav-icon">✅</span><span class="nav-text">Citas Atendidas</span>
        </a>
        <a href="/empleado/historial" class="nav-item {{ request()->is('empleado/historial*') ? 'active' : '' }}">
            <span class="nav-icon">📖</span><span class="nav-text">Historial Completo</span>
        </a>
    </div>

    <div class="nav-section">
        <p class="nav-section-title">Desempeño</p>
        <a href="/empleado/comisiones" class="nav-item {{ request()->is('empleado/comisiones*') ? 'active' : '' }}">
            <span class="nav-icon">💰</span><span class="nav-text">Mis Comisiones</span>
        </a>
        <a href="/empleado/calificaciones" class="nav-item {{ request()->is('empleado/calificaciones*') ? 'active' : '' }}">
            <span class="nav-icon">⭐</span><span class="nav-text">Calificaciones</span>
        </a>
    </div>

    <div class="nav-section">
        <p class="nav-section-title">Inventario</p>
        <a href="/empleado/reportar-falta" class="nav-item {{ request()->is('empleado/reportar-falta*') ? 'active' : '' }}">
            <span class="nav-icon">📦</span><span class="nav-text">Reportar Falta</span>
        </a>
        <a href="/empleado/ventas" class="nav-item {{ request()->is('empleado/ventas*') ? 'active' : '' }}">
    <span class="nav-icon">💵</span><span class="nav-text">Ventas</span>
</a>
    </div>

    <div class="nav-section">
        <p class="nav-section-title">Cuenta</p>
        <a href="/empleado/perfil" class="nav-item {{ request()->is('empleado/perfil*') ? 'active' : '' }}">
            <span class="nav-icon">👤</span><span class="nav-text">Mi Perfil</span>
        </a>
    </div>
</nav>
@endsection

@section('topbar-left')
    <h1 style="font-family: 'Abril Fatface', cursive; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.3rem;">Mis Citas</h1>
    <p style="color: var(--text-secondary); font-size: 0.95rem;">Gestiona tus próximas atenciones y comisiones.</p>
@endsection

@section('content')

<script type="application/json" id="citas-json">
    @json($citas_pendientes)
</script>

<div class="empleado-container">
    
    <div class="emp-stats-grid">
        <div class="emp-stat-card">
            <div class="emp-stat-header"><div class="emp-stat-icon">📅</div></div>
            <div class="emp-stat-value">{{ $estadisticas['total_citas'] }}</div>
            <div class="emp-stat-label">Total Activas</div>
        </div>
        <div class="emp-stat-card">
            <div class="emp-stat-header"><div class="emp-stat-icon">✅</div></div>
            <div class="emp-stat-value">{{ $estadisticas['confirmadas'] }}</div>
            <div class="emp-stat-label">Confirmadas (Listas)</div>
        </div>
        <div class="emp-stat-card">
            <div class="emp-stat-header"><div class="emp-stat-icon">⏳</div></div>
            <div class="emp-stat-value">{{ $estadisticas['pendientes'] }}</div>
            <div class="emp-stat-label">Falta Confirmación</div>
        </div>
        <div class="emp-stat-card" style="border-color: var(--dorado);">
            <div class="emp-stat-header"><div class="emp-stat-icon" style="background: var(--dorado); color: black;">💰</div></div>
            <div class="emp-stat-value">${{ number_format($estadisticas['ingresos_estimados'], 2) }}</div>
            <div class="emp-stat-label">Mi Comisión Estimada</div>
        </div>
    </div>
    
    <div class="emp-nav-tabs">
        <a href="{{ route('empleado.citas.pendientes') }}" class="emp-nav-tab active">📅 Activas</a>
        <a href="{{ url('/empleado/Citas_atendidas') }}" class="emp-nav-tab">✅ Atendidas</a>
    </div>
    
    <div class="emp-filters">
        <select class="emp-filter-select" id="filterEstado">
            <option value="">Todos los estados</option>
            <option value="en progreso">En Progreso</option>
            <option value="por cobrar">Por Cobrar en Caja</option>
            <option value="confirmada">Confirmadas</option>
            <option value="pendiente">Pendientes</option>
        </select>
    </div>
    
    <div class="emp-citas-grid" id="citasContainer">
        @forelse ($citas_pendientes as $cita)
            @php
                $servicioFiltro = strtolower(explode(' ', $cita['servicio'] ?? 'general')[0]);
                
                $badgeClass = 'emp-estado-pendiente';
                $estadoLower = strtolower($cita['estado']);
                
                if($estadoLower === 'confirmada') $badgeClass = 'emp-estado-confirmada';
                if($estadoLower === 'en progreso') $badgeClass = 'emp-estado-progreso'; 
                if($estadoLower === 'por cobrar') $badgeClass = 'emp-estado-por-cobrar'; 
            @endphp
            
            <div class="emp-cita-card" data-estado="{{ $estadoLower }}" data-servicio="{{ $servicioFiltro }}">
                <div class="emp-cita-header">
                    <div class="emp-cita-hora-container">
                        <div class="emp-cita-fecha">
                            @if($cita['es_hoy'])
                                <span style="color: #4ade80;">🔴 HOY</span>
                            @else
                                {{ $cita['fecha_texto'] }}
                            @endif
                        </div>
                        <div class="emp-cita-hora">{{ $cita['hora'] }}</div>
                    </div>
                    <span class="emp-cita-estado {{ $badgeClass }}">
                        {{ ucfirst($cita['estado']) }}
                    </span>
                </div>
                
                <div class="emp-cita-cliente">👤 {{ $cita['cliente'] }}</div>
                <div class="emp-cita-servicio">✂️ {{ Str::limit($cita['servicio'], 30) }}</div>
                
                <div class="emp-cita-info">
                    <div class="emp-cita-info-row">
                        <div class="emp-cita-info-item"><span>⏱️</span><span>{{ $cita['duracion'] }} min</span></div>
                        <div class="emp-cita-info-item"><span>Total Salón:</span><span class="emp-cita-precio">${{ number_format($cita['precio'], 2) }}</span></div>
                    </div>
                    <div class="emp-cita-info-row" style="border-top: 1px dashed var(--border-color); padding-top: 5px; margin-top: 5px;">
                        <div class="emp-cita-info-item"><span style="color: var(--dorado);">Mi Comisión:</span></div>
                        <div class="emp-cita-comision">+ ${{ number_format($cita['comision'], 2) }}</div>
                    </div>
                </div>
                
                <div class="emp-cita-acciones">
                    @if($estadoLower === 'en progreso')
                        <button class="emp-btn" 
                                style="background: #f59e0b; color: #000; font-weight: bold;" 
                                data-id="{{ $cita['id'] }}"
                                data-estado="Por Cobrar"
                                onclick="cambiarEstadoCita(this)">🏁 Finalizar en Silla</button>
                    @elseif($estadoLower === 'por cobrar')
                        <button class="emp-btn emp-btn-disabled" disabled>💸 Esperando pago en Caja</button>
                    @elseif($estadoLower !== 'confirmada')
                        <button class="emp-btn emp-btn-disabled" disabled title="Esperando que la recepción o el cliente confirmen">⏳ Falta Confirmar</button>
                    @elseif(!$cita['es_hoy'])
                        <button class="emp-btn emp-btn-disabled" disabled title="Solo puedes iniciar citas programadas para hoy">📅 Inicia el {{ $cita['fecha_texto'] }}</button>
                    @else
                        <button class="emp-btn emp-btn-primary" 
                                data-id="{{ $cita['id'] }}"
                                data-estado="En Progreso"
                                onclick="cambiarEstadoCita(this)">▶️ Iniciar Atención</button>
                    @endif
                    
                    <button class="emp-btn emp-btn-secondary" 
                            style="flex: 0.3;" 
                            data-id="{{ $cita['id'] }}"
                            onclick="verDetalles(this)" 
                            title="Ver Detalles">👁️</button>
                </div>
            </div>
        @empty
            <div class="emp-empty-state">
                <div class="emp-empty-icon">📅</div>
                <h3 class="emp-empty-title">No hay citas programadas</h3>
                <p class="emp-empty-text">No tienes citas pendientes asignadas en este momento.</p>
            </div>
        @endforelse
    </div>
</div>

<div class="emp-modal" id="detallesModal">
    <div class="emp-modal-content">
        <div class="emp-modal-header">
            <h2 class="emp-modal-title">Detalles del Servicio</h2>
            <div class="emp-close-btn" onclick="closeModal()">×</div>
        </div>
        
        <div class="emp-modal-body" id="modalBody"></div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const citasData = JSON.parse(document.getElementById('citas-json').textContent);

    document.getElementById('filterEstado').addEventListener('change', filtrarCitas);
    
    function filtrarCitas() {
        const estado = document.getElementById('filterEstado').value.toLowerCase();
        const citas = document.querySelectorAll('.emp-cita-card');
        
        citas.forEach(cita => {
            const citaEstado = cita.dataset.estado;
            let mostrar = true;
            if (estado && citaEstado !== estado) mostrar = false;
            cita.style.display = mostrar ? 'block' : 'none';
        });
    }

    function cambiarEstadoCita(btn) {
        const id = parseInt(btn.getAttribute('data-id'));
        const nuevoEstado = btn.getAttribute('data-estado');
        
        let titulo = nuevoEstado === 'En Progreso' ? '¿Iniciar atención?' : '¿Finalizar servicio?';
        let texto = nuevoEstado === 'En Progreso' 
            ? "El estado cambiará y se notificará a recepción que el cliente está en tu silla." 
            : "La cita pasará a caja. Recuerda indicarle al recepcionista si el cliente lleva algún producto extra.";
        let colorBtn = nuevoEstado === 'En Progreso' ? '#fad370' : '#f59e0b';

        Swal.fire({
            title: titulo,
            text: texto,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: colorBtn,
            cancelButtonColor: '#333',
            confirmButtonText: '<span style="color:#000; font-weight:bold;">Sí, confirmar</span>',
            cancelButtonText: 'Cancelar',
            background: '#1a1a1a',
            color: '#fff'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    Swal.fire({title: 'Actualizando...', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
                    
                    const response = await fetch(`/empleado/citas/${id}/estado`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ estado: nuevoEstado })
                    });

                    const data = await response.json();
                    
                    if (response.ok && data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Actualizado!',
                            text: nuevoEstado === 'En Progreso' ? '¡A trabajar!' : 'Cliente enviado a caja.',
                            background: '#1a1a1a', color: '#fff', confirmButtonColor: '#fad370'
                        }).then(() => {
                            window.location.reload(); 
                        });
                    } else {
                        Swal.fire({
                            icon: 'error', 
                            title: 'Error', 
                            text: data.message || 'Error al actualizar',
                            background: '#1a1a1a', color: '#fff'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error', 
                        title: 'Error', 
                        text: 'Falla de conexión',
                        background: '#1a1a1a', color: '#fff'
                    });
                }
            }
        });
    }
    
    function verDetalles(btn) {
        const citaId = parseInt(btn.getAttribute('data-id'));
        const cita = citasData.find(c => c.id === citaId);
        if(!cita) return;

        const words = cita.cliente.trim().split(' ');
        let initials = '';
        if (words.length > 1) {
            initials = words[0][0] + words[1][0];
        } else {
            initials = words[0].substring(0, 2);
        }
        initials = initials.toUpperCase();

        const serviciosArray = cita.servicio.split('+');
        let serviciosHtml = '';
        serviciosArray.forEach(srv => {
            serviciosHtml += `<div style="margin-bottom: 6px; display:flex; align-items:center; gap:8px;">
                                <span style="color:var(--dorado); font-size:10px;">✦</span> ${srv.trim()}
                              </div>`;
        });

        const modalBody = document.getElementById('modalBody');
        modalBody.innerHTML = `
            <div class="modern-client-card">
                <div class="client-avatar">${initials}</div>
                <div>
                    <div style="color: #fff; font-weight: 600; font-size: 18px;">${cita.cliente}</div>
                    <div style="color: #888; font-size: 13px; margin-top:2px;">📱 ${cita.telefono}</div>
                </div>
            </div>

            <div class="modern-info-grid">
                <div class="info-pill">
                    <span class="info-pill-label">📅 Fecha Programada</span>
                    <span class="info-pill-value">${cita.fecha_texto}</span>
                </div>
                <div class="info-pill">
                    <span class="info-pill-label">🕐 Hora Inicio</span>
                    <span class="info-pill-value">${cita.hora}</span>
                </div>
            </div>

            <div class="modern-service-list">
                <div style="font-size: 11px; color: #888; text-transform: uppercase; margin-bottom: 12px; letter-spacing: 1px;">✂️ Trabajo a realizar</div>
                <div style="color: #fff; font-size: 15px; font-weight: 500;">
                    ${serviciosHtml}
                </div>
                <div style="margin-top: 15px; font-size: 13px; color: #a0a0a0; border-top: 1px solid #333; padding-top:10px;">
                    ⏱️ Tiempo estimado: <strong style="color:white;">${cita.duracion} min</strong>
                </div>
            </div>

            <div class="finance-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="color: #ccc; font-size: 13px;">Total cobrado al cliente:</span>
                    <span style="color: #fff; font-weight: bold; font-size: 16px;">$${parseFloat(cita.precio).toFixed(2)}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(250, 211, 112, 0.2); padding-top: 15px;">
                    <span style="color: var(--dorado); font-size: 15px; font-weight: 600;">Tu Comisión:</span>
                    <span style="color: var(--dorado); font-size: 24px; font-weight: 900;">+$${parseFloat(cita.comision).toFixed(2)}</span>
                </div>
            </div>

            <div class="emp-modal-actions">
                <button class="emp-btn emp-btn-secondary emp-btn-full" onclick="closeModal()">Cerrar Detalles</button>
            </div>
        `;
        
        document.getElementById('detallesModal').classList.add('active');
    }
    
    function closeModal() {
        document.querySelectorAll('.emp-modal').forEach(modal => modal.classList.remove('active'));
    }

    document.getElementById('detallesModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endsection