@extends('layouts.app')

@section('title', 'Mis Puntos - StyleNow')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    :root {
        --dorado: #fad370; --bg-card: #151515; --bg-hover: #1f1f1f;
        --border-color: #2a2a2a; --text-muted: #888888;
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

    /* CONTENIDO DE PUNTOS */
    .puntos-container { max-width: 1000px; display: flex; flex-direction: column; gap: 1.5rem; }

    /* BANNER DORADO */
    .banner-dorado {
        background: linear-gradient(135deg, #FAD370 0%, #D4AF37 100%);
        border-radius: 16px; padding: 2.5rem; display: flex; justify-content: space-between; align-items: center; color: #000; box-shadow: 0 10px 30px rgba(250, 211, 112, 0.2);
    }
    .banner-title { font-family: 'Abril Fatface', cursive; font-size: 2.5rem; margin-bottom: 5px; }
    .banner-subtitle { font-size: 1rem; font-weight: 500; opacity: 0.8; margin-bottom: 20px;}
    .puntos-big { font-family: 'Abril Fatface', cursive; font-size: 4rem; line-height: 1; }
    
    .nivel-badge {
        background: rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 12px; text-align: center; min-width: 150px;
    }
    .nivel-icon { font-size: 2.5rem; margin-bottom: 10px; }
    .nivel-text { font-weight: bold; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px;}

    /* STATS GRID */
    .stats-puntos { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
    .stat-box { background: var(--bg-card); border: 1px solid var(--border-color); padding: 1.5rem; border-radius: 12px; display: flex; flex-direction: column; }
    .stat-icon { color: var(--dorado); font-size: 1.5rem; margin-bottom: 10px; }
    .stat-value { font-size: 2rem; font-weight: bold; color: white; font-family: 'Abril Fatface', cursive;}
    .stat-label { color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; }

    /* HISTORIAL LISTA */
    .historial-box { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; }
    .h-title { color: white; font-size: 1.2rem; font-weight: bold; margin-bottom: 1rem; display: flex; align-items: center; gap: 10px;}
    .h-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid var(--border-color); }
    .h-item:last-child { border-bottom: none; }
    .h-left { display: flex; align-items: center; gap: 15px; }
    .h-icon { background: rgba(250, 211, 112, 0.1); width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .h-name { color: white; font-weight: bold; }
    .h-date { color: var(--text-muted); font-size: 0.8rem; }
    .h-points { font-weight: bold; font-size: 1.1rem; }

    /* GRID PREMIOS */
    .premios-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; }
    .premio-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; transition: 0.3s; position: relative; }
    .premio-card:hover { border-color: var(--dorado); transform: translateY(-5px); }
    .badge-tipo { position: absolute; top: 15px; right: 15px; background: rgba(250, 211, 112, 0.2); color: var(--dorado); font-size: 0.7rem; padding: 4px 10px; border-radius: 20px; font-weight: bold; text-transform: uppercase;}
    .p-icon { font-size: 3rem; margin-bottom: 1rem; }
    .p-name { color: white; font-weight: bold; font-size: 1.1rem; margin-bottom: 5px; }
    .p-desc { color: var(--text-muted); font-size: 0.85rem; margin-bottom: 15px; min-height: 40px;}
    .p-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 15px;}
    .p-cost { color: var(--dorado); font-weight: bold; font-size: 1.2rem; }
    
    .btn-canjear { background: var(--dorado); color: black; border: none; padding: 8px 15px; border-radius: 6px; font-weight: bold; cursor: pointer; transition: 0.3s; }
    .btn-canjear:hover { background: #d4af37; }
    .btn-disabled { background: #333; color: #666; cursor: not-allowed; padding: 8px 15px; border-radius: 6px; font-weight: bold; border: none;}

    @media (max-width: 768px) {
        .banner-dorado { flex-direction: column; text-align: center; gap: 20px; }
    }
</style>
@endpush

{{-- =========================================
     MENU LATERAL DEL CLIENTE
     ========================================= --}}
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
        <p class="nav-section-title">BENEFICIOS</p>
        <a href="/cliente/puntos" class="nav-item active">
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
    <h1 style="font-family: 'Abril Fatface', cursive; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.3rem;">Mis Puntos</h1>
    <p style="color: var(--text-secondary); font-size: 0.95rem;">Gana, acumula y canjea puntos por increíbles recompensas.</p>
@endsection

@section('content')
<div class="puntos-container">

    <div class="banner-dorado">
        <div>
            <h2 class="banner-title">¡Hola, {{ explode(' ', $user->usr_nombre)[0] }}!</h2>
            <p class="banner-subtitle">Llevas {{ $diasRegistrado }} días siendo parte de nuestra familia.</p>
            <div style="display: flex; align-items: baseline; gap: 10px;">
                <span class="puntos-big">{{ $puntosDisponibles }}</span>
                <span style="font-weight: bold; font-size: 1.2rem;">puntos disponibles</span>
            </div>
        </div>
        <div class="nivel-badge">
            <div class="nivel-icon">
                @if($nivelActual == 'Bronce') 🥉 @elseif($nivelActual == 'Plata') 🥈 @elseif($nivelActual == 'Oro') 🥇 @else 💎 @endif
            </div>
            <div class="nivel-text">{{ $nivelActual }}</div>
            <div style="font-size: 0.8rem; margin-top: 5px; opacity: 0.8;">Nivel Actual</div>
        </div>
    </div>

    <div class="stats-puntos">
        <div class="stat-box">
            <div class="stat-icon">💰</div>
            <div class="stat-value">{{ $puntosTotales }}</div>
            <div class="stat-label">Puntos Históricos</div>
        </div>
        <div class="stat-box">
            <div class="stat-icon">✨</div>
            <div class="stat-value">{{ $puntosDisponibles }}</div>
            <div class="stat-label">Disponibles Hoy</div>
        </div>
        <div class="stat-box">
            <div class="stat-icon">🎯</div>
            <div class="stat-value">{{ $puntosParaSiguiente }}</div>
            <div class="stat-label">Para Siguiente Nivel</div>
        </div>
    </div>

    @if(isset($premiosPendientes) && $premiosPendientes->count() > 0)
    <div style="margin-top: 10px;">
        <h3 style="font-family: 'Abril Fatface', cursive; font-size: 1.5rem; margin-bottom: 15px; color: var(--dorado);">🎟️ Mis Premios por Reclamar</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
            @foreach($premiosPendientes as $pendiente)
            <div style="background: linear-gradient(135deg, rgba(250, 211, 112, 0.1) 0%, rgba(26, 26, 26, 1) 100%); border: 1px dashed var(--dorado); border-radius: 12px; padding: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="color: var(--dorado); font-size: 0.75rem; text-transform: uppercase; font-weight: bold; letter-spacing: 1px; margin-bottom: 5px;">VOUCHER VÁLIDO</div>
                    <div style="color: white; font-weight: bold; font-size: 1.1rem;">{{ $pendiente->can_premio_nombre }}</div>
                    <div style="color: var(--text-muted); font-size: 0.8rem; margin-top: 5px;">Canjeado el: {{ \Carbon\Carbon::parse($pendiente->created_at)->format('d/m/Y') }}</div>
                </div>
                <div style="font-size: 2rem; opacity: 0.8;">🎁</div>
            </div>
            @endforeach
        </div>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 10px;">* Presenta tu cédula en recepción o indica a tu estilista para hacer efectivos estos premios.</p>
    </div>
    @endif

    <div class="historial-box" style="margin-top: 10px;">
        <div class="h-title">📋 Últimos Movimientos</div>
        
        @if(isset($historialPuntos) && count($historialPuntos) > 0)
            @foreach(array_slice($historialPuntos, 0, 5) as $movimiento)
            <div class="h-item">
                <div class="h-left">
                    <div class="h-icon" @style([
    'background: ' . ($movimiento['color'] == '#ef4444' ? 'rgba(239, 68, 68, 0.1)' : 'rgba(74, 222, 128, 0.1)'),
    'color: ' . $movimiento['color']
])>
    {!! $movimiento['color'] == '#ef4444' ? '🎁' : '✂️' !!}
</div>
                    <div>
                        <div class="h-name">{{ $movimiento['titulo'] }}</div>
                        <div class="h-date">{{ $movimiento['fecha'] }}</div>
                    </div>
                </div>
<div class="h-points" @style(['color: ' . $movimiento['color']])>{{ $movimiento['puntos'] }} pts</div>
            </div>
            @endforeach
        @else
            <p style="color: var(--text-muted); text-align: center; padding: 20px;">Aún no tienes movimientos. ¡Agenda tu primera cita para empezar a ganar puntos!</p>
        @endif
    </div>

    <div style="margin-top: 20px;">
        <h3 style="font-family: 'Abril Fatface', cursive; font-size: 1.8rem; margin-bottom: 20px;">🎁 Premios Disponibles</h3>
        <div class="premios-grid">
            
            @foreach($premios as $premio)
            <div class="premio-card">
                <span class="badge-tipo">{{ $premio['tipo'] }}</span>
                <div class="p-icon">{{ $premio['icono'] }}</div>
                <div class="p-name">{{ $premio['nombre'] }}</div>
                <div class="p-desc">{{ $premio['desc'] }}</div>
                
                <div class="p-footer">
                    <div class="p-cost">{{ $premio['costo'] }} pts</div>
                    
                    @if($puntosDisponibles >= $premio['costo'])
                        <button class="btn-canjear" 
    data-id="{{ $premio['id'] }}"
    data-nombre="{{ $premio['nombre'] }}"
    data-costo="{{ $premio['costo'] }}"
    onclick="confirmarCanje(this.dataset.id, this.dataset.nombre, this.dataset.costo)">Canjear</button>
                    @else
                        <button class="btn-disabled" disabled>Insuficiente</button>
                    @endif
                </div>
            </div>
            @endforeach

        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmarCanje(id, nombre, costo) {
        Swal.fire({
            title: '¿Canjear Premio?',
            html: `Estás a punto de usar <b style="color:#fad370">${costo} puntos</b> por:<br><br><b style="font-size:1.2rem;">${nombre}</b>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#fad370',
            cancelButtonColor: '#333',
            confirmButtonText: '<span style="color:black; font-weight:bold;">Sí, canjear ahora</span>',
            cancelButtonText: 'Cancelar',
            background: '#151515', color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                
                Swal.fire({title: 'Procesando canje...', allowOutsideClick: false, didOpen: () => Swal.showLoading(), background: '#151515', color: '#fff'});

                fetch('/cliente/puntos/canjear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ premio_id: id })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: data.titulo,
                            html: data.mensaje, 
                            background: '#151515', color: '#fff', confirmButtonColor: '#fad370'
                        }).then(() => {
                            window.location.reload(); 
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
            }
        });
    }
</script>
@endsection