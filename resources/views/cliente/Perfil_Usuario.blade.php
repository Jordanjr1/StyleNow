@extends('layouts.app')

@section('title', 'Mi Perfil - StyleNow')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    :root {
        --negro: #0a0a0a; --blanco: #ffffff; --dorado: #fad370;
        --bg-primary: #0a0a0a; --bg-secondary: #121212; --bg-card: #1a1a1a;
        --text-primary: #ffffff; --text-secondary: #a0a0a0; --border-color: #2a2a2a;
        --hover-bg: #252525; --shadow-sm: 0 2px 8px rgba(0,0,0,0.3);
        --shadow-md: 0 4px 16px rgba(0,0,0,0.4); --shadow-lg: 0 8px 24px rgba(250, 211, 112, 0.2);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

    /* CONTENEDOR PRINCIPAL */
    .perfil-container { max-width: 1200px; }

    /* Navigation Tabs */
    .nav-tabs { display: flex; gap: 4px; background: var(--bg-card); padding: 8px; border-radius: 12px; margin-bottom: 30px; border: 1px solid var(--border-color); overflow-x: auto;}
    .nav-tab { padding: 12px 24px; border: none; background: none; color: var(--text-secondary); font-size: 14px; font-weight: 500; cursor: pointer; border-radius: 8px; transition: var(--transition); display: flex; align-items: center; gap: 8px; white-space: nowrap;}
    .nav-tab:hover { color: var(--text-primary); background: var(--hover-bg); }
    .nav-tab.active { background: var(--dorado); color: var(--negro); font-weight: bold;}

    /* Layout Principal */
    .profile-layout { display: grid; grid-template-columns: 1fr; gap: 30px; }
    @media (min-width: 992px) { .profile-layout { grid-template-columns: 350px 1fr; } }

    /* Profile Sidebar */
    .profile-sidebar { position: sticky; top: 30px; height: fit-content; }
    .content-card { background: var(--bg-card); border-radius: 15px; padding: 30px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); text-align: center; margin-bottom: 20px; }

    /* AVATAR CORREGIDO */
    .avatar-wrapper { position: relative; display: inline-block; margin-bottom: 20px; }
    .avatar { width: 120px; height: 120px; border-radius: 50%; background: var(--dorado); display: flex; align-items: center; justify-content: center; font-size: 48px; color: var(--negro); font-family: 'Abril Fatface', cursive; box-shadow: var(--shadow-lg); margin: 0; }
    .avatar-badge { position: absolute; bottom: 2px; right: 2px; background: linear-gradient(135deg, var(--dorado), #f4c430); color: var(--negro); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; border: 4px solid var(--bg-card); }

    .user-name { font-family: 'Abril Fatface', cursive; font-size: 1.5rem; color: var(--text-primary); margin-bottom: 5px; }
    .user-email { color: var(--text-secondary); font-size: 14px; margin-bottom: 20px; }
    .level-badge { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.4rem 1rem; background: linear-gradient(135deg, var(--dorado), #f4c430); color: var(--negro); border-radius: 20px; font-size: 0.85rem; font-weight: 700; margin-bottom: 25px; }

    /* STATS GRID CORREGIDO (Nombre cambiado para no chocar con app.blade.php) */
    .perfil-stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 25px; }
    .stat-item { background: var(--bg-secondary); padding: 15px 10px; border-radius: 12px; text-align: center; border: 1px solid var(--border-color); transition: var(--transition); }
    .stat-item:hover { border-color: var(--dorado); transform: translateY(-2px); }
    .stat-value { font-size: 20px; font-weight: bold; color: var(--dorado); margin-bottom: 4px; }
    .stat-label { font-size: 11px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold; }

    /* Buttons */
    .action-buttons { display: flex; flex-direction: column; gap: 12px; }
    .btn { padding: 12px 24px; border: none; border-radius: 12px; font-size: 14px; font-weight: bold; cursor: pointer; transition: var(--transition); display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none;}
    .btn-primary { background: var(--dorado); color: var(--negro); }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
    .btn-secondary { background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border-color); }
    .btn-secondary:hover { border-color: var(--dorado); background: var(--hover-bg);}

    /* Content Sections */
    .profile-content { display: none; animation: fadeIn 0.4s ease;}
    .profile-content.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px);} to { opacity: 1; transform: translateY(0);} }

    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 15px; border-bottom: 1px dashed var(--border-color); }
    .card-icon { width: 40px; height: 40px; background: rgba(250, 211, 112, 0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--dorado); font-size: 18px; }

    /* Info Grid */
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .list-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid var(--border-color); }
    .list-item:last-child { border-bottom: none; }
    .info-label { color: var(--text-secondary); font-size: 14px; }
    .info-value { font-weight: 600; color: var(--text-primary); }

    /* Citas & Favoritos Grid */
    .citas-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
    .cita-card { background: var(--bg-card); border-radius: 12px; padding: 20px; border: 1px solid var(--border-color); transition: var(--transition); }
    .cita-card:hover { transform: translateY(-5px); border-color: var(--dorado); box-shadow: var(--shadow-sm);}
    
    .cita-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; }
    .cita-fecha { font-size: 13px; color: var(--text-secondary); display: flex; align-items: center; gap: 6px; }
    .badge-estado { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase;}
    .estado-completada { background: rgba(74, 222, 128, 0.1); color: #4ade80; border: 1px solid rgba(74, 222, 128, 0.2); }
    .estado-cancelada { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
    .estado-pendiente, .estado-confirmada { background: rgba(250, 211, 112, 0.1); color: var(--dorado); border: 1px solid rgba(250, 211, 112, 0.2); }

    .cita-servicio { font-size: 15px; font-weight: 600; margin-bottom: 5px; color: white; line-height: 1.3;}
    .cita-details { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border-color); }
    .cita-precio { font-size: 18px; font-weight: bold; color: var(--dorado); }
    
    .star-box { display: flex; gap: 2px; margin-top: 8px; font-size: 14px;}
    .star-filled { color: var(--dorado); }
    .star-empty { color: #333; }

    /* Swithes */
    .switch { position: relative; display: inline-block; width: 50px; height: 26px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--border-color); transition: .4s; border-radius: 34px; }
    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: var(--dorado); }
    input:checked + .slider:before { transform: translateX(24px); background-color: #000;}
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
        <a href="/cliente/perfil-vista" class="nav-item active">
            <span class="nav-icon">👤</span><span class="nav-text">Mi Perfil</span>
        </a>
    </div>
</nav>
@endsection

@section('topbar-left')
    <h1 style="font-family: 'Abril Fatface', cursive; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.3rem;">Mi Perfil</h1>
    <p style="color: var(--text-secondary); font-size: 0.95rem;">Administra tu información y preferencias en StyleNow.</p>
@endsection

@section('content')
<div class="perfil-container">

    <div class="nav-tabs">
        <button class="nav-tab active" onclick="switchTab('informacion', this)">👤 Información</button>
        <button class="nav-tab" onclick="switchTab('citas', this)">📅 Mis Citas</button>
        <button class="nav-tab" onclick="switchTab('favoritos', this)">❤️ Favoritos</button>
        <button class="nav-tab" onclick="switchTab('configuracion', this)">⚙️ Configuración</button>
    </div>

    <div class="profile-layout">
        <div class="profile-sidebar">
            <div class="content-card">
                
                <div class="avatar-wrapper">
                    <div class="avatar">
                        {{ strtoupper(substr($user->usr_nombre, 0, 1)) }}{{ strtoupper(substr($user->usr_apellido ?? '', 0, 1)) }}
                    </div>
                    <div class="avatar-badge">👑</div>
                </div>
                
                <h2 class="user-name" id="display-nombre">{{ $user->usr_nombre }} {{ $user->usr_apellido }}</h2>
                <p class="user-email" id="display-email">{{ $user->usr_email }}</p>
                
                <span class="level-badge">⭐ Nivel {{ $nivelActual }}</span>
                
                <div class="perfil-stats-grid">
                    <div class="stat-item">
                        <div class="stat-value">{{ $puntosDisponibles }}</div>
                        <div class="stat-label">Puntos</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $historialCitas->count() }}</div>
                        <div class="stat-label">Citas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ count($favoritos) }}</div>
                        <div class="stat-label">Favoritos</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $promedioValoracion }}</div>
                        <div class="stat-label">Estrellas</div>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="editarPerfil()">✏️ Editar Perfil</button>
                    <a href="/cliente/citas/nueva" class="btn btn-secondary">💇 Agendar Nueva Cita</a>
                </div>
            </div>
            
            <div class="content-card" style="text-align: left;">
                <div class="section-header" style="margin-bottom: 10px; border-bottom: none; padding-bottom: 5px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="card-icon" style="font-size: 1rem; width: 30px; height: 30px;">📅</div>
                        <h3 style="font-size: 1.1rem; font-weight: 600; color: white;">Próximas Citas</h3>
                    </div>
                </div>
                <hr style="border: 0; border-top: 1px dashed var(--border-color); margin-bottom: 15px;">
                
                @if($proximasCitas->isEmpty())
                    <p style="color: var(--text-secondary); text-align: center; padding: 20px 0; font-size: 14px;">No tienes citas próximas.</p>
                @else
                    @foreach($proximasCitas->take(3) as $citaProx)
                        <div class="list-item" style="padding: 12px 0;">
                            <div>
                                <div style="font-weight: bold; font-size: 14px; margin-bottom: 3px; color: white;">{{ Str::limit($citaProx->cit_nombres_servicios, 25) }}</div>
                                <div style="font-size: 12px; color: var(--dorado);">
                                    {{ \Carbon\Carbon::parse($citaProx->cit_fechaCita)->format('d/m/Y - H:i') }}
                                </div>
                            </div>
                            <a href="/cliente/mis-citas" style="background: var(--bg-secondary); padding: 5px 10px; border-radius: 6px; border: 1px solid var(--border-color); text-decoration: none; font-size: 12px; color: white; transition: 0.3s;" onmouseover="this.style.borderColor='var(--dorado)'" onmouseout="this.style.borderColor='var(--border-color)'">Ver</a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div>
            
            <div class="profile-content active" id="informacion-content">
                <div class="info-grid">
                    <div class="content-card" style="text-align: left; padding: 25px;">
                        <div class="section-header">
                            <h3 style="font-size: 1.2rem; font-weight: bold;">👤 Datos de Contacto</h3>
                        </div>
                        <div class="list-item">
                            <span class="info-label">Nombre</span>
                            <span class="info-value" id="info-nombre">{{ $user->usr_nombre }}</span>
                        </div>
                        <div class="list-item">
                            <span class="info-label">Email</span>
                            <span class="info-value" style="color: var(--dorado);" id="info-email">{{ $user->usr_email }}</span>
                        </div>
                        <div class="list-item">
                            <span class="info-label">Teléfono</span>
                            <span class="info-value" id="info-telefono">{{ $user->usr_telefono ?? 'No registrado' }}</span>
                        </div>
                        <div class="list-item">
                            <span class="info-label">Miembro desde</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($user->usr_fechaRegistro)->translatedFormat('d \d\e F, Y') }}</span>
                        </div>
                    </div>
                    
                    <div class="content-card" style="text-align: left; padding: 25px;">
                        <div class="section-header">
                            <h3 style="font-size: 1.2rem; font-weight: bold;">📊 Tu Actividad</h3>
                        </div>
                        <div class="list-item">
                            <span class="info-label">Dinero Invertido</span>
                            <span class="info-value" style="color: #4ade80;">${{ number_format($totalGastado, 2) }}</span>
                        </div>
                        <div class="list-item">
                            <span class="info-label">Servicios Probados</span>
                            <span class="info-value">{{ $serviciosDiferentes }} distintos</span>
                        </div>
                        <div class="list-item">
                            <span class="info-label">Estilistas Visitados</span>
                            <span class="info-value">{{ $estilistasVisitados }} profesionales</span>
                        </div>
                        <div class="list-item">
                            <span class="info-label">Puntos Históricos</span>
                            <span class="info-value" style="color: var(--dorado);">{{ $puntosGanados }} pts</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-content" id="citas-content">
                <div class="section-header" style="background: var(--bg-card); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                    <h3 style="font-size: 1.2rem; font-weight: bold; margin:0;">📋 Citas Completadas</h3>
                </div>
                
                @if($historialCitas->isEmpty())
                    <div style="text-align: center; padding: 50px; background: var(--bg-card); border-radius: 12px;">
                        <div style="font-size: 3rem; opacity: 0.5; margin-bottom: 10px;">📂</div>
                        <p style="color: var(--text-muted);">Aún no tienes un historial de citas finalizadas.</p>
                    </div>
                @else
                    <div class="citas-grid">
                        @foreach ($historialCitas as $cita)
                            <div class="cita-card">
                                <div class="cita-header">
                                    <div class="cita-fecha">📅 {{ \Carbon\Carbon::parse($cita->cit_fechaCita)->format('d/m/Y - H:i') }}</div>
                                    <span class="badge-estado estado-completada">Completada</span>
                                </div>
                                <h4 class="cita-servicio">{{ Str::limit($cita->cit_nombres_servicios, 40) }}</h4>
                                <p style="color: var(--text-secondary); font-size: 13px;">Estilista: <span style="color: var(--dorado);">{{ $cita->estilista_nombre }}</span></p>
                                
                                @if ($cita->cit_calificacion)
                                    <div class="star-box">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span class="{{ $i <= $cita->cit_calificacion ? 'star-filled' : 'star-empty' }}">★</span>
                                        @endfor
                                    </div>
                                @endif
                                
                                <div class="cita-details">
                                    <div class="cita-precio">${{ number_format($cita->cit_precio, 2) }}</div>
                                    <div style="color: var(--text-muted); font-size: 12px;">+{{ floor($cita->cit_precio * 10) }} pts</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="profile-content" id="favoritos-content">
                <div class="section-header" style="background: var(--bg-card); padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                    <div>
                        <h3 style="font-size: 1.2rem; font-weight: bold; margin:0;">❤️ Tus Servicios Frecuentes</h3>
                        <p style="color: var(--text-muted); font-size: 13px; margin-top: 5px;">Basado en las citas que más has repetido.</p>
                    </div>
                </div>

                @if(count($favoritos) == 0)
                    <div style="text-align: center; padding: 50px; background: var(--bg-card); border-radius: 12px;">
                        <p style="color: var(--text-muted);">Aún no tenemos datos para mostrar tus favoritos.</p>
                    </div>
                @else
                    <div class="citas-grid">
                        @foreach ($favoritos as $fav)
                            <div class="cita-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                                        <span style="background: rgba(250, 211, 112, 0.2); color: var(--dorado); padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: bold;">RECOMENDADO</span>
                                        <span style="font-size: 1.2rem;">🌟</span>
                                    </div>
                                    <h4 style="font-size: 16px; color: white; margin-bottom: 10px;">{{ $fav->cit_nombres_servicios }}</h4>
                                </div>
                                <div class="cita-details" style="margin-top: auto;">
                                    <div class="cita-precio">${{ number_format($fav->cit_precio, 2) }}</div>
                                    <a href="/cliente/citas/nueva" class="btn btn-primary" style="padding: 6px 15px; font-size: 12px;">Agendar</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="profile-content" id="configuracion-content">
                <div class="content-card" style="text-align: left; padding: 30px;">
                    <h3 style="margin-bottom: 25px; font-size: 1.3rem; font-weight: bold;">🔔 Preferencias de Notificación</h3>
                    
                    <div style="display: grid; gap: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 15px;">
                            <div>
                                <h4 style="font-size: 15px; color: white; margin-bottom: 3px;">Correos Electrónicos</h4>
                                <p style="font-size: 13px; color: var(--text-muted);">Recibir confirmaciones de citas y recibos.</p>
                            </div>
                            <label class="switch"><input type="checkbox" checked onchange="alertaDesarrollo()"><span class="slider"></span></label>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 15px;">
                            <div>
                                <h4 style="font-size: 15px; color: white; margin-bottom: 3px;">Recordatorios de WhatsApp</h4>
                                <p style="font-size: 13px; color: var(--text-muted);">Mensajes automáticos 24h antes de tu cita.</p>
                            </div>
                            <label class="switch"><input type="checkbox" checked onchange="alertaDesarrollo()"><span class="slider"></span></label>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px;">
                            <div>
                                <h4 style="font-size: 15px; color: white; margin-bottom: 3px;">Ofertas y Promociones</h4>
                                <p style="font-size: 13px; color: var(--text-muted);">Avisos sobre descuentos especiales.</p>
                            </div>
                            <label class="switch"><input type="checkbox" onchange="alertaDesarrollo()"><span class="slider"></span></label>
                        </div>
                    </div>
                </div>

                <div class="content-card" style="text-align: left; padding: 30px;">
                    <h3 style="margin-bottom: 20px; font-size: 1.3rem; font-weight: bold;">🔒 Seguridad</h3>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <button class="btn btn-secondary" onclick="modalPassword()" style="flex: 1;">🔑 Cambiar Contraseña</button>
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
    // === LÓGICA DE TABS ===
    function switchTab(tabId, btnElement) {
        document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.profile-content').forEach(c => c.classList.remove('active'));
        
        btnElement.classList.add('active');
        document.getElementById(`${tabId}-content`).classList.add('active');
    }

    // === EDITAR PERFIL ===
    function editarPerfil() {
        const nombreActual = document.getElementById('info-nombre').innerText;
        const emailActual = document.getElementById('info-email').innerText;
        const telActual = document.getElementById('info-telefono').innerText;

        Swal.fire({
            title: '✏️ Editar Perfil',
            html: `
                <div style="text-align: left; margin-top: 15px;">
                    <label style="color: #888; font-size: 12px; font-weight: bold;">NOMBRES Y APELLIDOS</label>
                    <input id="swal-nombre" class="swal2-input" value="${nombreActual}" style="background: #111; color: white; border: 1px solid #333; margin-top: 5px; width: 90%;">
                    
                    <label style="color: #888; font-size: 12px; font-weight: bold; margin-top: 15px; display:block;">CORREO ELECTRÓNICO</label>
                    <input id="swal-email" type="email" class="swal2-input" value="${emailActual}" style="background: #111; color: white; border: 1px solid #333; margin-top: 5px; width: 90%;">
                    
                    <label style="color: #888; font-size: 12px; font-weight: bold; margin-top: 15px; display:block;">TELÉFONO</label>
                    <input id="swal-tel" class="swal2-input" value="${telActual !== 'No registrado' ? telActual : ''}" placeholder="Ej: 0991234567" style="background: #111; color: white; border: 1px solid #333; margin-top: 5px; width: 90%;">
                </div>
            `,
            background: '#151515', color: '#fff',
            showCancelButton: true, confirmButtonText: 'Guardar Cambios', cancelButtonText: 'Cancelar',
            confirmButtonColor: '#fad370', cancelButtonColor: '#333',
            customClass: { confirmButton: 'swal-btn-black-text' },
            preConfirm: () => {
                return {
                    nombre: document.getElementById('swal-nombre').value,
                    email: document.getElementById('swal-email').value,
                    telefono: document.getElementById('swal-tel').value
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                guardarPerfil(result.value);
            }
        });
    }

    async function guardarPerfil(datos) {
        try {
            Swal.fire({title: 'Guardando...', allowOutsideClick: false, didOpen: () => Swal.showLoading(), background: '#151515', color: '#fff'});
            
            const res = await fetch('/cliente/perfil/actualizar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(datos)
            });

            const data = await res.json();
            if(data.success) {
                Swal.fire({icon: 'success', title: '¡Actualizado!', text: data.message, background: '#151515', color: '#fff', confirmButtonColor: '#fad370'})
                .then(() => location.reload());
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } catch(e) {
            Swal.fire('Error', 'Falla en el servidor', 'error');
        }
    }

    // === CAMBIAR CONTRASEÑA ===
    function modalPassword() {
        Swal.fire({
            title: '🔑 Cambiar Contraseña',
            html: `
                <input type="password" id="swal-pass" class="swal2-input" placeholder="Nueva Contraseña" style="background: #111; color: white; border: 1px solid #333;">
            `,
            background: '#151515', color: '#fff',
            showCancelButton: true, confirmButtonText: 'Actualizar', cancelButtonText: 'Cancelar',
            confirmButtonColor: '#fad370',
            preConfirm: () => { return document.getElementById('swal-pass').value; }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                guardarPassword(result.value);
            }
        });
    }

    async function guardarPassword(pass) {
        try {
            const res = await fetch('/cliente/perfil/password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ password: pass })
            });

            const data = await res.json();
            if(data.success) {
                Swal.fire({icon: 'success', title: 'Protegido', text: data.message, background: '#151515', color: '#fff', confirmButtonColor: '#fad370', toast: true, position: 'top-end', timer: 3000, showConfirmButton: false});
            } else {
                Swal.fire({icon: 'error', text: data.message, background: '#151515', color: '#fff'});
            }
        } catch(e) {
            Swal.fire('Error', 'Falla de conexión', 'error');
        }
    }

    // === UTILIDADES ===
    function alertaDesarrollo() {
        Swal.fire({
            icon: 'info',
            title: 'Preferencias Guardadas',
            text: 'Tus preferencias de notificación se han actualizado localmente.',
            background: '#151515', color: '#fff',
            toast: true, position: 'bottom-end', timer: 2000, showConfirmButton: false
        });
    }

    // Pequeño parche CSS para el texto del botón SweetAlert
    document.head.insertAdjacentHTML("beforeend", `<style>.swal-btn-black-text { color: black !important; font-weight: bold; }</style>`);
</script>
@endsection