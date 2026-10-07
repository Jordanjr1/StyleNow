@extends('layouts.app')

@section('title', 'Mi Panel - StyleNow')

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
    .nav-badge { background: #ef4444; color: white; font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 10px; font-weight: 700; }

    /* STATS GRID */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
    .stat-card { background: var(--bg-card); padding: 1.5rem; border-radius: 15px; border: 1px solid var(--border-color); transition: all 0.3s ease; position: relative; overflow: hidden; }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: var(--dorado); }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(250, 211, 112, 0.2); }
    .stat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .stat-icon { width: 45px; height: 45px; border-radius: 10px; background: rgba(250, 211, 112, 0.15); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .stat-value { font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.3rem; }
    .stat-label { font-size: 0.9rem; color: var(--text-secondary); }
    .stat-sublabel { font-size: 0.8rem; color: var(--dorado); font-weight: 600; margin-top: 0.3rem; }

    .commission-badge { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.4rem 0.8rem; background: linear-gradient(135deg, #4ade80, #22c55e); color: #000; border-radius: 20px; font-size: 0.85rem; font-weight: 700; }
    .rating-stars { display: flex; gap: 0.2rem; font-size: 1.2rem; margin-top: 0.3rem; }
    .star { color: var(--dorado); }
    .star.empty { color: var(--text-secondary); opacity: 0.3; }

    /* QUICK ACTIONS */
    .quick-actions { background: var(--bg-card); padding: 2rem; border-radius: 15px; border: 1px solid var(--border-color); margin-bottom: 2rem; }
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .section-title { font-family: 'Abril Fatface', cursive; font-size: 1.5rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.8rem; }
    .actions-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
    
    .action-btn { padding: 1.2rem; background: rgba(250, 211, 112, 0.1); border: 1px solid var(--border-color); border-radius: 12px; text-align: center; cursor: pointer; transition: all 0.3s ease; text-decoration: none; color: var(--text-primary); position: relative; }
    .action-btn:hover { background: var(--dorado); color: var(--negro); transform: translateY(-5px); box-shadow: 0 10px 25px rgba(250, 211, 112, 0.3); }
    .action-icon { font-size: 2rem; margin-bottom: 0.5rem; }
    .action-text { font-size: 0.9rem; font-weight: 600; }
    .action-badge { position: absolute; top: 0.5rem; right: 0.5rem; background: #ef4444; color: white; font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 10px; font-weight: 700; }

    /* CAROUSEL */
    .carousel-container { background: var(--bg-card); padding: 2rem; border-radius: 15px; border: 1px solid var(--border-color); margin-bottom: 2rem; overflow: hidden; }
    .carousel { position: relative; width: 100%; height: 400px; border-radius: 12px; overflow: hidden; }
    .carousel-slide { position: absolute; width: 100%; height: 100%; opacity: 0; transition: opacity 0.5s ease-in-out; }
    .carousel-slide.active { opacity: 1; }
    .carousel-slide img { width: 100%; height: 100%; object-fit: cover; filter: brightness(0.7); }
    .carousel-content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: white; width: 80%; z-index: 2; }
    .carousel-content h3 { font-family: 'Abril Fatface', cursive; font-size: 2.5rem; margin-bottom: 1rem; text-shadow: 2px 2px 8px rgba(0,0,0,0.5); }
    .carousel-content p { font-size: 1.2rem; opacity: 0.95; text-shadow: 1px 1px 4px rgba(0,0,0,0.5); font-style: italic; }
    .carousel-controls { position: absolute; top: 50%; width: 100%; display: flex; justify-content: space-between; padding: 0 1rem; transform: translateY(-50%); z-index: 3; }
    .carousel-btn { width: 45px; height: 45px; border-radius: 50%; background: rgba(250, 211, 112, 0.9); border: none; color: var(--negro); font-size: 1.2rem; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; }
    .carousel-btn:hover { background: var(--dorado); transform: scale(1.1); }
    .carousel-indicators { position: absolute; bottom: 1rem; left: 50%; transform: translateX(-50%); display: flex; gap: 0.5rem; z-index: 3; }
    .indicator { width: 10px; height: 10px; border-radius: 50%; background: rgba(255, 255, 255, 0.5); cursor: pointer; transition: all 0.3s ease; }
    .indicator.active { background: var(--dorado); width: 30px; border-radius: 5px; }

    /* CONTENT GRID */
    .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; }
    .content-card { background: var(--bg-card); padding: 2rem; border-radius: 15px; border: 1px solid var(--border-color); }
    
    .list-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid var(--border-color); transition: background 0.3s ease; }
    .list-item:hover { background: rgba(250, 211, 112, 0.05); }
    .list-item:last-child { border-bottom: none; }
    .item-info h4 { font-size: 0.95rem; color: var(--text-primary); margin-bottom: 0.3rem; }
    .item-info p { font-size: 0.85rem; color: var(--text-secondary); }
    
    .item-actions { display: flex; gap: 0.5rem; }
    .btn-accept { padding: 0.5rem 1rem; background: rgba(74, 222, 128, 0.2); color: #4ade80; border: 1px solid #4ade80; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; }
    .btn-accept:hover { background: #4ade80; color: #000; }
    .btn-decline { padding: 0.5rem 1rem; background: rgba(255, 68, 68, 0.2); color: #ff4444; border: 1px solid #ff4444; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; }
    .btn-decline:hover { background: #ff4444; color: #fff; }

    .item-rating { display: flex; align-items: center; gap: 0.5rem; }
    .rating-value { font-size: 0.9rem; color: var(--dorado); font-weight: 700; }

    .view-all-btn { padding: 0.5rem 1rem; background: rgba(250, 211, 112, 0.1); border: 1px solid var(--border-color); color: var(--dorado); font-size: 0.85rem; font-weight: 600; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; }
    .view-all-btn:hover { background: var(--dorado); color: var(--negro); }

    /* ALERT CARD */
    .alert-card { background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(239, 68, 68, 0.05)); padding: 1.2rem; border-radius: 12px; border: 1px solid rgba(239, 68, 68, 0.3); margin-bottom: 1rem; transition: all 0.3s ease; }
    .alert-card:hover { transform: translateX(5px); box-shadow: 0 5px 20px rgba(239, 68, 68, 0.2); }
    .alert-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
    .alert-title { font-size: 1rem; font-weight: 700; color: #ef4444; display: flex; align-items: center; gap: 0.5rem; }
    .alert-priority { background: #ef4444; color: white; padding: 0.3rem 0.8rem; border-radius: 20px; font-weight: 700; font-size: 0.75rem; }
    .alert-description { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem; }

    .commission-progress { margin-top: 0.8rem; }
    .progress-bar-container { width: 100%; height: 8px; background: rgba(250, 211, 112, 0.2); border-radius: 10px; overflow: hidden; margin-bottom: 0.5rem; }
    .progress-bar { height: 100%; background: linear-gradient(90deg, var(--dorado), #f4c430); border-radius: 10px; transition: width 0.5s ease; }
    .progress-text { font-size: 0.75rem; color: var(--text-secondary); text-align: center; }

    @media (max-width: 1024px) { .stats-grid, .actions-grid { grid-template-columns: repeat(2, 1fr); } .content-grid { grid-template-columns: 1fr; } }
    @media (max-width: 640px) { .stats-grid, .actions-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('sidebar-empleado')
<nav class="sidebar-nav">
    <div class="nav-section">
        <p class="nav-section-title">Principal</p>
        <a href="{{ route('empleado.dashboard') }}" class="nav-item active">
            <span class="nav-icon">🏠</span><span class="nav-text">Mi Panel</span>
        </a>
    </div>

    <div class="nav-section">
        <p class="nav-section-title">Citas</p>
        <a href="{{ url('/empleado/Citas_pendients') }}" class="nav-item">
            <span class="nav-icon">📋</span><span class="nav-text">Citas Pendientes</span>
            @if(isset($citasPendientes) && count($citasPendientes) > 0)
                <span class="nav-badge" id="sidebar-pending-badge">{{ count($citasPendientes) }}</span>
            @endif
        </a>
        <a href="/empleado/Citas_atendidas" class="nav-item">
            <span class="nav-icon">✅</span><span class="nav-text">Citas Atendidas</span>
        </a>
        <a href="/empleado/historial" class="nav-item">
            <span class="nav-icon">📖</span><span class="nav-text">Historial Completo</span>
        </a>
    </div>

    <div class="nav-section">
        <p class="nav-section-title">Desempeño</p>
        <a href="/empleado/comisiones" class="nav-item">
            <span class="nav-icon">💰</span><span class="nav-text">Mis Comisiones</span>
        </a>
        <a href="/empleado/calificaciones" class="nav-item">
            <span class="nav-icon">⭐</span><span class="nav-text">Calificaciones</span>
        </a>
    </div>

    <div class="nav-section">
        <p class="nav-section-title">Inventario</p>
        <a href="/empleado/reportar-falta" class="nav-item">
            <span class="nav-icon">📦</span><span class="nav-text">Reportar Falta</span>
        </a>
        <a href="/empleado/ventas" class="nav-item">
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
    @php
        $hora = \Carbon\Carbon::now('America/Guayaquil')->hour;
        $saludo = ($hora < 12) ? 'Buenos días' : (($hora < 19) ? 'Buenas tardes' : 'Buenas noches');
    @endphp
    <h1 style="font-family: 'Abril Fatface', cursive; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.3rem;">¡{{ $saludo }}, {{ Auth::user()->usr_nombre }}!</h1>
    <p style="color: var(--text-secondary); font-size: 0.95rem;">Aquí está el resumen de tu rendimiento y actividades.</p>
@endsection

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">📅</div>
            </div>
            <div class="stat-value">{{ $metas['citas_mes'] }}</div>
            <div class="stat-label">Citas Atendidas (Mes)</div>
            <div class="commission-progress">
                <div class="progress-bar-container">
<div class="progress-bar" @style(['width: ' . $metas['progreso'] . '%'])></div>                </div>
                @if($metas['faltan'] > 0)
                    <p class="progress-text">{{ $metas['faltan'] }} citas más para {{ $metas['porcentaje_siguiente'] }}% de comisión</p>
                @else
                    <p class="progress-text" style="color:#4ade80;">¡Nivel Máximo Alcanzado!</p>
                @endif
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">💰</div>
            </div>
            <div class="stat-value">
                <span class="commission-badge"><span>{{ $metas['comision_actual'] }}%</span></span>
            </div>
            <div class="stat-label">Comisión Actual</div>
            <div class="stat-sublabel">
                {{ $metas['siguiente_nivel'] > 0 ? "Próximo nivel: {$metas['siguiente_nivel']} citas ({$metas['porcentaje_siguiente']}%)" : "Has llegado al máximo porcentaje" }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">⭐</div>
            </div>
            <div class="stat-value">{{ number_format($promedioValoracion, 1) }}</div>
            <div class="stat-label">Calificación Promedio</div>
            <div class="rating-stars">
                @php
                    $estrellas_llenas = floor($promedioValoracion);
                @endphp
                {!! str_repeat('<span class="star">★</span>', $estrellas_llenas) !!}
                {!! str_repeat('<span class="star empty">★</span>', 5 - $estrellas_llenas) !!}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">📋</div>
            </div>
            <div class="stat-value" id="dashboard-pending-count">{{ count($citasPendientes) }}</div>
            <div class="stat-label">Citas Pendientes</div>
            <div class="stat-sublabel" style="color: #ef4444;">Por aceptar o rechazar</div>
        </div>
    </div>

    <div class="carousel-container">
        <div class="section-header">
            <h2 class="section-title"><span>💪</span><span>Inspiración del Día</span></h2>
        </div>
        <div class="carousel" id="mainCarousel">
            <div class="carousel-slide active">
                <img src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=1200&h=400&fit=crop" alt="Estilista trabajando">
                <div class="carousel-content">
                    <h3>Tu Dedicación Marca la Diferencia</h3>
                    <p>"Cada cliente es una oportunidad para crear belleza y confianza"</p>
                </div>
            </div>
            <div class="carousel-slide">
                <img src="https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=1200&h=400&fit=crop" alt="Servicio al cliente">
                <div class="carousel-content">
                    <h3>Excelencia en Cada Detalle</h3>
                    <p>"Un gran servicio comienza con una sonrisa genuina"</p>
                </div>
            </div>
            <div class="carousel-slide">
                <img src="https://images.unsplash.com/photo-1633681926022-84c23e8cb2d6?w=1200&h=400&fit=crop" alt="Trabajo en equipo">
                <div class="carousel-content">
                    <h3>Juntos Somos Más Fuertes</h3>
                    <p>"El éxito del equipo es el éxito de todos"</p>
                </div>
            </div>
            
            <div class="carousel-controls">
                <button class="carousel-btn" onclick="changeSlide(-1)">‹</button>
                <button class="carousel-btn" onclick="changeSlide(1)">›</button>
            </div>
            
            <div class="carousel-indicators">
                <span class="indicator active" onclick="goToSlide(0)"></span>
                <span class="indicator" onclick="goToSlide(1)"></span>
                <span class="indicator" onclick="goToSlide(2)"></span>
            </div>
        </div>
    </div>

    <div class="quick-actions">
        <div class="section-header">
            <h2 class="section-title"><span>⚡</span><span>Acciones Rápidas</span></h2>
        </div>
        <div class="actions-grid">
            <a href="/empleado/Citas_pendients" class="action-btn">
                <div class="action-icon">📋</div>
                <div class="action-text">Ver Citas Pendientes</div>
                @if(isset($citasPendientes) && count($citasPendientes) > 0)
                    <span class="action-badge" id="quick-pending-badge">{{ count($citasPendientes) }}</span>
                @endif
            </a>
            <a href="/empleado/reportar-falta" class="action-btn">
                <div class="action-icon">📦</div>
                <div class="action-text">Reportar Falta</div>
            </a>
            <a href="/empleado/comisiones" class="action-btn">
                <div class="action-icon">💰</div>
                <div class="action-text">Mis Comisiones</div>
            </a>
            <a href="/empleado/ventas" class="action-btn">
                <div class="action-icon">💵</div>
                <div class="action-text">Punto de Venta</div>
            </a>
        </div>
    </div>

    <div class="content-grid">
        <div>
            <div class="content-card" style="margin-bottom: 1.5rem;">
                <div class="section-header">
                    <h2 class="section-title"><span>📋</span><span>Citas por Aceptar</span></h2>
                    <button class="view-all-btn" onclick="window.location.href='/empleado/Citas_pendients'">Ver Todas</button>
                </div>
                <div class="recent-list">
                    @forelse($citasPendientes as $citaP)
                        <div class="list-item" id="cita-row-{{ $citaP->cit_id }}">
                            <div class="item-info">
                                <h4>{{ $citaP->cliente_nombre ?? 'Cliente General' }}</h4>
                                <p>{{ Str::limit($citaP->servicio ?? 'Servicio', 20) }} • {{ \Carbon\Carbon::parse($citaP->cit_fechaCita)->format('d M h:i A') }}</p>
                            </div>
                            <div class="item-actions">
                                <button class="btn-accept" data-id="{{ $citaP->cit_id }}" onclick="gestionarCita(this.dataset.id, 'Confirmada')">Aceptar</button>
<button class="btn-decline" data-id="{{ $citaP->cit_id }}" onclick="gestionarCita(this.dataset.id, 'Cancelada')">Rechazar</button>
                            </div>
                        </div>
                    @empty
                        <p style="text-align:center; color:var(--text-muted); padding:20px;">No tienes citas pendientes por ahora.</p>
                    @endforelse
                </div>
            </div>

            <div class="content-card">
                <div class="section-header">
                    <h2 class="section-title"><span>✅</span><span>Últimas Atendidas</span></h2>
                    <button class="view-all-btn" onclick="window.location.href='/empleado/historial'">Ver Historial</button>
                </div>
                <div class="recent-list">
                    @forelse($ultimasAtendidas as $citaA)
                        <div class="list-item">
                            <div class="item-info">
                                <h4>{{ $citaA->cliente_nombre ?? 'Cliente General' }}</h4>
                                <p>{{ Str::limit($citaA->servicio ?? 'Servicio', 25) }} • {{ \Carbon\Carbon::parse($citaA->cit_fechaCita)->diffForHumans() }}</p>
                            </div>
                            <div class="item-rating">
                                <span class="rating-value" style="color:#4ade80;">+${{ number_format($citaA->cit_comisionGanada, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <p style="text-align:center; color:var(--text-muted); padding:20px;">Aún no tienes historial de citas completadas.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div>
            <div class="content-card" style="margin-bottom: 1.5rem;">
                <div class="section-header">
                    <h2 class="section-title"><span>⚠️</span><span>Falta de Inventario</span></h2>
                    <button class="view-all-btn" onclick="window.location.href='/empleado/reportar-falta'">Reportar</button>
                </div>
                <div class="recent-list">
                    @forelse($alertasInventario as $alerta)
                        <div class="alert-card">
                            <div class="alert-header">
                                <span class="alert-title"><span>⚠️</span><span>{{ Str::limit($alerta->prd_nombre, 20) }}</span></span>
                                <span class="alert-priority">STOCK: {{ $alerta->prd_stockActual }}</span>
                            </div>
                            <p class="alert-description">Este producto está a punto de agotarse en la vitrina.</p>
                        </div>
                    @empty
                        <div style="text-align:center; color:var(--text-muted); padding:20px;">El inventario está estable.</div>
                    @endforelse
                </div>
            </div>

            <div class="content-card">
                <div class="section-header">
                    <h2 class="section-title"><span>🎯</span><span>Mis Metas</span></h2>
                </div>
                <div class="recent-list">
                    <div class="list-item" style="display:block;">
                        <div class="item-info" style="margin-bottom: 10px;">
                            <h4>Nivel Bronce (40 citas)</h4>
                            <p>Desbloquea Comisión del 10%</p>
                        </div>
                        <div class="commission-progress">
                            <div class="progress-bar-container">
<div class="progress-bar" @style(['width: ' . $metas['progreso_40'] . '%'])></div>                            </div>
                            <p class="progress-text" style="text-align: left;">{{ $metas['citas_mes'] }} de 40 completadas</p>
                        </div>
                    </div>
                    
                    <div class="list-item" style="display:block;">
                        <div class="item-info" style="margin-bottom: 10px;">
                            <h4>Nivel Oro (50 citas)</h4>
                            <p>Desbloquea Comisión Máxima (15%)</p>
                        </div>
                        <div class="commission-progress">
                            <div class="progress-bar-container">
<div class="progress-bar" @style(['width: ' . $metas['progreso_50'] . '%'])></div>
                            </div>
                            <p class="progress-text" style="text-align: left;">{{ $metas['citas_mes'] }} de 50 completadas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // --------------------------------------------------
    // LÓGICA DEL CARRUSEL DE IMÁGENES
    // --------------------------------------------------
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');
    const indicators = document.querySelectorAll('.indicator');
    let autoPlayInterval;

    function showSlide(n) {
        slides.forEach(slide => slide.classList.remove('active'));
        indicators.forEach(indicator => indicator.classList.remove('active'));
        
        if (n >= slides.length) currentSlide = 0;
        if (n < 0) currentSlide = slides.length - 1;
        
        slides[currentSlide].classList.add('active');
        indicators[currentSlide].classList.add('active');
    }

    function changeSlide(n) {
        currentSlide += n;
        showSlide(currentSlide);
        resetAutoPlay();
    }

    function goToSlide(n) {
        currentSlide = n;
        showSlide(currentSlide);
        resetAutoPlay();
    }

    function autoPlay() {
        currentSlide++;
        showSlide(currentSlide);
    }

    function resetAutoPlay() {
        clearInterval(autoPlayInterval);
        autoPlayInterval = setInterval(autoPlay, 6000);
    }

    if(slides.length > 0) {
        autoPlayInterval = setInterval(autoPlay, 6000);
    }

    // --------------------------------------------------
    // ACEPTAR Y RECHAZAR CITAS DESDE EL DASHBOARD
    // --------------------------------------------------
    async function gestionarCita(id, nuevoEstado) {
        const accion = nuevoEstado === 'Confirmada' ? 'Aceptar' : 'Rechazar';
        const color = nuevoEstado === 'Confirmada' ? '#4ade80' : '#ef4444';

        const result = await Swal.fire({
            title: `¿${accion} esta cita?`,
            text: nuevoEstado === 'Confirmada' ? "Pasará a la pestaña de confirmadas." : "La cita será cancelada y notificada al cliente.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: color,
            cancelButtonColor: '#333',
            confirmButtonText: `Sí, ${accion}`,
            cancelButtonText: 'Cancelar',
            background: '#1a1a1a', color: '#fff'
        });

        if (result.isConfirmed) {
            try {
                Swal.fire({title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
                
                // NOTA: Usa la ruta que ya creamos antes para actualizar el estado
                const res = await fetch(`/empleado/citas/${id}/estado`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ estado: nuevoEstado })
                });

                const data = await res.json();
                
                if (res.ok && data.success) {
                    Swal.fire({
                        icon: 'success', 
                        title: `¡Cita ${nuevoEstado}!`, 
                        background: '#1a1a1a', color: '#fff', confirmButtonColor: '#fad370',
                        timer: 1500, showConfirmButton: false
                    }).then(() => {
                        // Animación de desaparición sin recargar la página entera
                        const row = document.getElementById(`cita-row-${id}`);
                        if(row) {
                            row.style.opacity = '0';
                            setTimeout(() => {
                                row.remove();
                                reducirContador();
                            }, 300);
                        }
                    });
                } else {
                    Swal.fire('Error', data.message || 'Error al actualizar', 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'Falla de conexión', 'error');
            }
        }
    }

    // Reduce visualmente el número de citas pendientes en todos los globos rojos
    function reducirContador() {
        const counters = [
            document.getElementById('dashboard-pending-count'),
            document.getElementById('sidebar-pending-badge'),
            document.getElementById('quick-pending-badge')
        ];
        
        counters.forEach(counter => {
            if(counter) {
                let val = parseInt(counter.innerText) - 1;
                if(val < 0) val = 0;
                counter.innerText = val;
                if(val === 0 && counter.classList.contains('nav-badge')) {
                    counter.style.display = 'none';
                }
            }
        });
    }
</script>
@endsection