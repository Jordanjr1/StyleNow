@extends('layouts.app')

@section('title', 'Mis Calificaciones - StyleNow')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
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
        max-width: 1000px;
        margin: 0 auto;
    }

    /* Pestañas de Navegación Locales */
    .empleado-container .emp-nav-tabs { display: flex; gap: 4px; background: var(--bg-card); padding: 6px; border-radius: 12px; margin-bottom: 25px; border: 1px solid var(--border-color); width: fit-content; }
    .empleado-container .emp-nav-tab { padding: 10px 20px; border: none; background: none; color: var(--text-secondary); font-size: 13px; font-weight: 600; cursor: pointer; border-radius: 8px; transition: 0.3s; display: flex; align-items: center; gap: 8px; text-decoration: none; }
    .empleado-container .emp-nav-tab:hover { color: var(--text-primary); }
    .empleado-container .emp-nav-tab.active { background: rgba(250, 211, 112, 0.1); border: 1px solid var(--dorado); color: var(--dorado); }

    /* ── Calificación destacada (banner) ── */
    .calificacion-destacada {
        background: linear-gradient(135deg, #1f1a0a 0%, #2a2000 60%, #1a1500 100%);
        border: 1px solid rgba(250, 211, 112, 0.35);
        border-radius: 16px;
        padding: 40px 30px;
        text-align: center;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    .calificacion-destacada::before {
        content: ''; position: absolute; inset: 0;
        background: radial-gradient(ellipse at top right, rgba(250,211,112,0.12) 0%, transparent 60%);
        pointer-events: none;
    }

    .calificacion-destacada::after {
        content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px;
        background: var(--dorado); border-radius: 16px 16px 0 0;
    }

    .calificacion-content { position: relative; z-index: 1; }
    .puntuacion-grande { font-size: 72px; font-weight: 700; color: var(--dorado); line-height: 1; margin-bottom: 10px; }
    .estrellas-grandes { font-size: 32px; color: var(--dorado); margin-bottom: 20px; letter-spacing: 5px; }
    .star-empty { color: rgba(250, 211, 112, 0.3); }

    /* ── Stats grid ── */
    .estadisticas-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 30px; }
    .estadistica-card { background: var(--bg-card); border-radius: 16px; padding: 25px; border: 1px solid var(--border-color); text-align: center; position: relative; }
    .estadistica-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: var(--dorado); border-radius: 16px 16px 0 0; }
    .estadistica-numero { font-size: 2rem; font-weight: 700; color: var(--dorado); margin-bottom: 8px; line-height: 1.2; }
    .estadistica-card div:last-child { font-size: 14px; color: var(--text-secondary); font-weight: 500; }

    /* ── Distribución ── */
    .section-card { background: var(--bg-card); border-radius: 16px; border: 1px solid var(--border-color); margin-bottom: 30px; overflow: hidden; }
    .section-header { padding: 20px 25px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px; font-size: 16px; font-weight: 600; color: var(--text-primary); }

    .distribucion-wrap { padding: 25px; }
    .distribucion-item { display: flex; align-items: center; margin-bottom: 15px; }
    .distribucion-item:last-child { margin-bottom: 0; }
    .distribucion-estrella { width: 80px; color: var(--dorado); font-size: 14px; letter-spacing: 2px; }
    .distribucion-bar { flex: 1; height: 10px; background: var(--bg-secondary); border-radius: 10px; overflow: hidden; margin: 0 15px; }
    .distribucion-fill { height: 100%; background: linear-gradient(90deg, var(--dorado), #f5b800); border-radius: 10px; transition: width 1s ease; }
    .distribucion-count { width: 80px; text-align: right; font-size: 13px; color: var(--text-secondary); }

    /* ── Comentarios ── */
    .comentario-card { padding: 22px 25px; border-bottom: 1px solid var(--border-color); transition: all 0.3s ease; }
    .comentario-card:last-child { border-bottom: none; }
    .comentario-card:hover { background: var(--hover-bg); }

    .comentario-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
    .comentario-cliente { font-weight: 600; color: var(--text-primary); font-size: 16px; margin-bottom: 4px; }
    .comentario-servicio { font-size: 13px; color: var(--text-secondary); }
    .comentario-estrellas { color: var(--dorado); font-size: 16px; letter-spacing: 2px; }

    .comentario-texto { color: #d1d5db; line-height: 1.6; margin-bottom: 15px; font-style: italic; font-size: 14px; }

    .comentario-respuesta { background: rgba(250, 211, 112, 0.05); padding: 15px; border-radius: 12px; border-left: 3px solid var(--dorado); margin-top: 12px; }
    .comentario-respuesta-label { font-weight: 600; margin-bottom: 5px; color: var(--dorado); font-size: 13px; }
    .comentario-respuesta div:last-child { font-size: 14px; color: #d1d5db; }

    .responder-btn { background: rgba(250, 211, 112, 0.1); color: var(--dorado); border: 1px solid rgba(250, 211, 112, 0.3); padding: 8px 18px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; font-family: 'Poppins', sans-serif; transition: 0.3s; }
    .responder-btn:hover { background: var(--dorado); color: #0a0a0a; }

    @media (max-width: 768px) {
        .estadisticas-grid { grid-template-columns: 1fr; }
        .comentario-header { flex-direction: column; gap: 8px; }
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
        <a href="{{ route('empleado.calificaciones') }}" class="nav-item active"><span class="nav-icon">⭐</span><span class="nav-text">Calificaciones</span></a>
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
        <a href="/empleado/perfil" class="nav-item"><span class="nav-icon">👤</span><span class="nav-text">Mi Perfil</span></a>
    </div>
</nav>
@endsection

@section('topbar-left')
    <h1 style="font-family: 'Abril Fatface', cursive; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.3rem;">Mis Calificaciones</h1>
    <p style="color: var(--text-secondary); font-size: 0.95rem;">Opiniones de tus clientes</p>
@endsection

@section('content')
<div class="empleado-container">

    <div class="emp-nav-tabs">
        <a href="{{ route('empleado.comisiones') }}" class="emp-nav-tab">💰 Comisiones</a>
        <a href="{{ route('empleado.calificaciones') }}" class="emp-nav-tab active">⭐ Calificaciones</a>
    </div>

    <div class="calificacion-destacada">
        <div class="calificacion-content">
            <div class="puntuacion-grande">{{ number_format($estadisticas['promedio'], 1) }}</div>
            <div class="estrellas-grandes">
                @php
                    $estrellas_llenas = floor($estadisticas['promedio']);
                    $media_estrella = $estadisticas['promedio'] - $estrellas_llenas >= 0.5;
                @endphp
                {!! str_repeat('★', $estrellas_llenas) !!}
                {!! $media_estrella ? '½' : '' !!}
                <span class="star-empty">{!! str_repeat('★', 5 - $estrellas_llenas - ($media_estrella ? 1 : 0)) !!}</span>
            </div>
            <div style="font-size: 18px; color: #ffffff; margin-bottom: 10px;">Calificación promedio</div>
            <div style="color: #a0a0a0; font-size: 14px;">Basado en {{ $estadisticas['total_calificaciones'] }} valoraciones</div>
        </div>
    </div>

    <div class="estadisticas-grid">
        <div class="estadistica-card">
            <div class="estadistica-numero">{{ number_format($estadisticas['mes_actual'], 1) }}</div>
            <div>Promedio este mes</div>
        </div>
        <div class="estadistica-card">
            <div class="estadistica-numero">{{ $estadisticas['distribucion'][5] }}</div>
            <div>Valoraciones 5 estrellas</div>
        </div>
        <div class="estadistica-card">
            <div class="estadistica-numero" style="font-size: 1.2rem; display:flex; align-items:center; justify-content:center; height: 38px;">{{ $estadisticas['servicio_mejor_calificado'] }}</div>
            <div>Servicio mejor calificado</div>
        </div>
    </div>

    <div class="section-card">
        <div class="section-header"><span>📊</span> Distribución de Calificaciones</div>
        <div class="distribucion-wrap">
            @foreach([5, 4, 3, 2, 1] as $estrellas)
                @php 
                    $cantidad = $estadisticas['distribucion'][$estrellas];
                    $porcentaje = $estadisticas['total_calificaciones'] > 0 ? ($cantidad / $estadisticas['total_calificaciones']) * 100 : 0; 
                @endphp
                <div class="distribucion-item">
                    <div class="distribucion-estrella">
                        {!! str_repeat('★', $estrellas) !!}<span class="star-empty">{!! str_repeat('★', 5 - $estrellas) !!}</span>
                    </div>
                    <div class="distribucion-bar">
<div class="distribucion-fill" @style(['width: ' . $porcentaje . '%'])></div>                    </div>
                    <div class="distribucion-count">
                        {{ $cantidad }} ({{ number_format($porcentaje, 1) }}%)
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="section-card">
        <div class="section-header"><span>💬</span> Comentarios Recientes</div>

        @forelse($calificaciones as $cal)
            <div class="comentario-card">
                <div class="comentario-header">
                    <div>
                        <div class="comentario-cliente">{{ $cal['cliente'] }}</div>
                        <div class="comentario-servicio">{{ $cal['servicio'] }} • {{ $cal['fecha'] }}</div>
                    </div>
                    <div class="comentario-estrellas">
                        {!! str_repeat('★', $cal['calificacion']) !!}<span class="star-empty">{!! str_repeat('★', 5 - $cal['calificacion']) !!}</span>
                    </div>
                </div>

                <div class="comentario-texto">"{{ $cal['comentario'] }}"</div>

                @if($cal['respuesta'])
                    <div class="comentario-respuesta">
                        <div class="comentario-respuesta-label">Tu respuesta:</div>
                        <div>{{ $cal['respuesta'] }}</div>
                    </div>
                @else
<button class="responder-btn" data-id="{{ $cal['id'] }}" onclick="responderComentario(this.dataset.id)">💬 Responder</button>                @endif
            </div>
        @empty
            <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                Aún no tienes comentarios ni calificaciones en tus citas.
            </div>
        @endforelse
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    async function responderComentario(id) {
        const { value: respuesta } = await Swal.fire({
            title: 'Responder a Cliente',
            input: 'textarea',
            inputPlaceholder: 'Escribe un mensaje de agradecimiento...',
            inputAttributes: { 'aria-label': 'Escribe tu respuesta aquí' },
            showCancelButton: true,
            confirmButtonText: 'Enviar Respuesta',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#fad370',
            background: '#1a1a1a',
            color: '#fff'
        });

        if (respuesta) {
            try {
                Swal.fire({title: 'Enviando...', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
                
                const res = await fetch(`/empleado/calificaciones/${id}/responder`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ respuesta: respuesta })
                });

                const data = await res.json();
                
                if (data.success) {
                    Swal.fire({icon: 'success', title: '¡Respuesta enviada!', background: '#1a1a1a', color: '#fff', confirmButtonColor: '#fad370'})
                    .then(() => window.location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'Falla en la conexión.', 'error');
            }
        }
    }

    // Animación de barras de distribución
    document.addEventListener('DOMContentLoaded', function() {
        const bars = document.querySelectorAll('.distribucion-fill');
        bars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => { bar.style.width = width; }, 300);
        });
    });
</script>
@endsection