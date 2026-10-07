@extends('layouts.app')

@section('title', 'Dashboard Administrativo - StyleNow')

@push('styles')
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
    .nav-arrow { font-size: 0.8rem; transition: transform 0.3s ease; }
    .nav-item.expanded .nav-arrow { transform: rotate(90deg); }
    .submenu { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; background: rgba(0, 0, 0, 0.2); }
    .submenu.open { max-height: 500px; }
    .submenu-item { display: block; padding: 0.7rem 1.5rem 0.7rem 4rem; color: var(--text-secondary); text-decoration: none; font-size: 0.9rem; transition: all 0.3s ease; }
    .submenu-item:hover { color: var(--dorado); background: rgba(250, 211, 112, 0.05); }

    /* STATS GRID */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
    .stat-card { background: var(--bg-card); padding: 1.5rem; border-radius: 15px; border: 1px solid var(--border-color); transition: all 0.3s ease; position: relative; overflow: hidden; }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: var(--dorado); }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(250, 211, 112, 0.2); }
    .stat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .stat-icon { width: 45px; height: 45px; border-radius: 10px; background: rgba(250, 211, 112, 0.15); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .stat-trend { display: flex; align-items: center; gap: 0.3rem; font-size: 0.85rem; padding: 0.3rem 0.6rem; border-radius: 20px; }
    .stat-trend.positive { color: #4ade80; background: rgba(74, 222, 128, 0.1); }
    .stat-trend.negative { color: #ff4444; background: rgba(255, 68, 68, 0.1); }
    .stat-value { font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.3rem; }
    .stat-label { font-size: 0.9rem; color: var(--text-secondary); }

    /* QUICK ACTIONS */
    .quick-actions { background: var(--bg-card); padding: 2rem; border-radius: 15px; border: 1px solid var(--border-color); margin-bottom: 2rem; }
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .section-title { font-family: 'Abril Fatface', cursive; font-size: 1.5rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.8rem; }
    .actions-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
    .action-btn { padding: 1.2rem; background: rgba(250, 211, 112, 0.1); border: 1px solid var(--border-color); border-radius: 12px; text-align: center; cursor: pointer; transition: all 0.3s ease; text-decoration: none; color: var(--text-primary); }
    .action-btn:hover { background: var(--dorado); color: var(--negro); transform: translateY(-5px); box-shadow: 0 10px 25px rgba(250, 211, 112, 0.3); }
    .action-icon { font-size: 2rem; margin-bottom: 0.5rem; }
    .action-text { font-size: 0.9rem; font-weight: 600; }

    /* CONTENT GRID */
    .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; }
    .content-card { background: var(--bg-card); padding: 2rem; border-radius: 15px; border: 1px solid var(--border-color); }
    .recent-list { margin-top: 1rem; }
    .list-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid var(--border-color); transition: background 0.3s ease; }
    .list-item:hover { background: rgba(250, 211, 112, 0.05); }
    .list-item:last-child { border-bottom: none; }
    .item-info h4 { font-size: 0.95rem; color: var(--text-primary); margin-bottom: 0.3rem; }
    .item-info p { font-size: 0.85rem; color: var(--text-secondary); }
    .item-value { font-size: 1.1rem; font-weight: 700; color: var(--dorado); }
    .view-all-btn { padding: 0.5rem 1rem; background: rgba(250, 211, 112, 0.1); border: 1px solid var(--border-color); color: var(--dorado); font-size: 0.85rem; font-weight: 600; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; text-decoration: none; }
    .view-all-btn:hover { background: var(--dorado); color: var(--negro); }
    
    @media (max-width: 1024px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .content-grid { grid-template-columns: 1fr; }
        .actions-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>

<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endpush

@section('topbar-left')
    <h1>Dashboard Administrativo</h1>
    <p>Bienvenido de vuelta, aquí está tu resumen de hoy</p>
@endsection

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">📅</div>
                <div class="stat-trend positive">
                    <span>▲</span>
                    <span>Hoy</span>
                </div>
            </div>
            <div class="stat-value">{{ $citasHoy }}</div>
            <div class="stat-label">Citas Hoy</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">💰</div>
                <div class="stat-trend positive">
                    <span>▲</span>
                    <span>Mes</span>
                </div>
            </div>
            <div class="stat-value">${{ number_format($ingresosMes, 2) }}</div>
            <div class="stat-label">Ingresos del Mes</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">👥</div>
                <div class="stat-trend positive">
                    <span>●</span>
                    <span>Total</span>
                </div>
            </div>
            <div class="stat-value">{{ $clientesActivos }}</div>
            <div class="stat-label">Clientes Activos</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">🎁</div>
                <div class="stat-trend positive">
                    <span>⭐</span>
                    <span>Total</span>
                </div>
            </div>
            <div class="stat-value">{{ number_format($puntosTotales) }}</div>
            <div class="stat-label">Puntos en Sistema</div>
        </div>
    </div>

    <div class="quick-actions">
        <div class="section-header">
            <h2 class="section-title">
                <span>⚡</span>
                <span>Acciones Rápidas</span>
            </h2>
        </div>
        <div class="actions-grid">
            <a href="{{ route('citas.lista') }}" class="action-btn">
                <div class="action-icon">➕</div>
                <div class="action-text">Nueva Cita</div>
            </a>
            <a href="{{ route('admin.usuarios.lista') }}" class="action-btn">
                <div class="action-icon">👤</div>
                <div class="action-text">Registrar Cliente</div>
            </a>
            <a href="{{ route('configuracion.servicios') }}" class="action-btn">
                <div class="action-icon">✂️</div>
                <div class="action-text">Gestionar Servicios</div>
            </a>
            <a href="{{ route('reportes.financieros') }}" class="action-btn">
                <div class="action-icon">📊</div>
                <div class="action-text">Ver Reportes</div>
            </a>
        </div>
    </div>

    <div class="content-grid">
        <div class="content-card">
            <div class="section-header">
                <h2 class="section-title">
                    <span>🕐</span>
                    <span>Citas Recientes</span>
                </h2>
                <a href="{{ route('citas.lista') }}" class="view-all-btn">Ver Todas</a>
            </div>
            <div class="recent-list">
                @forelse($citasRecientes as $cita)
                    <div class="list-item">
                        <div class="item-info">
                            <h4>{{ $cita->usr_nombre }} {{ $cita->usr_apellido }}</h4>
                            <p>{{ $cita->srv_nombre }} • {{ \Carbon\Carbon::parse($cita->cit_fechaCita)->format('h:i A') }}</p>
                        </div>
                        <div class="item-value">${{ number_format($cita->srv_precio, 2) }}</div>
                    </div>
                @empty
                    <div class="list-item" style="justify-content: center; color: var(--text-secondary);">
                        <p>No hay citas registradas recientemente.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="content-card">
            <div class="section-header">
                <h2 class="section-title">
                    <span>🏆</span>
                    <span>Top Empleados</span>
                </h2>
            </div>
            <div class="recent-list">
                @forelse($topEmpleados as $emp)
                    <div class="list-item">
                        <div class="item-info">
                            <h4>{{ $emp->usr_nombre }} {{ $emp->usr_apellido }}</h4>
                            <p>{{ $emp->total_citas }} servicios realizados</p>
                        </div>
                        <div class="item-value">⭐</div>
                    </div>
                @empty
                    <div class="list-item" style="justify-content: center; color: var(--text-secondary);">
                        <p>Aún no hay datos de rendimiento.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection