@extends('layouts.app')

@section('title', 'Historial Completo - StyleNow')

@push('styles')
<style>
    /* Estilos del sidebar-nav (Compartido) */
    .sidebar-nav { padding: 2rem 0; }
    .nav-section { margin-bottom: 1.5rem; }
    .nav-section-title { padding: 0 1.5rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 700; }
    .nav-item { display: flex; align-items: center; gap: 1rem; padding: 0.9rem 1.5rem; color: var(--text-primary); text-decoration: none; transition: all 0.3s ease; cursor: pointer; position: relative; }
    .nav-item:hover { background: rgba(250, 211, 112, 0.1); border-left: 3px solid var(--dorado); padding-left: calc(1.5rem - 3px); }
    .nav-item.active { background: rgba(250, 211, 112, 0.15); border-left: 3px solid var(--dorado); padding-left: calc(1.5rem - 3px); color: var(--dorado); }
    .nav-icon { font-size: 1.2rem; width: 24px; text-align: center; }
    .nav-text { flex: 1; font-size: 0.95rem; font-weight: 500; }

    /* VARIABLES DE DISEÑO */
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

    /* Pestañas de Navegación */
    .empleado-container .emp-nav-tabs { display: flex; gap: 4px; background: var(--bg-card); padding: 8px; border-radius: 12px; margin-bottom: 25px; border: 1px solid var(--border-color); width: fit-content; }
    .empleado-container .emp-nav-tab { padding: 12px 24px; border: none; background: none; color: var(--text-secondary); font-size: 14px; font-weight: 500; cursor: pointer; border-radius: 8px; transition: var(--transition); display: flex; align-items: center; gap: 8px; text-decoration: none; }
    .empleado-container .emp-nav-tab:hover { color: var(--text-primary); background: var(--hover-bg); }
    .empleado-container .emp-nav-tab.active { background: var(--dorado); color: #0a0a0a; font-weight: bold;}

    /* Filtros de Tiempo (Visuales) */
    .time-filters { display: flex; gap: 10px; margin-bottom: 20px; }
    .time-filter-btn { padding: 8px 20px; background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-secondary); border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.3s; }
    .time-filter-btn:hover { border-color: var(--dorado); color: var(--text-primary); }
    .time-filter-btn.active { background: var(--dorado); color: #000; border-color: var(--dorado); }

    /* Stats Grid (Estilo Historial) */
    .historial-stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; margin-bottom: 15px; }
    .historial-stats-grid-bottom { display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; margin-bottom: 30px; }
    .h-stat-card { background: var(--bg-card); padding: 25px; border-radius: 15px; border: 1px solid var(--border-color); position: relative; }
    .h-stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: var(--dorado); }
    .h-stat-value { font-size: 2.5rem; font-weight: 700; color: var(--dorado); margin-bottom: 5px; line-height: 1; }
    .h-stat-label { font-size: 14px; color: var(--text-primary); font-weight: 600; margin-bottom: 5px; }
    .h-stat-sub { font-size: 12px; color: #4ade80; }
    .h-stat-sub.neutral { color: var(--text-secondary); }

    /* Sección de Tabla Mensual */
    .section-card { background: var(--bg-card); border-radius: 15px; border: 1px solid var(--border-color); margin-bottom: 30px; overflow: hidden; }
    .section-header { padding: 20px 25px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px; font-size: 18px; font-weight: 600; color: var(--text-primary); }
    
    table { width: 100%; border-collapse: collapse; text-align: left; }
    th { padding: 15px 25px; font-size: 13px; color: var(--text-muted); font-weight: 600; background: var(--bg-secondary); border-bottom: 1px solid var(--border-color); }
    td { padding: 15px 25px; font-size: 14px; color: var(--text-secondary); border-bottom: 1px solid var(--border-color); }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: var(--hover-bg); }
    
    .text-white { color: var(--text-primary); font-weight: 600; }
    .text-gold { color: var(--dorado); font-weight: bold; }
    .rating-stars { color: var(--dorado); font-size: 14px; letter-spacing: 2px; }

    /* Servicios Más Realizados */
    .top-services-container { padding: 25px; display: flex; flex-direction: column; gap: 20px; }
    .top-service-row { display: flex; align-items: center; gap: 15px; }
    .ts-icon { width: 40px; height: 40px; background: rgba(250, 211, 112, 0.1); color: var(--dorado); border-radius: 10px; display: flex; justify-content: center; align-items: center; font-size: 18px; }
    .ts-name { width: 200px; font-weight: 600; color: var(--text-primary); font-size: 15px; }
    .ts-bar-container { flex: 1; height: 8px; background: var(--bg-secondary); border-radius: 10px; overflow: hidden; }
    .ts-bar { height: 100%; background: var(--dorado); border-radius: 10px; transition: width 1s ease-in-out; }
    .ts-count { font-weight: bold; color: var(--text-primary); font-size: 16px; width: 40px; text-align: right; }

    @media (max-width: 1024px) { 
        .historial-stats-grid, .historial-stats-grid-bottom { grid-template-columns: repeat(3, 1fr); } 
    }
    @media (max-width: 768px) { 
        .historial-stats-grid, .historial-stats-grid-bottom { grid-template-columns: 1fr; }
        .ts-name { width: 120px; font-size: 13px; }
        .section-card { overflow-x: auto; }
    }
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
        </a>
        <a href="{{ url('/empleado/Citas_atendidas') }}" class="nav-item {{ request()->is('empleado/Citas_atendidas*') ? 'active' : '' }}">
            <span class="nav-icon">✅</span><span class="nav-text">Citas Atendidas</span>
        </a>
        <a href="{{ route('empleado.historial') }}" class="nav-item active">
            <span class="nav-icon">📖</span><span class="nav-text">Historial Completo</span>
        </a>
    </div>
    
    <div class="nav-section">
        <p class="nav-section-title">Desempeño</p>
        <a href="{{ route('empleado.comisiones') }}" class="nav-item {{ request()->is('empleado/comisiones*') ? 'active' : '' }}">
            <span class="nav-icon">💰</span><span class="nav-text">Mis Comisiones</span>
        </a>
        <a href="{{ route('empleado.calificaciones') }}" class="nav-item {{ request()->is('empleado/calificaciones*') ? 'active' : '' }}">
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
    <h1 style="font-family: 'Abril Fatface', cursive; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.3rem;">Historial Completo</h1>
    <p style="color: var(--text-secondary); font-size: 0.95rem;">Estadísticas y análisis de tu desempeño global</p>
@endsection

@section('content')
<div class="empleado-container">

    <div class="emp-nav-tabs">
        <a href="{{ url('/empleado/Citas_pendients') }}" class="emp-nav-tab">📅 Pendientes</a>
        <a href="{{ url('/empleado/Citas_atendidas') }}" class="emp-nav-tab">✅ Atendidas</a>
        <a href="{{ route('empleado.historial') }}" class="emp-nav-tab active">📊 Historial</a>
    </div>

    <div class="time-filters">
        <button class="time-filter-btn active">Todo el Historial</button>
        <button class="time-filter-btn">Este Año</button>
    </div>

    <div class="historial-stats-grid">
        <div class="h-stat-card">
            <div class="h-stat-value">{{ $estadisticas['total_citas'] }}</div>
            <div class="h-stat-label">Citas Totales</div>
            <div class="h-stat-sub neutral">Histórico global</div>
        </div>
        <div class="h-stat-card">
            <div class="h-stat-value">${{ number_format($estadisticas['comisiones_totales'], 2) }}</div>
            <div class="h-stat-label">Mis Comisiones</div>
            <div class="h-stat-sub neutral">Ganancia neta acumulada</div>
        </div>
        <div class="h-stat-card">
            <div class="h-stat-value">${{ number_format($estadisticas['propinas_totales'], 2) }}</div>
            <div class="h-stat-label">Propinas Totales</div>
            <div class="h-stat-sub neutral">Ingreso extra</div>
        </div>
        <div class="h-stat-card">
            <div class="h-stat-value">{{ number_format($estadisticas['valoracion_promedio'], 1) }}/5</div>
            <div class="h-stat-label">Valoración Promedio</div>
            <div class="h-stat-sub neutral">Calificación de clientes</div>
        </div>
        <div class="h-stat-card">
            <div class="h-stat-value">{{ $estadisticas['clientes_unicos'] }}</div>
            <div class="h-stat-label">Clientes Únicos</div>
            <div class="h-stat-sub neutral">Fidelización</div>
        </div>
    </div>

    <div class="historial-stats-grid-bottom">
        <div class="h-stat-card" style="grid-column: span 1;">
            <div class="h-stat-value">{{ $estadisticas['horas_trabajadas'] }}h</div>
            <div class="h-stat-label">Horas Trabajadas</div>
            <div class="h-stat-sub neutral">En atención activa</div>
        </div>
    </div>

    <div class="section-card">
        <div class="section-header">
            <span>📅</span> Historial Mensual
        </div>
        <table>
            <thead>
                <tr>
                    <th>Mes</th>
                    <th>Citas</th>
                    <th>Comisiones</th>
                    <th>Propinas</th>
                    <th>Valoración</th>
                    <th>Servicio Top</th>
                    <th>Horas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tablaMensual as $row)
                <tr>
                    <td class="text-white">{{ $row['mes'] }}</td>
                    <td>{{ $row['citas'] }}</td>
                    <td class="text-gold">${{ number_format($row['comisiones'], 2) }}</td>
                    <td class="text-white">${{ number_format($row['propinas'], 2) }}</td>
                    <td class="rating-stars">{{ number_format($row['valoracion'], 1) }}/5 ⭐</td>
                    <td>{{ Str::limit($row['servicio_top'], 25) }}</td>
                    <td>{{ $row['horas'] }}h</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px;">No hay historial registrado aún.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section-card">
        <div class="section-header">
            <span>🏆</span> Servicios Más Realizados
        </div>
        <div class="top-services-container">
            @if(count($topServicios) > 0)
                @php
                    $maxServiceCount = max($topServicios);
                @endphp
                
                @foreach($topServicios as $nombreServicio => $cantidad)
                    @php
                        $porcentaje = ($cantidad / $maxServiceCount) * 100;
                    @endphp
                    <div class="top-service-row">
                        <div class="ts-icon">✂️</div>
                        <div class="ts-name">{{ Str::limit($nombreServicio, 25) }}</div>
                        <div class="ts-bar-container">
    <div class="ts-bar" @style(['width: ' . $porcentaje . '%'])></div>
</div>
                        <div class="ts-count">{{ $cantidad }}</div>
                    </div>
                @endforeach
            @else
                <p style="text-align: center; color: var(--text-muted); padding: 20px;">No hay datos de servicios suficientes.</p>
            @endif
        </div>
    </div>

</div>
@endsection