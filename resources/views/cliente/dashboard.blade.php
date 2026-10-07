@extends('layouts.app')

@section('title', 'Mi Panel - StyleNow')

@push('styles')
<style>
    /* Estilos del sidebar-nav */
    .sidebar-nav { padding: 1rem 0; }
    .nav-section { margin-bottom: 1.5rem; }
    .nav-section-title { padding: 0 1.5rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 700; }
    .nav-item { display: flex; align-items: center; gap: 1rem; padding: 0.9rem 1.5rem; color: var(--text-primary); text-decoration: none; transition: all 0.3s ease; cursor: pointer; position: relative; }
    .nav-item:hover { background: rgba(250, 211, 112, 0.1); border-left: 3px solid var(--dorado); padding-left: calc(1.5rem - 3px); }
    .nav-item.active { background: rgba(250, 211, 112, 0.15); border-left: 3px solid var(--dorado); padding-left: calc(1.5rem - 3px); color: var(--dorado); }
    .nav-icon { font-size: 1.2rem; width: 24px; text-align: center; }
    .nav-text { flex: 1; font-size: 0.95rem; font-weight: 500; }

    /* STATS GRID */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
    .stat-card { background: var(--bg-card); padding: 1.5rem; border-radius: 15px; border: 1px solid var(--border-color); transition: all 0.3s ease; position: relative; overflow: hidden; }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: var(--dorado); }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(250, 211, 112, 0.1); border-color: rgba(250, 211, 112, 0.3); }
    .stat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .stat-icon { width: 45px; height: 45px; border-radius: 10px; background: rgba(250, 211, 112, 0.15); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .stat-value { font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.3rem; font-family: 'Abril Fatface', cursive;}
    .stat-label { font-size: 0.9rem; color: var(--text-secondary); }

    /* QUICK ACTIONS */
    .quick-actions { background: var(--bg-card); padding: 2rem; border-radius: 15px; border: 1px solid var(--border-color); margin-bottom: 2rem; }
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .section-title { font-family: 'Abril Fatface', cursive; font-size: 1.5rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.8rem; }
    .actions-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
    .action-btn { padding: 1.2rem; background: rgba(250, 211, 112, 0.05); border: 1px solid var(--border-color); border-radius: 12px; text-align: center; cursor: pointer; transition: all 0.3s ease; text-decoration: none; color: var(--text-primary); display: block;}
    .action-btn:hover { background: var(--dorado); color: var(--negro); transform: translateY(-5px); box-shadow: 0 10px 25px rgba(250, 211, 112, 0.3); border-color: var(--dorado);}
    .action-icon { font-size: 2rem; margin-bottom: 0.5rem; }
    .action-text { font-size: 0.9rem; font-weight: 600; }

    /* CAROUSEL */
    .carousel-container { background: var(--bg-card); padding: 2rem; border-radius: 15px; border: 1px solid var(--border-color); margin-bottom: 2rem; overflow: hidden; }
    .carousel { position: relative; width: 100%; height: 400px; border-radius: 12px; overflow: hidden; }
    .carousel-slide { position: absolute; width: 100%; height: 100%; opacity: 0; transition: opacity 0.5s ease-in-out; }
    .carousel-slide.active { opacity: 1; }
    .carousel-slide img { width: 100%; height: 100%; object-fit: cover; }
    .carousel-content { position: absolute; bottom: 0; left: 0; right: 0; padding: 2rem; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); color: white; }
    .carousel-content h3 { font-family: 'Abril Fatface', cursive; font-size: 1.8rem; margin-bottom: 0.5rem; color: var(--dorado);}
    .carousel-content p { font-size: 1rem; opacity: 0.9; }
    .carousel-controls { position: absolute; top: 50%; width: 100%; display: flex; justify-content: space-between; padding: 0 1rem; transform: translateY(-50%); }
    .carousel-btn { width: 45px; height: 45px; border-radius: 50%; background: rgba(250, 211, 112, 0.5); border: none; color: #fff; font-size: 1.2rem; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(5px);}
    .carousel-btn:hover { background: var(--dorado); color: var(--negro); transform: scale(1.1); }
    .carousel-indicators { position: absolute; bottom: 1rem; left: 50%; transform: translateX(-50%); display: flex; gap: 0.5rem; }
    .indicator { width: 10px; height: 10px; border-radius: 50%; background: rgba(255, 255, 255, 0.5); cursor: pointer; transition: all 0.3s ease; }
    .indicator.active { background: var(--dorado); width: 30px; border-radius: 5px; }

    /* CONTENT GRID */
    .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; }
    .content-card { background: var(--bg-card); padding: 2rem; border-radius: 15px; border: 1px solid var(--border-color); }
    .recent-list { margin-top: 1rem; }
    .list-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid var(--border-color); transition: background 0.3s ease; }
    .list-item:hover { background: rgba(250, 211, 112, 0.05); }
    .list-item:last-child { border-bottom: none; }
    .item-info h4 { font-size: 0.95rem; color: var(--text-primary); margin-bottom: 0.3rem; font-weight: bold;}
    .item-info p { font-size: 0.85rem; color: var(--text-secondary); }
    .item-status { padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase;}
    .status-confirmada { background: rgba(74, 222, 128, 0.1); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.2);}
    .status-pendiente { background: rgba(250, 211, 112, 0.1); color: var(--dorado); border: 1px solid rgba(250, 211, 112, 0.2);}

    .view-all-btn { padding: 0.5rem 1rem; background: rgba(250, 211, 112, 0.1); border: 1px solid var(--dorado); color: var(--dorado); font-size: 0.85rem; font-weight: bold; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; text-decoration: none;}
    .view-all-btn:hover { background: var(--dorado); color: var(--negro); }

    /* PROMOS EN DASHBOARD */
    .promo-card { background: linear-gradient(135deg, rgba(250, 211, 112, 0.1), rgba(21, 21, 21, 1)); padding: 1.2rem; border-radius: 12px; border: 1px solid rgba(250, 211, 112, 0.3); margin-bottom: 1rem; transition: all 0.3s ease; position: relative;}
    .promo-card:hover { transform: translateX(5px); box-shadow: 0 5px 20px rgba(250, 211, 112, 0.1); border-color: var(--dorado);}
    .promo-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
    .promo-title { font-size: 1rem; font-weight: bold; color: white; }
    .promo-discount { background: var(--dorado); color: var(--negro); padding: 0.3rem 0.8rem; border-radius: 20px; font-weight: bold; font-size: 0.8rem; }
    .promo-description { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.8rem; }
    .promo-validity { font-size: 0.75rem; color: var(--text-muted); }

    @media (max-width: 1024px) {
        .stats-grid, .actions-grid { grid-template-columns: repeat(2, 1fr); }
        .content-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .stats-grid, .actions-grid { grid-template-columns: 1fr; }
        .carousel { height: 250px; }
    }
</style>
@endpush

@section('sidebar-cliente')
<nav class="sidebar-nav">
    <div class="nav-section">
        <p class="nav-section-title">Principal</p>
        <a href="/cliente/dashboard" class="nav-item active">
            <span class="nav-icon">🏠</span><span class="nav-text">Mi Panel</span>
        </a>
    </div>

    <div class="nav-section">
        <p class="nav-section-title">Reservas</p>
        <a href="/cliente/citas/nueva" class="nav-item">
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
        <p class="nav-section-title">Beneficios</p>
        <a href="/cliente/puntos" class="nav-item">
            <span class="nav-icon">⭐</span><span class="nav-text">Mis Puntos</span>
        </a>
        <a href="/cliente/promo-vista" class="nav-item">
            <span class="nav-icon">🎁</span><span class="nav-text">Promociones</span>
        </a>
    </div>

    <div class="nav-section">
        <p class="nav-section-title">Cuenta</p>
        <a href="/cliente/perfil-vista" class="nav-item">
            <span class="nav-icon">👤</span><span class="nav-text">Mi Perfil</span>
        </a>
    </div>
</nav>
@endsection

@section('topbar-left')
    <h1 style="font-family: 'Abril Fatface', cursive; font-size: 2rem; margin-bottom: 0.3rem;">¡Hola, {{ explode(' ', Auth::user()->usr_nombre)[0] }}!</h1>
    <p style="color: var(--text-secondary); font-size: 0.95rem;">Nos alegra tenerte de vuelta en StyleNow.</p>
@endsection

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">📅</div>
            </div>
            <div class="stat-value">{{ $citasProximas->count() }}</div>
            <div class="stat-label">Citas Próximas</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">⭐</div>
            </div>
            <div class="stat-value">{{ $puntosDisponibles }}</div>
            <div class="stat-label">Puntos Acumulados</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">✂️</div>
            </div>
            <div class="stat-value">{{ $citasCompletadas->count() }}</div>
            <div class="stat-label">Servicios Recibidos</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon">🎁</div>
            </div>
            <div class="stat-value">{{ $promocionesActivas->count() }}</div>
            <div class="stat-label">Promociones Activas</div>
        </div>
    </div>

    <div class="carousel-container">
        <div class="section-header">
            <h2 class="section-title">
                <span style="color: var(--dorado);">✨</span> Descubre Tendencias
            </h2>
        </div>
        <div class="carousel" id="mainCarousel">
            <div class="carousel-slide active">
                <img src="https://images.unsplash.com/photo-1562322140-8baeececf3df?w=1200&h=400&fit=crop" alt="Corte">
                <div class="carousel-content">
                    <h3>Cortes de Última Tendencia</h3>
                    <p>Renueva tu look con nuestros estilistas profesionales.</p>
                </div>
            </div>
            <div class="carousel-slide">
                <img src="https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?w=1200&h=400&fit=crop" alt="Color">
                <div class="carousel-content">
                    <h3>Coloración Experta</h3>
                    <p>Transforma tu imagen cuidando la salud de tu cabello.</p>
                </div>
            </div>
            <div class="carousel-slide">
                <img src="https://images.unsplash.com/photo-1610992015732-2449b76344bc?w=1200&h=400&fit=crop" alt="Manicure">
                <div class="carousel-content">
                    <h3>Spa de Uñas</h3>
                    <p>Manicure y Pedicure con acabados perfectos y duraderos.</p>
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
        <div class="section-header" style="margin-bottom: 20px;">
            <h2 class="section-title">
                <span style="color: var(--dorado);">⚡</span> Acciones Rápidas
            </h2>
        </div>
        <div class="actions-grid">
            <a href="/cliente/citas/nueva" class="action-btn">
                <div class="action-icon">📅</div>
                <div class="action-text">Agendar Cita</div>
            </a>
            <a href="/cliente/puntos" class="action-btn">
                <div class="action-icon">⭐</div>
                <div class="action-text">Ver Mis Puntos</div>
            </a>
            <a href="/cliente/promo-vista" class="action-btn">
                <div class="action-icon">🎁</div>
                <div class="action-text">Ver Promociones</div>
            </a>
            <a href="/cliente/perfil-vista" class="action-btn">
                <div class="action-icon">👤</div>
                <div class="action-text">Mi Perfil</div>
            </a>
        </div>
    </div>

    <div class="content-grid">
        
        <div class="content-card">
            <div class="section-header">
                <h2 class="section-title">
                    <span style="color: var(--dorado);">📅</span> Mis Próximas Citas
                </h2>
                <a href="/cliente/mis-citas" class="view-all-btn">Ver Todas</a>
            </div>
            <div class="recent-list">
                @if($citasProximas->isEmpty())
                    <div style="text-align: center; padding: 40px 20px; border: 1px dashed var(--border-color); border-radius: 12px;">
                        <div style="font-size: 2.5rem; opacity: 0.5; margin-bottom: 10px;">🛋️</div>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">No tienes citas programadas por ahora.</p>
                        <a href="/cliente/citas/nueva" style="color: var(--dorado); text-decoration: none; font-weight: bold; margin-top: 10px; display: inline-block;">Agendar ahora →</a>
                    </div>
                @else
                    @foreach($citasProximas->take(4) as $cita)
                        <div class="list-item">
                            <div class="item-info">
                                <h4>{{ Str::limit($cita->cit_nombres_servicios, 35) }}</h4>
                                <p>
                                    {{ \Carbon\Carbon::parse($cita->cit_fechaCita)->translatedFormat('d \d\e M') }} • 
                                    {{ \Carbon\Carbon::parse($cita->cit_fechaCita)->format('H:i') }} • 
                                    👤 {{ explode(' ', $cita->estilista_nombre)[0] }}
                                </p>
                            </div>
                            <div class="item-status status-{{ strtolower($cita->cit_estadoCita) }}">{{ $cita->cit_estadoCita }}</div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="content-card">
            <div class="section-header">
                <h2 class="section-title">
                    <span style="color: var(--dorado);">🎁</span> Destacados
                </h2>
            </div>
            <div class="recent-list">
                @if($promocionesActivas->isEmpty())
                    <p style="color: var(--text-muted); text-align: center; padding: 20px; font-size: 0.9rem;">No hay promociones activas hoy.</p>
                @else
                    @foreach($promocionesActivas as $promo)
                        <div class="promo-card">
                            <div class="promo-header">
                                <span class="promo-title">{{ Str::limit($promo->prm_nombre, 20) }}</span>
                                <span class="promo-discount">
                                    @if($promo->prm_tipoDescuento == 'Porcentaje')
                                        {{ round($promo->prm_valorDescuento) }}% OFF
                                    @elseif($promo->prm_tipoDescuento == 'Fijo')
                                        ${{ number_format($promo->prm_valorDescuento, 0) }}
                                    @else
                                        {{ $promo->prm_tipoDescuento }}
                                    @endif
                                </span>
                            </div>
                            <p class="promo-description">{{ Str::limit($promo->prm_descripcion, 40) }}</p>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <p class="promo-validity">Hasta: {{ \Carbon\Carbon::parse($promo->prm_fechaFin)->format('d/m/Y') }}</p>
                                <a href="/cliente/promo-vista" style="color: var(--dorado); text-decoration: none; font-size: 11px; font-weight: bold; padding: 4px 10px; background: rgba(250,211,112,0.1); border-radius: 8px;">Ver código</a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Carousel functionality simple y elegante
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');
    const indicators = document.querySelectorAll('.indicator');
    let autoPlayInterval;

    function showSlide(n) {
        if(slides.length === 0) return;
        
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
        autoPlayInterval = setInterval(autoPlay, 5000);
    }

    if(slides.length > 0){
        autoPlayInterval = setInterval(autoPlay, 5000);
    }
</script>
@endsection