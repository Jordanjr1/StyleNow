@extends('layouts.app')

@section('title', 'Mi Perfil - StyleNow')

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

    /* VARIABLES DE DISEÑO */
    .empleado-container {
        --dorado: #fad370; --bg-card: #1a1a1a; --bg-secondary: #121212;
        --text-primary: #ffffff; --text-secondary: #a0a0a0; --border-color: #2a2a2a;
        --hover-bg: #252525;
        font-family: 'Poppins', sans-serif; max-width: 1200px; margin: 0 auto;
    }

    /* Pestañas de Navegación */
    .emp-nav-tabs { display: flex; gap: 4px; background: var(--bg-card); padding: 8px; border-radius: 12px; margin-bottom: 30px; border: 1px solid var(--border-color); width: fit-content; }
    .emp-nav-tab { padding: 12px 24px; border: none; background: none; color: var(--text-secondary); font-size: 14px; font-weight: 500; cursor: pointer; border-radius: 8px; transition: 0.3s; display: flex; align-items: center; gap: 8px; text-decoration: none; }
    .emp-nav-tab:hover { color: var(--text-primary); background: var(--hover-bg); }
    .emp-nav-tab.active { background: var(--dorado); color: #0a0a0a; font-weight: bold;}

    /* Layout del Perfil */
    .profile-layout { display: grid; grid-template-columns: 350px 1fr; gap: 30px; align-items: start; }

    /* Sidebar Izquierdo (Info Usuario) */
    .profile-sidebar-card { background: var(--bg-card); border-radius: 16px; border: 1px solid var(--border-color); padding: 30px; text-align: center; position: relative; overflow: hidden; }
    .profile-sidebar-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: var(--dorado); }
    
    .avatar-wrapper { position: relative; margin-bottom: 20px; display: flex; justify-content: center; }
    .avatar-circle { width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #1e1e1e, #121212); border: 2px dashed var(--dorado); display: flex; align-items: center; justify-content: center; font-size: 40px; color: var(--dorado); font-weight: bold; }
    .avatar-badge { position: absolute; bottom: 0; right: calc(50% - 60px); background: var(--dorado); color: #000; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; border: 3px solid var(--bg-card); }

    .user-name { font-size: 22px; font-weight: bold; color: var(--text-primary); margin-bottom: 5px; }
    .user-email { color: var(--text-secondary); font-size: 13px; margin-bottom: 25px; }
    
    .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; }
    .stat-box { background: var(--bg-secondary); padding: 15px; border-radius: 12px; border: 1px solid var(--border-color); text-align: center; }
    .stat-value { font-size: 20px; font-weight: bold; color: var(--dorado); }
    .stat-label { font-size: 11px; color: var(--text-secondary); text-transform: uppercase; margin-top: 4px; }

    .btn-edit { width: 100%; padding: 12px; background: rgba(250, 211, 112, 0.1); color: var(--dorado); border: 1px solid var(--dorado); border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.3s; font-family: 'Poppins';}
    .btn-edit:hover { background: var(--dorado); color: #000; }

    /* Contenido Derecho (Tabs) */
    .profile-content-section { display: none; animation: fadeIn 0.4s ease; }
    .profile-content-section.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .content-card { background: var(--bg-card); border-radius: 16px; border: 1px solid var(--border-color); padding: 30px; margin-bottom: 20px; }
    .card-title { font-size: 18px; font-weight: bold; color: white; display: flex; align-items: center; gap: 10px; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px dashed var(--border-color); }
    
    .info-row { display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid var(--border-color); }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: var(--text-secondary); font-size: 14px; }
    .info-value { color: white; font-weight: 500; font-size: 15px; }

    /* Formulario */
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; margin-bottom: 8px; font-size: 13px; color: var(--text-secondary); font-weight: 600; text-transform: uppercase;}
    .form-input { width: 100%; padding: 14px 15px; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 10px; color: white; font-family: 'Poppins'; outline: none; transition: 0.3s;}
    .form-input:focus { border-color: var(--dorado); }
    
    .btn-save { padding: 12px 25px; background: var(--dorado); color: #000; font-weight: bold; border: none; border-radius: 10px; cursor: pointer; transition: 0.3s; font-family: 'Poppins'; margin-top: 10px;}
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(250, 211, 112, 0.3); }

    /* Próximas Citas Widget */
    .agenda-item { display: flex; justify-content: space-between; align-items: center; padding: 15px; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 12px; margin-bottom: 10px; border-left: 3px solid var(--dorado);}
    .agenda-fecha { font-size: 12px; color: var(--dorado); font-weight: bold; margin-bottom: 3px; }
    .agenda-servicio { font-size: 15px; color: white; font-weight: bold; }
    .agenda-cliente { font-size: 13px; color: var(--text-secondary); }

    @media (max-width: 900px) {
        .profile-layout { grid-template-columns: 1fr; }
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
        <a href="{{ route('empleado.reportar.falta') }}" class="nav-item"><span class="nav-icon">📦</span><span class="nav-text">Reportar Falta</span></a>
        <a href="{{ route('empleado.ventas') }}" class="nav-item"><span class="nav-icon">💵</span><span class="nav-text">Ventas</span></a>
    </div>

    <div class="nav-section">
        <p class="nav-section-title">Cuenta</p>
        <a href="{{ route('empleado.perfil') }}" class="nav-item active"><span class="nav-icon">👤</span><span class="nav-text">Mi Perfil</span></a>
    </div>
</nav>
@endsection

@section('topbar-left')
    <h1 style="font-family: 'Abril Fatface', cursive; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.3rem;">Mi Cuenta</h1>
    <p style="color: var(--text-secondary); font-size: 0.95rem;">Gestiona tu información personal y credenciales</p>
@endsection

@section('content')
<div class="empleado-container">

    <div class="emp-nav-tabs">
        <button class="emp-nav-tab active" onclick="switchTab('info')">👤 Información Pública</button>
        <button class="emp-nav-tab" onclick="switchTab('config')">⚙️ Configuración y Seguridad</button>
    </div>

    <div class="profile-layout">
        
        <div>
            <div class="profile-sidebar-card">
                <div class="avatar-wrapper">
                    <div class="avatar-circle">
                        {{ strtoupper(substr($empleado->usr_nombre, 0, 1) . substr($empleado->usr_apellido, 0, 1)) }}
                    </div>
                    <div class="avatar-badge">✂️</div>
                </div>
                
                <h2 class="user-name">{{ $empleado->usr_nombre }} {{ $empleado->usr_apellido }}</h2>
                <p class="user-email">{{ $empleado->usr_email }}</p>
                
                <div class="stats-grid">
                    <div class="stat-box">
                        <div class="stat-value">⭐ {{ number_format($promedioValoracion, 1) }}</div>
                        <div class="stat-label">Valoración</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value">✅ {{ count($citasCompletadas) }}</div>
                        <div class="stat-label">Atenciones</div>
                    </div>
                </div>

                <div class="stat-box" style="margin-bottom: 20px; border-color: var(--dorado);">
                    <div class="stat-value" style="font-size: 24px;">${{ number_format($totalGanado, 2) }}</div>
                    <div class="stat-label" style="color: var(--dorado);">Total Generado (Histórico)</div>
                </div>
                
                <button class="btn-edit" onclick="switchTab('config')">✏️ Editar Mis Datos</button>
            </div>

            <div class="content-card" style="margin-top: 20px; padding: 20px;">
                <h3 style="font-size: 15px; margin-bottom: 15px; color: var(--dorado);">📅 Mi Agenda Próxima</h3>
                
                @forelse($proximasCitas as $cita)
                    <div class="agenda-item">
                        <div>
                            <div class="agenda-fecha">{{ \Carbon\Carbon::parse($cita->cit_fechaCita)->translatedFormat('d M - H:i') }}</div>
                            <div class="agenda-servicio">{{ Str::limit($cita->srv_nombre ?? 'Servicio General', 25) }}</div>
                            <div class="agenda-cliente">👤 {{ $cita->cliente_nombre ?? 'Público General' }}</div>
                        </div>
                    </div>
                @empty
                    <p style="text-align: center; color: var(--text-muted); font-size: 13px;">No hay citas agendadas.</p>
                @endforelse
            </div>
        </div>

        <div>
            <div id="tab-info" class="profile-content-section active">
                <div class="content-card">
                    <div class="card-title"><span>📄</span> Datos de Contacto Registrados</div>
                    
                    <div class="info-row">
                        <span class="info-label">Nombre Completo</span>
                        <span class="info-value">{{ $empleado->usr_nombre }} {{ $empleado->usr_apellido }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Teléfono Móvil</span>
                        <span class="info-value">{{ $empleado->usr_telefono ?? 'No registrado' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Correo Electrónico</span>
                        <span class="info-value" style="color: var(--dorado);">{{ $empleado->usr_email }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Rol en el sistema</span>
                        <span class="info-value">Staff / Empleado</span>
                    </div>
                </div>
            </div>

            <div id="tab-config" class="profile-content-section">
                <div class="content-card">
                    <div class="card-title"><span>✏️</span> Actualizar Información</div>
                    
                    <form id="formPerfil">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label class="form-label">Nombres</label>
                                <input type="text" id="editNombre" class="form-input" value="{{ $empleado->usr_nombre }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Apellidos</label>
                                <input type="text" id="editApellido" class="form-input" value="{{ $empleado->usr_apellido }}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Teléfono / WhatsApp</label>
                            <input type="text" id="editTelefono" class="form-input" value="{{ $empleado->usr_telefono }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" id="editEmail" class="form-input" value="{{ $empleado->usr_email }}" required>
                        </div>

                        <button type="submit" class="btn-save">💾 Guardar Cambios</button>
                    </form>
                </div>

                <div class="content-card" style="border-color: rgba(239, 68, 68, 0.3);">
                    <div class="card-title" style="color: #ef4444; border-bottom-color: rgba(239, 68, 68, 0.3);"><span>🔐</span> Seguridad de la Cuenta</div>
                    
                    <form id="formPassword">
                        <div class="form-group">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" id="newPassword" class="form-input" placeholder="Mínimo 6 caracteres" required minlength="6">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" id="confirmPassword" class="form-input" placeholder="Repite la contraseña" required minlength="6">
                        </div>

                        <button type="submit" class="btn-save" style="background: #ef4444; color: white;">🔑 Actualizar Contraseña</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Sistema de Pestañas
    function switchTab(tabId) {
        document.querySelectorAll('.emp-nav-tab').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.profile-content-section').forEach(sec => sec.classList.remove('active'));
        
        event.currentTarget.classList.add('active');
        document.getElementById('tab-' + tabId).classList.add('active');
    }

    // Actualizar Datos Personales
    document.getElementById('formPerfil').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            Swal.fire({title: 'Guardando...', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
            
            const res = await fetch(`/empleado/perfil/actualizar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    nombre: document.getElementById('editNombre').value,
                    apellido: document.getElementById('editApellido').value,
                    telefono: document.getElementById('editTelefono').value,
                    email: document.getElementById('editEmail').value
                })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                Swal.fire({icon: 'success', title: '¡Actualizado!', background: '#1a1a1a', color: '#fff', confirmButtonColor: '#fad370'})
                .then(() => window.location.reload());
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'Falla en la conexión.', 'error');
        }
    });

    // Actualizar Contraseña
    document.getElementById('formPassword').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const pass1 = document.getElementById('newPassword').value;
        const pass2 = document.getElementById('confirmPassword').value;

        if(pass1 !== pass2) {
            Swal.fire({icon: 'warning', title: 'Las contraseñas no coinciden', background: '#1a1a1a', color: '#fff'});
            return;
        }

        try {
            Swal.fire({title: 'Actualizando seguridad...', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
            
            const res = await fetch(`/empleado/perfil/password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ password: pass1 })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                document.getElementById('formPassword').reset();
                Swal.fire({icon: 'success', title: '¡Contraseña cambiada!', text: 'Usa tu nueva clave la próxima vez que inicies sesión.', background: '#1a1a1a', color: '#fff', confirmButtonColor: '#fad370'});
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'Falla en la conexión.', 'error');
        }
    });
</script>
@endsection