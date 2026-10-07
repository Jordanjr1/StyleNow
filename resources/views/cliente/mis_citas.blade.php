@extends('layouts.app')

@section('title', 'Mis Citas - StyleNow')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    :root {
        --bg-main: #0a0a0a; 
        --bg-card: #151515; 
        --bg-hover: #1f1f1f;
        --dorado: #fad370; 
        --text-main: #ffffff; 
        --text-muted: #888888;
        --border-color: #2a2a2a;
    }

    /* ESTILOS DEL MENÚ LATERAL (Por si no están globales) */
    .sidebar-nav { padding: 1rem 0; }
    .nav-section { margin-bottom: 1.5rem; }
    .nav-section-title { padding: 0 1.5rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); margin-bottom: 0.5rem; font-weight: 700; }
    .nav-item { display: flex; align-items: center; gap: 1rem; padding: 0.9rem 1.5rem; color: var(--text-primary); text-decoration: none; transition: all 0.3s ease; cursor: pointer; position: relative; }
    .nav-item:hover { background: rgba(250, 211, 112, 0.1); border-left: 3px solid var(--dorado); padding-left: calc(1.5rem - 3px); }
    .nav-item.active { background: rgba(250, 211, 112, 0.15); border-left: 3px solid var(--dorado); padding-left: calc(1.5rem - 3px); color: var(--dorado); }
    .nav-icon { font-size: 1.2rem; width: 24px; text-align: center; }
    .nav-text { flex: 1; font-size: 0.95rem; font-weight: 500; }

    /* CONTENEDOR DE CITAS */
    .appointments-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; max-width: 900px; }

    /* ESTILO "TICKET" VIP */
    .ticket-card {
        display: flex;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
    }
    .ticket-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(250, 211, 112, 0.08);
        border-color: rgba(250, 211, 112, 0.3);
    }

    /* Borde dorado lateral */
    .ticket-card::before {
        content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 4px; background: var(--dorado);
    }

    /* SECCIÓN IZQUIERDA: FECHA (CALENDARIO) */
    .ticket-date {
        background: #111;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-width: 140px;
        border-right: 2px dashed var(--border-color);
    }
    .t-month { color: var(--dorado); font-weight: bold; font-size: 0.9rem; letter-spacing: 2px; text-transform: uppercase; }
    .t-day { font-family: 'Abril Fatface', cursive; color: white; font-size: 3rem; line-height: 1; margin: 5px 0; }
    .t-weekday { color: var(--text-muted); font-size: 0.85rem; }

    /* SECCIÓN CENTRAL: DETALLES */
    .ticket-details {
        padding: 1.5rem 2rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .t-time { color: var(--dorado); font-size: 1.2rem; font-weight: bold; margin-bottom: 5px; display: flex; align-items: center; gap: 8px;}
    .t-services { color: white; font-size: 1.3rem; font-weight: 600; margin-bottom: 8px; }
    .t-stylist { color: var(--text-muted); font-size: 0.95rem; display: flex; align-items: center; gap: 8px; }
    
    .ticket-meta {
        display: flex; gap: 20px; margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border-color);
    }
    .meta-item { display: flex; flex-direction: column; }
    .m-label { font-size: 0.75rem; color: #666; text-transform: uppercase; letter-spacing: 1px; }
    .m-value { font-size: 0.95rem; color: white; font-weight: 600; }

    /* SECCIÓN DERECHA: ESTADO Y ACCIONES */
    .ticket-actions {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: space-between;
        min-width: 200px;
    }
    
    .status-badge {
        padding: 6px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;
    }
    .status-confirmada { background: rgba(74, 222, 128, 0.1); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.3); }
    .status-pendiente { background: rgba(250, 211, 112, 0.1); color: var(--dorado); border: 1px solid rgba(250, 211, 112, 0.3); }

    .btn-cancel {
        background: transparent; color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); padding: 8px 15px; border-radius: 8px; cursor: pointer; transition: 0.3s; font-size: 0.85rem; font-weight: bold; margin-top: 10px;
    }
    .btn-cancel:hover { background: #ef4444; color: white; border-color: #ef4444; }
    .no-cancel { color: #666; font-size: 0.75rem; text-align: right; max-width: 150px; margin-top: 10px;}

    /* EMPTY STATE */
    .empty-state {
        background: var(--bg-card); border: 1px dashed var(--border-color); border-radius: 16px; padding: 4rem 2rem; text-align: center; max-width: 900px; margin-top: 2rem;
    }
    .empty-icon { font-size: 4rem; margin-bottom: 1rem; opacity: 0.5; }
    .empty-title { color: white; font-size: 1.5rem; margin-bottom: 1rem; font-family: 'Abril Fatface', cursive;}
    .btn-primary { background: var(--dorado); color: #000; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block; transition: 0.3s; margin-top: 15px;}
    .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(250, 211, 112, 0.3); }

    @media (max-width: 768px) {
        .ticket-card { flex-direction: column; }
        .ticket-date { border-right: none; border-bottom: 2px dashed var(--border-color); flex-direction: row; justify-content: space-between; padding: 1rem 1.5rem; min-width: auto;}
        .t-day { font-size: 1.5rem; margin: 0;}
        .ticket-actions { align-items: flex-start; border-top: 1px solid var(--border-color); flex-direction: row; padding: 1rem 1.5rem;}
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
        <a href="/cliente/mis-citas" class="nav-item active">
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

{{-- =========================================
     BARRA SUPERIOR (TOPBAR)
     ========================================= --}}
@section('topbar-left')
    <h1 style="font-family: 'Abril Fatface', cursive; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.3rem;">Mis Citas Próximas</h1>
    <p style="color: var(--text-secondary); font-size: 0.95rem;">Revisa los detalles o cancela tus reservaciones activas.</p>
@endsection

{{-- =========================================
     CONTENIDO PRINCIPAL DE LA VISTA
     ========================================= --}}
@section('content')

@if($citasActivas->count() > 0)
    <div class="appointments-grid">
        @foreach($citasActivas as $cita)
            <div class="ticket-card" id="cita-row-{{ $cita->cit_id }}">
                
                <div class="ticket-date">
                    <span class="t-month">{{ $cita->mes_nombre }}</span>
                    <span class="t-day">{{ $cita->dia_numero }}</span>
                    <span class="t-weekday">{{ $cita->dia_nombre }}</span>
                </div>

                <div class="ticket-details">
                    <div class="t-time">
                        🕒 {{ $cita->hora_formato }}
                    </div>
                    <div class="t-services">{{ Str::limit($cita->cit_nombres_servicios, 50) }}</div>
                    <div class="t-stylist">👤 Profesional: <strong style="color: white; margin-left: 5px;">{{ $cita->estilista_nombre }}</strong></div>
                    
                    <div class="ticket-meta">
                        <div class="meta-item">
                            <span class="m-label">Duración</span>
                            <span class="m-value">{{ $cita->cit_duracionTotal }} min</span>
                        </div>
                        <div class="meta-item">
                            <span class="m-label">Total a Pagar</span>
                            <span class="m-value" style="color: var(--dorado);">${{ number_format($cita->cit_precio, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="ticket-actions">
                    @if($cita->cit_estadoCita == 'Confirmada')
                        <span class="status-badge status-confirmada">✓ Confirmada</span>
                    @else
                        <span class="status-badge status-pendiente">⏳ Pendiente</span>
                    @endif

                    @if($cita->se_puede_cancelar)
                       <button class="btn-cancel" 
    data-id="{{ $cita->cit_id }}"
    onclick="cancelarCita(this.dataset.id)">✕ Cancelar Cita</button>
                    @else
                        <span class="no-cancel">Demasiado tarde para cancelar online. Por favor, llámanos.</span>
                    @endif
                </div>

            </div>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <div class="empty-icon">📅</div>
        <h2 class="empty-title">No tienes citas próximas</h2>
        <p style="color: var(--text-muted); margin-bottom: 20px;">Parece que es momento de un cambio de look o mantenimiento. ¡Agenda tu próxima visita hoy mismo!</p>
        <a href="/cliente/citas/nueva" class="btn-primary">✨ Agendar Nueva Cita</a>
    </div>
@endif

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    async function cancelarCita(id) {
        const result = await Swal.fire({
            title: '¿Seguro que deseas cancelar?',
            text: 'Recuerda: Cancelar 3 veces resultará en el bloqueo automático de tu cuenta.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#333',
            confirmButtonText: 'Sí, cancelar cita',
            cancelButtonText: 'Volver',
            background: '#1a1a1a', color: '#fff'
        });

        if (result.isConfirmed) {
            try {
                Swal.fire({title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading(), background: '#1a1a1a', color: '#fff'});

                const res = await fetch(`/cliente/citas/${id}/cancelar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await res.json();

                if (data.success) {
                    if (data.banned) {
                        // El cliente llegó al límite (3 cancelaciones)
                        Swal.fire({
                            icon: 'error',
                            title: 'Cuenta Bloqueada 🚫',
                            text: data.message,
                            background: '#1a1a1a', color: '#fff',
                            confirmButtonColor: '#fad370',
                            allowOutsideClick: false
                        }).then(() => {
                            // Lo mandamos a la página de login a la fuerza
                            window.location.href = '/login'; 
                        });
                    } else {
                        // Cancelación normal, le avisamos cuántas le quedan
                        Swal.fire({
                            icon: 'warning',
                            title: 'Cita Cancelada',
                            text: data.message,
                            background: '#1a1a1a', color: '#fff',
                            confirmButtonColor: '#fad370'
                        }).then(() => {
                            location.reload(); // Recargamos para que desaparezca la cita
                        });
                    }
                } else {
                    Swal.fire({icon: 'error', title: 'Error', text: data.message, background: '#1a1a1a', color: '#fff'});
                }
            } catch(e) {
                Swal.fire({icon: 'error', title: 'Falla de conexión', background: '#1a1a1a', color: '#fff'});
            }
        }
    }
</script>
@endsection