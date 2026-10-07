@extends('layouts.app')

@section('title', 'Mis Comisiones - StyleNow')

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

    /* VARIABLES Y CONTENEDOR */
    .empleado-container {
        --dorado: #fad370;
        --bg-card: #1a1a1a;
        --bg-secondary: #121212;
        --text-primary: #ffffff;
        --text-secondary: #a0a0a0;
        --border-color: #2a2a2a;
        --hover-bg: #252525;
        font-family: 'Poppins', sans-serif;
        max-width: 1000px; /* Centrado y enfocado según la imagen */
        margin: 0 auto;
    }

    /* Pestañas de Navegación Locales */
    .empleado-container .emp-nav-tabs { display: flex; gap: 4px; background: var(--bg-card); padding: 6px; border-radius: 12px; margin-bottom: 25px; border: 1px solid var(--border-color); width: fit-content; }
    .empleado-container .emp-nav-tab { padding: 10px 20px; border: none; background: none; color: var(--text-secondary); font-size: 13px; font-weight: 600; cursor: pointer; border-radius: 8px; transition: 0.3s; display: flex; align-items: center; gap: 8px; text-decoration: none; }
    .empleado-container .emp-nav-tab:hover { color: var(--text-primary); }
    .empleado-container .emp-nav-tab.active { background: rgba(250, 211, 112, 0.1); border: 1px solid var(--dorado); color: var(--dorado); }

    /* TARJETA DORADA DE METAS */
    .gold-target-card {
        background: linear-gradient(145deg, rgba(250, 211, 112, 0.1), rgba(250, 211, 112, 0.02));
        border: 1px solid rgba(250, 211, 112, 0.4);
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 30px;
    }
    
    .gold-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px; text-align: center; }
    .gold-item-value { font-size: 2.2rem; font-weight: bold; color: var(--dorado); line-height: 1.2; margin-bottom: 5px; }
    .gold-item-label { font-size: 0.8rem; color: #d1d5db; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;}
    
    /* Progress Bar */
    .progress-wrapper { width: 100%; }
    .progress-bar { width: 100%; height: 8px; background: rgba(255, 255, 255, 0.1); border-radius: 4px; overflow: hidden; margin-bottom: 10px; }
    .progress-fill { height: 100%; background: var(--dorado); border-radius: 4px; transition: width 1s ease-in-out; }
    .progress-labels { display: flex; justify-content: space-between; font-size: 0.8rem; color: #9ca3af; font-weight: 500;}

    /* PANELES OSCUROS (Historial y Metas) */
    .dark-panel {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 30px;
    }
    .panel-title { font-size: 1.1rem; font-weight: 600; color: var(--text-primary); display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color); }

    /* Listado de Historial */
    .history-row { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; border-bottom: 1px solid var(--border-color); transition: background 0.3s; }
    .history-row:last-child { border-bottom: none; padding-bottom: 0; }
    .history-month { font-weight: 600; font-size: 1.1rem; color: var(--text-primary); width: 150px; }
    
    .history-details { flex: 1; display: flex; flex-direction: column; gap: 4px; }
    .detail-sales { font-size: 0.9rem; color: var(--text-secondary); }
    .detail-rate { font-size: 0.85rem; color: var(--dorado); font-weight: 500; }
    
    .history-status-col { text-align: right; margin-right: 30px; }
    .badge-status { padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; display: inline-block; }
    .status-pendiente { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
    .status-pagado { background: rgba(74, 222, 128, 0.15); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.3); }
    .status-date { font-size: 0.75rem; color: var(--text-secondary); }
    
    .history-amount { font-size: 1.5rem; font-weight: 800; color: var(--dorado); min-width: 120px; text-align: right; }

    /* Metas y Bonos */
    .bonus-row { display: flex; justify-content: space-between; align-items: center; padding: 15px; background: var(--bg-secondary); border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 15px; }
    .bonus-info { display: flex; align-items: center; gap: 15px; }
    .bonus-icon { width: 40px; height: 40px; background: rgba(255,255,255,0.05); border-radius: 10px; display: flex; justify-content: center; align-items: center; font-size: 1.2rem; }
    .bonus-title { font-weight: 600; color: var(--text-primary); font-size: 1rem; margin-bottom: 3px; }
    .bonus-desc { font-size: 0.85rem; color: var(--text-secondary); }
    .bonus-amount { font-size: 1.2rem; font-weight: 700; color: #4ade80; }
    .bonus-achieved { opacity: 0.5; }
    .bonus-achieved .bonus-amount { color: var(--text-secondary); text-decoration: line-through; }

    @media (max-width: 768px) {
        .gold-grid { grid-template-columns: 1fr 1fr; gap: 15px; }
        .history-row { flex-direction: column; align-items: flex-start; gap: 10px; }
        .history-status-col { text-align: left; margin: 10px 0 0 0; }
        .history-amount { text-align: left; font-size: 1.8rem; }
    }
</style>
@endpush

@section('sidebar-empleado')
<nav class="sidebar-nav">
    <div class="nav-section">
        <p class="nav-section-title">Principal</p>
        <a href="{{ route('empleado.dashboard') }}" class="nav-item">
            <span class="nav-icon">🏠</span><span class="nav-text">Mi Panel</span>
        </a>
    </div>
    
    <div class="nav-section">
        <p class="nav-section-title">Citas</p>
        <a href="{{ url('/empleado/Citas_pendients') }}" class="nav-item">
            <span class="nav-icon">📋</span><span class="nav-text">Citas Pendientes</span>
        </a>
        <a href="{{ url('/empleado/Citas_atendidas') }}" class="nav-item">
            <span class="nav-icon">✅</span><span class="nav-text">Citas Atendidas</span>
        </a>
        <a href="{{ route('empleado.historial') }}" class="nav-item">
            <span class="nav-icon">📖</span><span class="nav-text">Historial Completo</span>
        </a>
    </div>
    
    <div class="nav-section">
        <p class="nav-section-title">Desempeño</p>
        <a href="{{ route('empleado.comisiones') }}" class="nav-item active">
            <span class="nav-icon">💰</span><span class="nav-text">Mis Comisiones</span>
        </a>
        <a href="{{ route('empleado.calificaciones') }}" class="nav-item">
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
        <a href="/empleado/perfil" class="nav-item">
            <span class="nav-icon">👤</span><span class="nav-text">Mi Perfil</span>
        </a>
    </div>
</nav>
@endsection

@section('topbar-left')
    <h1 style="font-family: 'Abril Fatface', cursive; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.3rem;">Mis Comisiones</h1>
    <p style="color: var(--text-secondary); font-size: 0.95rem;">Seguimiento de ventas y comisiones proyectadas</p>
@endsection

@section('content')
<div class="empleado-container">

    <div class="emp-nav-tabs">
        <a href="{{ route('empleado.comisiones') }}" class="emp-nav-tab active">💰 Comisiones</a>
        <a href="{{ route('empleado.calificaciones') }}" class="emp-nav-tab">⭐ Calificaciones</a>
    </div>

    <div class="gold-target-card">
        <div class="gold-grid">
            <div>
                <div class="gold-item-value">${{ number_format($ventasEsteMes, 2) }}</div>
                <div class="gold-item-label">Ventas este mes</div>
            </div>
            <div>
                <div class="gold-item-value">{{ $porcentajeMeta }}%</div>
                <div class="gold-item-label">Meta alcanzada</div>
            </div>
            <div>
                <div class="gold-item-value">${{ number_format($comisionEsteMes, 2) }}</div>
                <div class="gold-item-label">Proyección comisión</div>
            </div>
            <div>
                <div class="gold-item-value">{{ $diasRestantes }}</div>
                <div class="gold-item-label">Días restantes</div>
            </div>
        </div>

        <div class="progress-wrapper">
            <div class="progress-bar">
                <div class="progress-fill" @style(['width: ' . $porcentajeMeta . '%'])></div>
            </div>
            <div class="progress-labels">
                <span>${{ number_format($ventasEsteMes, 2) }} / ${{ number_format($metaVentas, 2) }}</span>
                <span>Faltan ${{ number_format($faltante, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="dark-panel">
        <div class="panel-title">
            <span>📄</span> Historial de Comisiones
        </div>
        
        <div>
            @forelse($historialBruto as $item)
                @php
                    $tasaAprox = $item['ventas'] > 0 ? round(($item['comision'] / $item['ventas']) * 100) : 0;
                @endphp
                <div class="history-row">
                    <div class="history-month">{{ $item['mes'] }}</div>
                    
                    <div class="history-details">
                        <div class="detail-sales">Ventas: ${{ number_format($item['ventas'], 2) }}</div>
                        <div class="detail-rate">{{ $tasaAprox }}% de comisión promedio</div>
                    </div>
                    
                    <div class="history-status-col">
                        @if($item['estado'] === 'Pendiente')
                            <span class="badge-status status-pendiente">Pendiente</span>
                            <div class="status-date">Cierre a fin de mes</div>
                        @else
                            <span class="badge-status status-pagado">Pagado</span>
                            <div class="status-date">Pagado: {{ $item['fecha_pago'] }}</div>
                        @endif
                    </div>
                    
                    <div class="history-amount">
                        ${{ number_format($item['comision'], 2) }}
                    </div>
                </div>
            @empty
                <div style="text-align:center; padding: 20px; color: var(--text-muted);">
                    No hay registro de comisiones aún.
                </div>
            @endforelse
        </div>
    </div>

    <div class="dark-panel">
        <div class="panel-title">
            <span>🎯</span> Metas y Bonos Disponibles
        </div>
        
        <div>
            @foreach($bonos as $bono)
                <div class="bonus-row {{ $bono['alcanzado'] ? 'bonus-achieved' : '' }}">
                    <div class="bonus-info">
                        <div class="bonus-icon">{{ $bono['icono'] }}</div>
                        <div>
                            <div class="bonus-title">
                                {{ $bono['titulo'] }} 
                                @if($bono['alcanzado']) <span style="color: #4ade80; font-size: 12px; margin-left: 5px;">(Alcanzado ✅)</span> @endif
                            </div>
                            <div class="bonus-desc">{{ $bono['desc'] }}</div>
                        </div>
                    </div>
                    <div class="bonus-amount">
                        +${{ $bono['bono'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection