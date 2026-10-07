@extends('layouts.app')

@section('title', 'Reportar Falta de Inventario - StyleNow')

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

    /* Stats Grid */
    .emp-stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 30px; }
    .emp-stat-card { background: var(--bg-card); padding: 1.5rem; border-radius: 15px; border: 1px solid var(--border-color); position: relative; overflow: hidden; }
    .emp-stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: var(--dorado); }
    .emp-stat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .emp-stat-icon { width: 45px; height: 45px; border-radius: 10px; background: rgba(250, 211, 112, 0.15); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .emp-stat-value { font-size: 2rem; font-weight: 700; color: var(--dorado); margin-bottom: 0.3rem; }
    .emp-stat-label { font-size: 0.9rem; color: var(--text-secondary); }

    /* Formulario de Reporte */
    .report-card { background: var(--bg-card); border-radius: 16px; border: 1px solid var(--border-color); padding: 30px; margin-bottom: 30px; }
    .section-title { font-size: 1.2rem; font-weight: bold; color: var(--text-primary); display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px dashed var(--border-color); }
    
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 8px; }
    .form-label { font-size: 13px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; }
    .form-input, .form-select, .form-textarea { padding: 14px 18px; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 12px; color: var(--text-primary); font-family: 'Poppins', sans-serif; outline: none; transition: 0.3s; }
    .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: var(--dorado); box-shadow: 0 0 0 2px rgba(250, 211, 112, 0.1); }
    .form-textarea { resize: vertical; min-height: 100px; }
    
    .btn-submit { background: var(--dorado); color: #000; font-weight: bold; font-size: 15px; padding: 15px 30px; border: none; border-radius: 12px; cursor: pointer; transition: 0.3s; width: 100%; margin-top: 10px; font-family: 'Poppins', sans-serif; }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(250, 211, 112, 0.3); }

    /* Tabla de Historial */
    .history-card { background: var(--bg-card); border-radius: 16px; border: 1px solid var(--border-color); overflow: hidden; }
    .tabla-header { display: grid; grid-template-columns: 100px 1.5fr 1fr 1fr; padding: 15px 25px; background: var(--bg-secondary); font-weight: 600; border-bottom: 2px solid var(--border-color); font-size: 13px; color: var(--text-secondary); text-transform: uppercase; }
    .tabla-fila { display: grid; grid-template-columns: 100px 1.5fr 1fr 1fr; padding: 20px 25px; border-bottom: 1px solid var(--border-color); transition: 0.3s; align-items: center; }
    .tabla-fila:hover { background: var(--hover-bg); }
    .tabla-fila:last-child { border-bottom: none; }

    .fecha-col { font-size: 13px; color: var(--text-secondary); }
    .prod-col { font-weight: 600; color: var(--text-primary); font-size: 15px; display: flex; flex-direction: column; gap: 4px;}
    .prod-comment { font-size: 12px; color: var(--text-secondary); font-weight: normal; font-style: italic;}
    
    .badge-urgency { padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; width: fit-content; text-align: center;}
    .urgency-baja { background: rgba(74, 222, 128, 0.15); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.3); }
    .urgency-media { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
    .urgency-alta { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }

    .badge-status { padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
    .status-pendiente { background: rgba(250, 211, 112, 0.1); color: var(--dorado); border: 1px dashed var(--dorado); }
    .status-atendido { background: rgba(74, 222, 128, 0.1); color: #4ade80; border: 1px solid #4ade80; }

    @media (max-width: 768px) {
        .emp-stats-grid { grid-template-columns: 1fr; }
        .form-row { grid-template-columns: 1fr; }
        .tabla-header { display: none; }
        .tabla-fila { grid-template-columns: 1fr; gap: 10px; padding: 15px; border-left: 3px solid var(--dorado); background: var(--bg-secondary); margin-bottom: 10px; border-radius: 10px;}
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
        <a href="{{ route('empleado.calificaciones') }}" class="nav-item"><span class="nav-icon">⭐</span><span class="nav-text">Calificaciones</span></a>
    </div>

    <div class="nav-section">
        <p class="nav-section-title">Inventario</p>
        <a href="{{ route('empleado.reportar.falta') }}" class="nav-item active">
            <span class="nav-icon">📦</span><span class="nav-text">Reportar Falta</span>
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
    <h1 style="font-family: 'Abril Fatface', cursive; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.3rem;">Reportar Falta</h1>
    <p style="color: var(--text-secondary); font-size: 0.95rem;">Notifica al administrador sobre productos agotados</p>
@endsection

@section('content')
<div class="empleado-container">

    <div class="emp-stats-grid">
        <div class="emp-stat-card">
            <div class="emp-stat-header"><div class="emp-stat-icon">📋</div></div>
            <div class="emp-stat-value">{{ $estadisticas['total_reportes'] }}</div>
            <div class="emp-stat-label">Reportes Realizados</div>
        </div>
        <div class="emp-stat-card" style="border-color: #f59e0b;">
            <div class="emp-stat-header"><div class="emp-stat-icon" style="background: rgba(245,158,11,0.15); color: #f59e0b;">⏳</div></div>
            <div class="emp-stat-value" style="color: #f59e0b;">{{ $estadisticas['pendientes'] }}</div>
            <div class="emp-stat-label">Pendientes de Compra</div>
        </div>
        <div class="emp-stat-card" style="border-color: #4ade80;">
            <div class="emp-stat-header"><div class="emp-stat-icon" style="background: rgba(74,222,128,0.15); color: #4ade80;">✅</div></div>
            <div class="emp-stat-value" style="color: #4ade80;">{{ $estadisticas['atendidos'] }}</div>
            <div class="emp-stat-label">Reportes Atendidos</div>
        </div>
    </div>

    <div class="report-card">
        <div class="section-title"><span>📢</span> Crear Nuevo Reporte</div>
        
        <form id="formReporte">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Producto Faltante *</label>
                    <select id="producto_id" class="form-select" required>
    <option value="">Seleccione el producto agotado...</option>
    @foreach($productos as $prod)
        @if($prod->pro_stock > 5)
            <option value="{{ $prod->pro_id }}" disabled style="color: #666; background: #1a1a1a;">
                ✅ {{ $prod->pro_nombre }} - (Stock suficiente: {{ $prod->pro_stock }})
            </option>
        @else
            <option value="{{ $prod->pro_id }}" style="color: #fad370; font-weight: bold;">
                ⚠️ {{ $prod->pro_nombre }} - (Quedan: {{ $prod->pro_stock }})
            </option>
        @endif
    @endforeach
</select>
                </div>
                <div class="form-group">
                    <label class="form-label">Nivel de Urgencia *</label>
                    <select id="urgencia" class="form-select" required>
                        <option value="Baja">🟢 Baja (Puede esperar unos días)</option>
                        <option value="Media" selected>🟡 Media (Se necesita pronto)</option>
                        <option value="Alta">🔴 Alta (Afecta el servicio hoy mismo)</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label">Comentarios / Detalles (Opcional)</label>
                <textarea id="comentario" class="form-textarea" placeholder="Ej: Queda menos de un cuarto en la última botella de Shampoo..."></textarea>
            </div>

            <button type="submit" class="btn-submit">🚀 Enviar Reporte al Administrador</button>
        </form>
    </div>

    <div class="history-card">
        <div class="section-title" style="padding: 25px; margin: 0; border-bottom: 2px solid var(--border-color);">
            <span>📝</span> Mis Reportes Anteriores
        </div>
        
        <div class="tabla-header">
            <div>Fecha</div>
            <div>Producto Reportado</div>
            <div>Urgencia</div>
            <div>Estado del Administrador</div>
        </div>
        
        <div id="historialContenedor">
            @forelse($reportes as $rep)
                @php
                    $badgeUrgencia = 'urgency-' . strtolower($rep->rep_nivel_urgencia);
                    $fechaFormato = \Carbon\Carbon::parse($rep->rep_fecha)->format('d/m/Y H:i');
                @endphp
                <div class="tabla-fila">
                    <div class="fecha-col">{{ $fechaFormato }}</div>
                    <div class="prod-col">
                        {{ $rep->pro_nombre }}
                        @if($rep->rep_comentario)
                            <span class="prod-comment">"{{ Str::limit($rep->rep_comentario, 60) }}"</span>
                        @endif
                    </div>
                    <div>
                        <div class="badge-urgency {{ $badgeUrgencia }}">{{ $rep->rep_nivel_urgencia }}</div>
                    </div>
                    <div>
                        @if($rep->rep_estado === 'Pendiente')
                            <span class="badge-status status-pendiente">⏳ Pendiente Compra</span>
                        @else
                            <span class="badge-status status-atendido">✅ Reabastecido</span>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                    No has realizado ningún reporte de inventario aún.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('formReporte').addEventListener('submit', async function(e) {
        e.preventDefault();

        const producto_id = document.getElementById('producto_id').value;
        const urgencia = document.getElementById('urgencia').value;
        const comentario = document.getElementById('comentario').value;

        try {
            Swal.fire({title: 'Enviando reporte...', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
            
            const res = await fetch(`/empleado/reportar-falta`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ producto_id, urgencia, comentario })
            });

            const data = await res.json();
            
            if (res.ok && data.success) {
                Swal.fire({
                    icon: 'success', 
                    title: '¡Reporte Enviado!', 
                    text: 'El administrador ha sido notificado.',
                    background: '#1a1a1a', 
                    color: '#fff', 
                    confirmButtonColor: '#fad370'
                }).then(() => window.location.reload());
            } else {
                Swal.fire({
                    icon: 'error', 
                    title: 'Error', 
                    text: data.message || 'Error al guardar.',
                    background: '#1a1a1a', color: '#fff'
                });
            }
        } catch (error) {
            Swal.fire('Error', 'Falla en la conexión.', 'error');
        }
    });
</script>
@endsection