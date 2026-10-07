@extends('layouts.app')

@section('title', 'Citas Atendidas - StyleNow')

@push('styles')
<style>
    /* Estilos del sidebar-nav (IGUAL QUE CITAS PENDIENTES) */
    .sidebar-nav { padding: 2rem 0; }
    .nav-section { margin-bottom: 1.5rem; }
    .nav-section-title { padding: 0 1.5rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 700; }
    .nav-item { display: flex; align-items: center; gap: 1rem; padding: 0.9rem 1.5rem; color: var(--text-primary); text-decoration: none; transition: all 0.3s ease; cursor: pointer; position: relative; }
    .nav-item:hover { background: rgba(250, 211, 112, 0.1); border-left: 3px solid var(--dorado); padding-left: calc(1.5rem - 3px); }
    .nav-item.active { background: rgba(250, 211, 112, 0.15); border-left: 3px solid var(--dorado); padding-left: calc(1.5rem - 3px); color: var(--dorado); }
    .nav-icon { font-size: 1.2rem; width: 24px; text-align: center; }
    .nav-text { flex: 1; font-size: 0.95rem; font-weight: 500; }

    /* VARIABLES Y CONTENEDOR */
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
    .empleado-container .emp-nav-tabs { display: flex; gap: 4px; background: var(--bg-card); padding: 8px; border-radius: 12px; margin-bottom: 30px; border: 1px solid var(--border-color); }
    .empleado-container .emp-nav-tab { padding: 12px 24px; border: none; background: none; color: var(--text-secondary); font-size: 14px; font-weight: 500; cursor: pointer; border-radius: 8px; transition: var(--transition); display: flex; align-items: center; gap: 8px; text-decoration: none; }
    .empleado-container .emp-nav-tab:hover { color: var(--text-primary); background: var(--hover-bg); }
    .empleado-container .emp-nav-tab.active { background: var(--dorado); color: #0a0a0a; font-weight: bold;}

    /* Filters */
    .empleado-container .emp-filters { display: flex; gap: 15px; margin-bottom: 25px; flex-wrap: wrap; }
    .empleado-container .emp-filter-input, .empleado-container .emp-filter-select { padding: 12px 16px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; color: var(--text-primary); min-width: 200px; font-weight: 500; transition: var(--transition); outline: none;}
    .empleado-container .emp-filter-input:focus, .empleado-container .emp-filter-select:focus { border-color: var(--dorado); }

    /* Tabla */
    .empleado-container .content-card { background: var(--bg-card); border-radius: 15px; padding: 0; border: 1px solid var(--border-color); overflow: hidden; }
    .empleado-container .tabla-header { display: grid; grid-template-columns: 120px 1fr 200px 100px 150px 150px; padding: 20px; background: var(--bg-secondary); font-weight: 600; border-bottom: 2px solid var(--border-color); font-size: 14px; color: var(--text-secondary); }
    .empleado-container .tabla-fila { display: grid; grid-template-columns: 120px 1fr 200px 100px 150px 150px; padding: 20px; border-bottom: 1px solid var(--border-color); transition: var(--transition); align-items: center; }
    .empleado-container .tabla-fila:hover { background: var(--hover-bg); border-left: 3px solid var(--dorado); padding-left: 17px; }
    .empleado-container .tabla-fila:last-child { border-bottom: none; }

    .empleado-container .fecha-col { font-weight: 600; color: var(--text-primary); }
    .empleado-container .cliente-col { font-weight: 600; color: var(--text-primary); font-size: 15px;}
    .empleado-container .servicio-col { color: var(--text-secondary); font-size: 13px;}
    .empleado-container .duracion-col { color: var(--text-secondary); font-size: 14px; }
    .empleado-container .valoracion { color: var(--dorado); font-size: 16px; letter-spacing: 2px; }
    .empleado-container .ingreso-col { text-align: right; }
    .empleado-container .ingreso { font-weight: 700; color: #4ade80; font-size: 18px; }

    .empleado-container .emp-empty-state { text-align: center; padding: 60px 20px; }
    .empleado-container .emp-empty-icon { font-size: 80px; margin-bottom: 20px; opacity: 0.5; }
    .empleado-container .emp-empty-title { font-size: 24px; font-weight: 600; margin-bottom: 10px; color: var(--text-primary); }

    @media (max-width: 1024px) { 
        .empleado-container .emp-stats-grid { grid-template-columns: repeat(2, 1fr); } 
        .empleado-container .tabla-header, .empleado-container .tabla-fila { grid-template-columns: 100px 1fr 150px 80px 100px 100px; }
    }
    @media (max-width: 768px) { 
        .empleado-container .emp-stats-grid { grid-template-columns: 1fr; } 
        .empleado-container .tabla-header { display: none; }
        .empleado-container .tabla-fila { grid-template-columns: 1fr; gap: 10px; border-left: 3px solid var(--dorado); padding-left: 17px; margin-bottom: 10px; background: var(--bg-secondary); border-radius: 10px;}
        .empleado-container .ingreso-col { text-align: left; margin-top: 10px;}
    }
</style>
@endpush

{{-- SECCIÓN DEL MENÚ LATERAL --}}
@section('sidebar-empleado')
<nav class="sidebar-nav">
    <div class="nav-section">
        <p class="nav-section-title">Principal</p>
        <a href="{{ route('empleado.dashboard') }}" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span class="nav-text">Mi Panel</span>
        </a>
    </div>

    <div class="nav-section">
        <p class="nav-section-title">Citas</p>
        <a href="{{ url('/empleado/Citas_pendients') }}" class="nav-item">
            <span class="nav-icon">📋</span>
            <span class="nav-text">Citas Pendientes</span>
        </a>

        <a href="/empleado/Citas_atendidas" class="nav-item active">
            <span class="nav-icon">✅</span>
            <span class="nav-text">Citas Atendidas</span>
        </a>

        <a href="/empleado/historial" class="nav-item">
            <span class="nav-icon">📖</span>
            <span class="nav-text">Historial Completo</span>
        </a>
    </div>

    <div class="nav-section">
        <p class="nav-section-title">Desempeño</p>
        <a href="/empleado/comisiones" class="nav-item">
            <span class="nav-icon">💰</span>
            <span class="nav-text">Mis Comisiones</span>
        </a>
        <a href="/empleado/calificaciones" class="nav-item">
            <span class="nav-icon">⭐</span>
            <span class="nav-text">Calificaciones</span>
        </a>
    </div>

    <div class="nav-section">
        <p class="nav-section-title">Inventario</p>
        <a href="/empleado/reportar-falta" class="nav-item {{ request()->is('empleado/reportar-falta*') ? 'active' : '' }}">
            <span class="nav-icon">📦</span>
            <span class="nav-text">Reportar Falta</span>
        </a>
        <a href="/empleado/ventas" class="nav-item {{ request()->is('empleado/ventas*') ? 'active' : '' }}">
    <span class="nav-icon">💵</span><span class="nav-text">Ventas</span>
</a>
    </div>

    <div class="nav-section">
        <p class="nav-section-title">Cuenta</p>
        <a href="/empleado/perfil" class="nav-item">
            <span class="nav-icon">👤</span>
            <span class="nav-text">Mi Perfil</span>
        </a>
    </div>
</nav>
@endsection

@section('topbar-left')
    <h1 style="font-family: 'Abril Fatface', cursive; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.3rem;">Citas Atendidas</h1>
    <p style="color: var(--text-secondary); font-size: 0.95rem;">Historial de servicios que ya completaste</p>
@endsection

@section('content')
<div class="empleado-container">
    
    <div class="emp-stats-grid">
        <div class="emp-stat-card">
            <div class="emp-stat-header"><div class="emp-stat-icon">✅</div></div>
            <div class="emp-stat-value">{{ $estadisticas['total_citas'] }}</div>
            <div class="emp-stat-label">Citas Atendidas</div>
        </div>
        
        <div class="emp-stat-card">
            <div class="emp-stat-header"><div class="emp-stat-icon" style="background: var(--dorado); color: black;">💰</div></div>
            <div class="emp-stat-value">${{ number_format($estadisticas['comisiones_totales'], 2) }}</div>
            <div class="emp-stat-label">Mis Comisiones Ganadas</div>
        </div>
        
        <div class="emp-stat-card">
            <div class="emp-stat-header"><div class="emp-stat-icon">💵</div></div>
            <div class="emp-stat-value">${{ number_format($estadisticas['propinas'], 2) }}</div>
            <div class="emp-stat-label">Propinas</div>
        </div>
        
        <div class="emp-stat-card">
            <div class="emp-stat-header"><div class="emp-stat-icon">⭐</div></div>
            <div class="emp-stat-value">{{ number_format($estadisticas['valoracion_promedio'], 1) }}/5</div>
            <div class="emp-stat-label">Valoración Promedio</div>
        </div>
    </div>
    
    <div class="emp-nav-tabs">
        <a href="{{ url('/empleado/Citas_pendients') }}" class="emp-nav-tab">📅 Pendientes</a>
        <a href="{{ url('/empleado/Citas_atendidas') }}" class="emp-nav-tab active">✅ Atendidas</a>
    </div>
    
    <div class="emp-filters">
        <input type="date" class="emp-filter-input" id="filtroFecha">
        <input type="text" class="emp-filter-input" id="filtroCliente" placeholder="🔍 Buscar por cliente...">
    </div>
    
    <div class="content-card">
        <div class="tabla-header">
            <div>Fecha / Hora</div>
            <div>Cliente</div>
            <div>Servicios Realizados</div>
            <div>Duración</div>
            <div>Valoración</div>
            <div style="text-align: right;">Mi Comisión</div>
        </div>
        
        <div id="contenedorFilas">
            @forelse ($citasAtendidas as $cita)
                <div class="tabla-fila" data-fecha="{{ $cita['fecha_formato_input'] }}">
                    <div class="fecha-col">
                        {{ $cita['fecha_texto'] }} <br>
                        <small style="color:var(--text-secondary); font-weight:normal;">{{ $cita['hora'] }}</small>
                    </div>
                    <div class="cliente-col">
                        {{ $cita['cliente'] }}
                    </div>
                    <div class="servicio-col">
                        {{ Str::limit($cita['servicio'], 45) }}
                    </div>
                    <div class="duracion-col">
                        ⏱️ {{ $cita['duracion'] }} min
                    </div>
                    <div class="valoracion">
                        {!! str_repeat('★', floor($cita['valoracion'])) . str_repeat('<span style="color:#444;">☆</span>', 5 - floor($cita['valoracion'])) !!}
                    </div>
                    <div class="ingreso-col">
                        <div class="ingreso">+ ${{ number_format($cita['comision'], 2) }}</div>
                    </div>
                </div>
            @empty
                <div class="emp-empty-state">
                    <div class="emp-empty-icon">✅</div>
                    <h3 class="emp-empty-title">No hay citas atendidas aún</h3>
                    <p class="emp-empty-text" style="color: var(--text-secondary);">Tus comisiones aparecerán aquí una vez que completes los servicios.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Filtrado por Cliente
    document.getElementById('filtroCliente').addEventListener('input', function(e) {
        const search = e.target.value.toLowerCase();
        const filas = document.querySelectorAll('.tabla-fila');
        
        filas.forEach(fila => {
            const cliente = fila.querySelector('.cliente-col').textContent.toLowerCase();
            fila.style.display = cliente.includes(search) ? 'grid' : 'none';
        });
    });
    
    // Filtrado por Fecha Exacta
    document.getElementById('filtroFecha').addEventListener('change', function(e) {
        const fechaBuscada = e.target.value; // Formato YYYY-MM-DD
        const filas = document.querySelectorAll('.tabla-fila');
        
        filas.forEach(fila => {
            const fechaFila = fila.dataset.fecha;
            if (!fechaBuscada) {
                fila.style.display = 'grid'; // Mostrar todo si se borra la fecha
            } else {
                fila.style.display = fechaFila === fechaBuscada ? 'grid' : 'none';
            }
        });
    });
</script>
@endsection