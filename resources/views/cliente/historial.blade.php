@extends('layouts.app')

@section('title', 'Historial de Citas - StyleNow')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    :root {
        --bg-main: #0a0a0a; --bg-card: #151515; --bg-hover: #1f1f1f;
        --dorado: #fad370; --text-main: #ffffff; --text-muted: #888888;
        --border-color: #2a2a2a;
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

    .page-header { margin-bottom: 2.5rem; }
    .page-title { font-family: 'Abril Fatface', cursive; font-size: 2.5rem; color: var(--text-main); margin-bottom: 0.5rem; }
    .page-subtitle { color: var(--text-muted); font-size: 1rem; }

    /* CONTENEDOR */
    .history-container { max-width: 900px; display: flex; flex-direction: column; gap: 1rem; }

    /* TARJETA PRINCIPAL (WRAPPER) */
    .history-card-wrapper {
        background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem 2rem; transition: 0.3s; margin-bottom: 0.5rem;
    }
    .history-card-wrapper:hover { border-color: rgba(250, 211, 112, 0.4); background: var(--bg-hover); }

    .card-top { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }

    .h-left { display: flex; gap: 20px; align-items: center; }
    .h-icon-box { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .icon-success { background: rgba(74, 222, 128, 0.1); color: #4ade80; }
    .icon-danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

    .h-info { display: flex; flex-direction: column; }
    .h-services { color: white; font-size: 1.1rem; font-weight: 600; margin-bottom: 5px; }
    .h-details { color: var(--text-muted); font-size: 0.85rem; display: flex; align-items: center; gap: 10px; }
    .h-dot { width: 4px; height: 4px; background: #444; border-radius: 50%; }

    .h-right { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; }
    .h-price { font-size: 1.2rem; font-weight: bold; color: var(--dorado); }
    
    .badge { padding: 5px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
    .badge-completada { background: rgba(74, 222, 128, 0.1); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.2); }
    .badge-cancelada { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }

    /* BOTÓN CALIFICAR */
    .btn-rate {
        background: rgba(250, 211, 112, 0.1); color: var(--dorado); border: 1px solid var(--dorado); padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: bold; transition: 0.3s; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;
    }
    .btn-rate:hover { background: var(--dorado); color: #000; box-shadow: 0 0 15px rgba(250, 211, 112, 0.4); }

    /* ESTRELLAS YA CALIFICADAS */
    .static-stars { color: var(--dorado); font-size: 1rem; letter-spacing: 2px; }

    /* =========================
       SECCIÓN DE COMENTARIOS Y RESPUESTAS
       ========================= */
    .feedback-section {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px dashed var(--border-color);
        width: 100%;
        animation: fadeIn 0.5s ease;
    }
    .customer-comment {
        color: #ccc;
        font-style: italic;
        font-size: 0.95rem;
        margin-bottom: 10px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }
    .quote-icon { color: var(--dorado); font-size: 1.2rem; font-family: serif; line-height: 1; }
    
    .employee-reply {
        background: linear-gradient(90deg, rgba(250, 211, 112, 0.08) 0%, rgba(21, 21, 21, 0) 100%);
        border-left: 3px solid var(--dorado);
        padding: 12px 15px;
        border-radius: 0 8px 8px 0;
        margin-top: 10px;
        margin-left: 20px;
    }
    .reply-author {
        color: var(--dorado);
        font-size: 0.75rem;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 5px;
        display: block;
    }
    .reply-text { color: white; font-size: 0.9rem; margin: 0; line-height: 1.5; }

    /* =========================
       MODAL DE CALIFICACIÓN
       ========================= */
    .rating-modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); display: none; justify-content: center; align-items: center; z-index: 1000; animation: fadeIn 0.3s;
    }
    .rating-modal {
        background: #1a1a1a; border: 1px solid var(--border-color); width: 90%; max-width: 450px; border-radius: 16px; padding: 2rem; text-align: center; position: relative; box-shadow: 0 10px 40px rgba(0,0,0,0.5);
    }
    .modal-title { font-family: 'Abril Fatface', cursive; font-size: 1.8rem; color: white; margin-bottom: 10px; }
    .modal-subtitle { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 25px; }

    .star-rating { display: flex; justify-content: center; gap: 10px; margin-bottom: 25px; }
    .star-btn { font-size: 2.5rem; color: #333; cursor: pointer; transition: 0.2s; background: none; border: none; padding: 0;}
    .star-btn:hover, .star-btn.active { color: var(--dorado); transform: scale(1.2); }

    .modal-textarea {
        width: 100%; background: #0f0f0f; border: 1px solid #333; color: white; border-radius: 8px; padding: 12px; margin-bottom: 20px; font-family: 'Poppins'; outline: none; resize: none;
    }
    .modal-textarea:focus { border-color: var(--dorado); }

    .modal-actions { display: flex; gap: 10px; }
    .btn-submit { background: var(--dorado); color: black; border: none; padding: 12px; border-radius: 8px; font-weight: bold; flex: 1; cursor: pointer; transition: 0.3s; }
    .btn-submit:hover { box-shadow: 0 5px 15px rgba(250, 211, 112, 0.3); }
    .btn-close { background: #333; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; flex: 1; cursor: pointer; }
    
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    @media (max-width: 768px) {
        .card-top { flex-direction: column; align-items: flex-start; }
        .h-right { align-items: flex-start; width: 100%; flex-direction: row-reverse; justify-content: space-between; border-top: 1px solid var(--border-color); padding-top: 15px; }
    }
</style>
@endpush

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
        <a href="/cliente/historial" class="nav-item active">
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

@section('topbar-left')
    <h1 style="font-family: 'Abril Fatface', cursive; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.3rem;">Historial de Citas</h1>
    <p style="color: var(--text-secondary); font-size: 0.95rem;">Un registro de todos tus servicios y visitas anteriores.</p>
@endsection

@section('content')

@if($historialCitas->count() > 0)
    <div class="history-container">
        @foreach($historialCitas as $cita)
            @php
                $esCancelada = (strtolower($cita->cit_estadoCita) == 'cancelada' || strtolower($cita->cit_estadoCita) == 'no asistió');
                $esCompletada = (strtolower($cita->cit_estadoCita) == 'completada' || strtolower($cita->cit_estadoCita) == 'atendida');
            @endphp

            <div class="history-card-wrapper" id="card-cita-{{ $cita->cit_id }}">
                
                <div class="card-top">
                    <div class="h-left">
                        <div class="h-icon-box {{ $esCancelada ? 'icon-danger' : 'icon-success' }}">
                            {!! $esCancelada ? '✕' : '✓' !!}
                        </div>
                        
                        <div class="h-info">
                            <div class="h-services">{{ Str::limit($cita->cit_nombres_servicios, 50) }}</div>
                            <div class="h-details">
                                <span>📅 {{ $cita->fecha_formateada }}</span>
                                <span class="h-dot"></span>
                                <span>👤 {{ $cita->estilista_nombre }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="h-right">
                        <div style="text-align: right;">
                            <div class="h-price">${{ number_format($cita->cit_precio, 2) }}</div>
                            <span class="badge {{ $esCancelada ? 'badge-cancelada' : 'badge-completada' }}">
                                {{ $cita->cit_estadoCita }}
                            </span>
                        </div>
                        
                        @if($esCompletada)
                            @if($cita->cit_calificacion)
                                <div class="static-stars" title="Tu calificación: {{ $cita->cit_calificacion }} estrellas">
                                    @for($i=0; $i < $cita->cit_calificacion; $i++) ★ @endfor
                                </div>
                            @else
                                <button 
    data-id="{{ $cita->cit_id }}"
    data-nombre="{{ $cita->estilista_nombre }}"
    onclick="abrirModal(this.dataset.id, this.dataset.nombre)" 
    class="btn-rate">
    ⭐ Calificar 
</button>
                            @endif
                        @endif
                    </div>
                </div>

                @if($cita->cit_comentario || $cita->cit_respuesta_empleado)
                    <div class="feedback-section">
                        @if($cita->cit_comentario)
                            <div class="customer-comment">
                                <span class="quote-icon">"</span>
                                <span>{{ $cita->cit_comentario }}</span>
                            </div>
                        @endif

                        @if($cita->cit_respuesta_empleado)
                            <div class="employee-reply">
                                <span class="reply-author">↳ Respuesta de {{ $cita->estilista_nombre }}</span>
                                <p class="reply-text">{{ $cita->cit_respuesta_empleado }}</p>
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <div class="empty-icon">📂</div>
        <h2 class="empty-title">Tu historial está limpio</h2>
        <p style="color: var(--text-muted); margin-bottom: 20px;">Aún no tienes citas finalizadas o pasadas en nuestro sistema.</p>
        <a href="/cliente/citas/nueva" style="background: var(--dorado); color: #000; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block;">✨ Agendar mi Primera Cita</a>
    </div>
@endif

<div id="modalRating" class="rating-modal-overlay">
    <div class="rating-modal">
        <h3 class="modal-title">¿Qué tal tu experiencia?</h3>
        <p class="modal-subtitle">Califica tu servicio con <span id="modalStylistName" style="color: var(--dorado); font-weight:bold;"></span></p>

        <div class="star-rating" id="starContainer">
            <button class="star-btn" onclick="setRating(1)">★</button>
            <button class="star-btn" onclick="setRating(2)">★</button>
            <button class="star-btn" onclick="setRating(3)">★</button>
            <button class="star-btn" onclick="setRating(4)">★</button>
            <button class="star-btn" onclick="setRating(5)">★</button>
        </div>

        <textarea id="ratingComment" class="modal-textarea" rows="3" placeholder="Escribe un comentario opcional sobre el servicio... (Ej: Me encantó el corte)"></textarea>

        <div class="modal-actions">
            <button class="btn-close" onclick="cerrarModal()">Cancelar</button>
            <button class="btn-submit" onclick="enviarCalificacion()">Enviar Opinión</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentRating = 0;
    let currentCitaId = null;

    function abrirModal(id, nombreEstilista) {
        currentCitaId = id;
        currentRating = 0;
        document.getElementById('modalStylistName').innerText = nombreEstilista;
        document.getElementById('ratingComment').value = '';
        updateStars(0);
        document.getElementById('modalRating').style.display = 'flex';
    }

    function cerrarModal() {
        document.getElementById('modalRating').style.display = 'none';
    }

    function setRating(val) {
        currentRating = val;
        updateStars(val);
    }

    function updateStars(val) {
        const stars = document.querySelectorAll('.star-btn');
        stars.forEach((star, index) => {
            if (index < val) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });
    }

    async function enviarCalificacion() {
        if (currentRating === 0) {
            Swal.fire('Oops', 'Por favor selecciona al menos una estrella ⭐', 'warning');
            return;
        }

        const comentario = document.getElementById('ratingComment').value;

        try {
            Swal.fire({title: 'Guardando...', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
            
            const res = await fetch(`/cliente/citas/${currentCitaId}/calificar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ calificacion: currentRating, comentario: comentario })
            });

            const data = await res.json();

            if (res.ok && data.success) {
                cerrarModal();
                Swal.fire({
                    icon: 'success', 
                    title: '¡Gracias por tu opinión!', 
                    text: 'Nos ayudas a mejorar cada día.',
                    background: '#151515', color: '#fff', confirmButtonColor: '#fad370'
                }).then(() => {
                    location.reload(); 
                });
            } else {
                Swal.fire('Error', 'No se pudo guardar la calificación.', 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'Error de conexión', 'error');
        }
    }

    window.onclick = function(event) {
        const modal = document.getElementById('modalRating');
        if (event.target == modal) {
            cerrarModal();
        }
    }
</script>
@endsection