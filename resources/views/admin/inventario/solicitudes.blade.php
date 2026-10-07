@extends('layouts.app')

@section('title', 'Solicitudes de Inventario - StyleNow')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    :root {
        --bg-primary: #0a0a0a; --bg-secondary: #1a1a1a; --bg-card: #2a2a2a;
        --text-primary: #ffffff; --text-secondary: #cccccc; --text-muted: #888888;
        --accent-primary: #fad370; --accent-secondary: #d4af37;
        --border-color: rgba(250, 211, 112, 0.3); --hover-bg: rgba(250, 211, 112, 0.1);
    }

    .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px;}
    .admin-title { font-family: 'Abril Fatface', cursive; font-size: 2rem; color: var(--text-primary); margin: 0;}
    .admin-subtitle { color: var(--text-secondary); font-size: 0.95rem; }

    .content-card { background: var(--bg-card); border-radius: 15px; border: 1px solid var(--border-color); overflow: hidden; }
    
    .tabla-header { display: grid; grid-template-columns: 100px 1.5fr 1fr 1fr 150px; padding: 20px 25px; background: var(--bg-secondary); font-weight: 600; border-bottom: 2px solid var(--border-color); font-size: 13px; color: var(--text-secondary); text-transform: uppercase; }
    .tabla-fila { display: grid; grid-template-columns: 100px 1.5fr 1fr 1fr 150px; padding: 20px 25px; border-bottom: 1px solid var(--border-color); transition: 0.3s; align-items: center; }
    .tabla-fila:hover { background: var(--hover-bg); }
    .tabla-fila:last-child { border-bottom: none; }

    .empleado-info { display: flex; flex-direction: column; gap: 4px; }
    .empleado-name { font-weight: bold; color: var(--text-primary); font-size: 15px; }
    .sucursal-name { font-size: 12px; color: var(--text-secondary); }

    .prod-info { display: flex; flex-direction: column; gap: 4px; }
    .prod-name { font-weight: 600; color: var(--accent-primary); font-size: 15px;}
    .prod-comment { font-size: 12px; color: #a0a0a0; font-style: italic;}

    .badge { padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; width: fit-content; text-align: center;}
    .urgency-baja { background: rgba(74, 222, 128, 0.15); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.3); }
    .urgency-media { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
    .urgency-alta { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
    
    .status-atendido { background: rgba(74, 222, 128, 0.1); color: #4ade80; border: 1px solid #4ade80; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: bold; display: inline-block;}

    .btn-atender { background: #4ade80; color: #000; font-weight: bold; font-size: 13px; padding: 8px 16px; border: none; border-radius: 8px; cursor: pointer; transition: 0.3s; }
    .btn-atender:hover { transform: scale(1.05); box-shadow: 0 4px 10px rgba(74, 222, 128, 0.3); }
</style>
@endpush

@section('content')
<div class="admin-header">
    <div>
        <h1 class="admin-title">Solicitudes de Empleados</h1>
        <p class="admin-subtitle">Atiende los reportes de productos faltantes en las sucursales</p>
    </div>
</div>

<div class="content-card">
    <div class="tabla-header">
        <div>Fecha</div>
        <div>Empleado / Sucursal</div>
        <div>Producto Faltante</div>
        <div>Urgencia</div>
        <div style="text-align: right;">Acción</div>
    </div>
    
    <div>
        @forelse($solicitudes as $sol)
            @php
                $badgeUrgencia = 'urgency-' . strtolower($sol->rep_nivel_urgencia);
                $fechaFormato = \Carbon\Carbon::parse($sol->rep_fecha)->format('d/m/Y');
            @endphp
<div class="tabla-fila" @style(['opacity: 0.6' => $sol->rep_estado === 'Atendido'])>                <div style="color: var(--text-secondary); font-size: 13px;">{{ $fechaFormato }}</div>
                
                <div class="empleado-info">
                    <span class="empleado-name">{{ $sol->empleado_nombre }}</span>
                    <span class="sucursal-name">📍 {{ $sol->suc_nombre ?? 'General' }}</span>
                </div>
                
                <div class="prod-info">
                    <span class="prod-name">📦 {{ $sol->prd_nombre }}</span>
                    <span style="font-size: 11px; color: var(--text-muted);">Stock actual en sistema: {{ $sol->prd_stockActual }}</span>
                    @if($sol->rep_comentario)
                        <span class="prod-comment">"{{ $sol->rep_comentario }}"</span>
                    @endif
                </div>
                
                <div>
                    <div class="badge {{ $badgeUrgencia }}">{{ $sol->rep_nivel_urgencia }}</div>
                </div>
                
                <div style="text-align: right;">
                    @if($sol->rep_estado === 'Pendiente')
<button class="btn-atender" 
    data-id="{{ $sol->rep_id }}" 
    data-nombre="{{ addslashes($sol->prd_nombre) }}" 
    onclick="marcarAtendido(this.dataset.id, this.dataset.nombre)">✅ Marcar Comprado</button>                    @else
                        <span class="status-atendido">✓ Resuelto</span>
                    @endif
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 50px; color: var(--text-muted);">
                <div style="font-size: 50px; margin-bottom: 15px;">📦</div>
                <h3>Todo en orden</h3>
                <p>No hay solicitudes de inventario pendientes.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    async function marcarAtendido(id, nombreProducto) {
        const { value: cantidad, isConfirmed } = await Swal.fire({
            title: 'Reabastecer Producto',
            html: `¿Cuántas unidades de <b style="color:var(--accent-primary)">${nombreProducto}</b> compraste?<br><br><small style="color:#aaa;">Se sumarán automáticamente al stock del sistema. Si dejas el campo vacío, solo se cerrará el reporte sin sumar stock.</small>`,
            icon: 'info',
            input: 'number',
            inputAttributes: { min: 0, step: 1 },
            showCancelButton: true,
            confirmButtonColor: '#4ade80',
            cancelButtonColor: '#333',
            confirmButtonText: '<span style="color:#000; font-weight:bold;">Guardar y Sumar Stock</span>',
            cancelButtonText: 'Cancelar',
            background: '#1a1a1a', 
            color: '#fff'
        });

        if (isConfirmed) {
            try {
                Swal.fire({title: 'Actualizando inventario...', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
                
                const res = await fetch(`/admin/inventario/solicitudes/${id}/atender`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                    },
                    body: JSON.stringify({ cantidad: cantidad || 0 }) // Enviamos la cantidad al controlador
                });

                const data = await res.json();
                
                if (res.ok && data.success) {
                    Swal.fire({
                        icon: 'success', 
                        title: '¡Inventario Actualizado!', 
                        text: cantidad > 0 ? `Se sumaron ${cantidad} unidades al stock.` : 'Reporte marcado como atendido.',
                        background: '#1a1a1a', color: '#fff', confirmButtonColor: '#fad370'
                    }).then(() => window.location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'Falla de conexión', 'error');
            }
        }
    }
</script>
@endsection