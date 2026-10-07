@extends('layouts.app')

@section('title', 'Perfil del Administrador - StyleNow')

@push('styles')
<style>
    /* VARIABLES GLOBALES */
    :root {
        --bg-primary: #0a0a0a; --bg-secondary: #1a1a1a; --bg-card: #2a2a2a;
        --text-primary: #ffffff; --text-secondary: #cccccc; --text-muted: #888;
        --accent-primary: #FAD370; --accent-hover: #eec95c;
        --border-color: rgba(250, 211, 112, 0.2);
        --danger: #f44336; --success: #4caf50;
    }

    .profile-container {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 30px;
        align-items: start;
    }

    /* --- SIDEBAR IZQUIERDO --- */
    .profile-sidebar {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 30px 20px;
        text-align: center;
        position: sticky;
        top: 20px;
    }

    .avatar-circle {
        width: 100px; height: 100px;
        background: var(--accent-primary);
        color: #000;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        font-weight: bold;
        margin: 0 auto 15px;
        font-family: serif; /* O la fuente que uses para títulos */
        border: 2px solid #fff;
    }
    
    .status-badge {
        background: rgba(76, 175, 80, 0.2);
        color: #4caf50;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: bold;
        display: inline-block;
        margin-top: 5px;
    }

    .profile-name { font-size: 20px; font-weight: bold; color: var(--text-primary); margin-bottom: 5px; }
    .profile-role { color: var(--accent-primary); font-size: 14px; margin-bottom: 10px; }

    .stats-row {
        display: flex;
        justify-content: space-around;
        margin: 25px 0;
        border-top: 1px solid rgba(255,255,255,0.1);
        border-bottom: 1px solid rgba(255,255,255,0.1);
        padding: 15px 0;
    }
    .stat-item h4 { font-size: 18px; color: var(--text-primary); margin-bottom: 2px; }
    .stat-item span { font-size: 11px; color: var(--text-muted); text-transform: uppercase; }

    /* MENU DE PESTAÑAS */
    .profile-menu { display: flex; flex-direction: column; gap: 10px; text-align: left; }
    .menu-btn {
        background: transparent;
        border: 1px solid rgba(255,255,255,0.1);
        color: var(--text-secondary);
        padding: 12px 15px;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: 0.3s;
        font-size: 14px;
    }
    .menu-btn:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }
    .menu-btn.active {
        border-color: var(--accent-primary);
        color: var(--accent-primary);
        background: rgba(250, 211, 112, 0.05);
    }

    /* --- CONTENIDO DERECHO --- */
    .profile-content {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 40px;
        min-height: 500px;
    }

    .section-title {
        font-size: 22px;
        font-weight: bold;
        color: var(--text-primary);
        margin-bottom: 10px;
    }
    .section-subtitle { color: var(--text-muted); font-size: 14px; margin-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .full-width { grid-column: span 2; }

    .input-group label { display: block; color: var(--text-muted); font-size: 13px; margin-bottom: 8px; font-weight: 600; }
    .form-input {
        width: 100%;
        background: var(--bg-secondary);
        border: 1px solid rgba(255,255,255,0.1);
        padding: 12px 15px;
        border-radius: 8px;
        color: var(--text-primary);
        font-size: 14px;
        transition: 0.3s;
    }
    .form-input:focus { outline: none; border-color: var(--accent-primary); }

    .btn-save {
        background: var(--accent-primary);
        color: #000;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }
    .btn-save:hover { background: var(--accent-hover); transform: translateY(-2px); }

    /* PESTAÑAS LOGICA */
    .tab-content { display: none; animation: fadeIn 0.4s ease; }
    .tab-content.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* LISTA DE ACTIVIDAD */
    .activity-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .act-icon {
        width: 40px; height: 40px;
        background: rgba(63, 81, 181, 0.2);
        color: #7986cb;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
    }
    .act-info h5 { margin: 0; color: var(--text-primary); font-size: 15px; }
    .act-info p { margin: 3px 0 0; color: var(--text-muted); font-size: 12px; }
    .act-time { margin-left: auto; font-size: 12px; color: var(--text-muted); }

    /* ALERTAS */
    .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
    .alert-success { background: rgba(76, 175, 80, 0.15); color: #81c784; border: 1px solid #4caf50; }
    .alert-error { background: rgba(244, 67, 54, 0.15); color: #e57373; border: 1px solid #f44336; }

    @media (max-width: 900px) {
        .profile-container { grid-template-columns: 1fr; }
        .profile-sidebar { position: static; margin-bottom: 20px; }
        .form-grid { grid-template-columns: 1fr; }
        .full-width { grid-column: span 1; }
    }
</style>
@endpush

@section('topbar-left')
    <h1>👑 Perfil del Administrador</h1>
    <p>Gestiona tu información personal, seguridad y preferencias</p>
@endsection

@section('content')

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if(session('error')) <div class="alert alert-error">{{ session('error') }}</div> @endif
@if($errors->any()) 
    <div class="alert alert-error">
        <ul style="margin:0; padding-left:20px;">
            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif

<div class="profile-container">
    
    <div class="profile-sidebar">
        <div class="avatar-circle">
            {{ strtoupper(substr($admin->usr_nombre, 0, 1) . substr($admin->usr_apellido, 0, 1)) }}
        </div>
        <div class="profile-name">{{ $admin->usr_nombre }} {{ $admin->usr_apellido }}</div>
        <div class="profile-role">Administrador</div>
        <div class="status-badge">● {{ $admin->usr_estado === 'A' ? 'Activo' : 'Inactivo' }}</div>

        <div class="stats-row">
            <div class="stat-item"><h4>{{ $diasActivo ?? 0 }}</h4><span>Días Activo</span></div>
            <div class="stat-item"><h4>{{ $antiguedad ?? 'Hoy' }}</h4><span>Registro</span></div>
        </div>

        <div class="profile-menu">
            <button class="menu-btn active" onclick="switchTab('personal')">👤 Perfil Personal</button>
            <button class="menu-btn" onclick="switchTab('security')">🔒 Seguridad</button>
            <button class="menu-btn" onclick="switchTab('activity')">📊 Actividad</button>
        </div>
    </div>

    <div class="profile-content">
        
        <div id="tab-personal" class="tab-content active">
            <h2 class="section-title">👤 Perfil Personal</h2>
            <p class="section-subtitle">Actualiza tu información personal y de contacto</p>
            
            <form action="{{ route('admin.perfil.update') }}" method="POST">
                @csrf @method('PUT')
                <div class="form-grid">
                    <div class="input-group">
                        <label>Nombre *</label>
                        <input type="text" name="usr_nombre" class="form-input" value="{{ old('usr_nombre', $admin->usr_nombre) }}" required>
                    </div>
                    <div class="input-group">
                        <label>Apellido *</label>
                        <input type="text" name="usr_apellido" class="form-input" value="{{ old('usr_apellido', $admin->usr_apellido) }}" required>
                    </div>
                    <div class="input-group">
                        <label>Email *</label>
                        <input type="email" name="usr_email" class="form-input" value="{{ old('usr_email', $admin->usr_email) }}" required>
                    </div>
                    <div class="input-group">
                        <label>Teléfono</label>
                        <input type="text" name="usr_telefono" class="form-input" value="{{ old('usr_telefono', $admin->usr_telefono) }}">
                    </div>
                    <div class="input-group full-width">
                        <label>Cédula ID (No editable)</label>
                        <input type="text" name="usr_cedula" class="form-input" value="{{ old('usr_cedula', $admin->usr_cedula) }}" readonly style="background: rgba(255,255,255,0.05); color:#888;">
                    </div>
                </div>
                <div style="margin-top:20px;">
                    <button type="submit" class="btn-save">💾 Guardar Cambios</button>
                </div>
            </form>
        </div>

        <div id="tab-security" class="tab-content">
            <h2 class="section-title">🔒 Seguridad y Acceso</h2>
            <p class="section-subtitle">Gestiona tu contraseña</p>
            
            <form action="{{ route('admin.perfil.password') }}" method="POST">
                @csrf @method('PUT')
                <div class="form-grid">
                    <div class="input-group full-width">
                        <label>Contraseña Actual</label>
                        <input type="password" name="current_password" class="form-input" placeholder="••••••••" required>
                    </div>
                    <div class="input-group">
                        <label>Nueva Contraseña</label>
                        <input type="password" name="new_password" class="form-input" placeholder="Mínimo 6 caracteres" required>
                    </div>
                    <div class="input-group">
                        <label>Confirmar Contraseña</label>
                        <input type="password" name="new_password_confirmation" class="form-input" placeholder="Repetir nueva contraseña" required>
                    </div>
                </div>
                <div style="margin-top:20px;">
                    <button type="submit" class="btn-save">🔑 Cambiar Contraseña</button>
                </div>
            </form>
        </div>

        <div id="tab-activity" class="tab-content">
            <h2 class="section-title">📊 Historial de Actividad</h2>
            <p class="section-subtitle">Registro de tus últimas acciones en el sistema</p>

            <div style="display:flex; justify-content:flex-end; margin-bottom:20px;">
                <button type="button" class="menu-btn" id="btn-export" onclick="exportarPDF()" style="font-size:13px; border-color:var(--accent-primary); color:var(--accent-primary);">
                    📥 Exportar Reporte PDF
                </button>
            </div>

            <div id="activity-log-container">
                <div class="activity-item">
                    <div class="act-icon" style="background:rgba(76, 175, 80, 0.2); color:#4caf50;">🔐</div>
                    <div class="act-info">
                        <h5>Inicio de sesión exitoso</h5>
                        <p>Acceso al sistema desde panel principal</p>
                    </div>
                    <div class="act-time">Hace 5 min</div>
                </div>

                <div class="activity-item">
                    <div class="act-icon" style="background:rgba(33, 150, 243, 0.2); color:#2196f3;">👤</div>
                    <div class="act-info">
                        <h5>Visualización de Perfil</h5>
                        <p>El administrador accedió a la configuración de cuenta</p>
                    </div>
                    <div class="act-time">Hace 10 min</div>
                </div>

                <div class="activity-item">
                    <div class="act-icon" style="background:rgba(255, 193, 7, 0.2); color:#ffc107;">⚙️</div>
                    <div class="act-info">
                        <h5>Gestión de Servicios</h5>
                        <p>Se actualizó el catálogo de servicios</p>
                    </div>
                    <div class="act-time">Ayer, 16:45</div>
                </div>
                
                <div class="activity-item">
                    <div class="act-icon" style="background:rgba(156, 39, 176, 0.2); color:#ab47bc;">🎁</div>
                    <div class="act-info">
                        <h5>Promoción Creada</h5>
                        <p>Se creó la promoción "Descuento Bienvenida"</p>
                    </div>
                    <div class="act-time">12/02/2026</div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    // --- LÓGICA DE PESTAÑAS ---
    function switchTab(tabName) {
        document.querySelectorAll('.menu-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        const buttons = document.querySelectorAll('.profile-menu .menu-btn');
        if(tabName === 'personal') buttons[0].classList.add('active');
        if(tabName === 'security') buttons[1].classList.add('active');
        if(tabName === 'activity') buttons[2].classList.add('active');

        document.getElementById(`tab-${tabName}`).classList.add('active');
    }

    // --- LÓGICA DE REPORTE PDF PREMIUM ---
    function exportarPDF() {
        const btn = document.getElementById('btn-export');
        const originalText = btn.innerHTML;
        btn.innerHTML = '⏳ Generando...';
        btn.disabled = true;

        // 1. Extraer datos del DOM
        const items = document.querySelectorAll('#activity-log-container .activity-item');
        let tableRows = '';

        items.forEach((item, index) => {
            const icon = item.querySelector('.act-icon').innerText;
            const title = item.querySelector('.act-info h5').innerText;
            const desc = item.querySelector('.act-info p').innerText;
            const time = item.querySelector('.act-time').innerText;
            
            // Fondo alternado para filas (Zebra Striping)
            const bgRow = index % 2 === 0 ? '#1f1f1f' : '#141414';

            tableRows += `
                <tr style="background-color: ${bgRow};">
                    <td style="padding: 12px; border-bottom: 1px solid #333; text-align: center; font-size: 18px; width: 50px;">${icon}</td>
                    <td style="padding: 12px; border-bottom: 1px solid #333;">
                        <div style="font-weight: bold; color: #fff; font-size: 13px; font-family: Helvetica, sans-serif;">${title}</div>
                        <div style="color: #999; font-size: 11px; margin-top: 4px; font-family: Helvetica, sans-serif;">${desc}</div>
                    </td>
                    <td style="padding: 12px; border-bottom: 1px solid #333; text-align: right; color: #FAD370; font-size: 11px; font-weight: bold; width: 100px; font-family: Helvetica, sans-serif;">
                        ${time}
                    </td>
                </tr>
            `;
        });

        // 2. Construir la plantilla HTML específica para el PDF (Diseño Corporativo Dark)
        const content = `
            <div style="font-family: 'Helvetica', sans-serif; background-color: #0a0a0a; color: #ffffff; padding: 40px; width: 100%; box-sizing: border-box;">
                
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #FAD370; padding-bottom: 20px; margin-bottom: 30px;">
                    <div>
                        <h1 style="margin: 0; color: #FAD370; font-size: 28px; text-transform: uppercase; letter-spacing: 2px;">StyleNow</h1>
                        <p style="margin: 5px 0 0; font-size: 10px; color: #666; text-transform: uppercase; letter-spacing: 1px;">Reporte Oficial de Actividad</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="margin: 0; font-size: 10px; color: #888; text-transform: uppercase;">Fecha de Emisión</p>
                        <p style="margin: 3px 0 0; font-size: 12px; font-weight: bold; color: #fff;">${new Date().toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                    </div>
                </div>

                <div style="background-color: #1a1a1a; padding: 20px; border-radius: 8px; margin-bottom: 40px; border-left: 4px solid #FAD370;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="color: #888; font-size: 10px; text-transform: uppercase; padding-bottom: 5px;">Generado por:</td>
                            <td style="color: #888; font-size: 10px; text-transform: uppercase; padding-bottom: 5px; text-align: right;">ID Sistema:</td>
                        </tr>
                        <tr>
                            <td style="font-size: 16px; font-weight: bold; color: #fff;">{{ $admin->usr_nombre }} {{ $admin->usr_apellido }}</td>
                            <td style="font-size: 16px; font-weight: bold; color: #fff; text-align: right;">#{{ $admin->usr_id }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 12px; color: #ccc; padding-top: 5px;">{{ $admin->usr_email }}</td>
                            <td style="font-size: 12px; color: #ccc; padding-top: 5px; text-align: right;">Rol: Administrador</td>
                        </tr>
                    </table>
                </div>

                <h3 style="font-size: 14px; color: #FAD370; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 1px;">Historial de Movimientos</h3>

                <table style="width: 100%; border-collapse: collapse; background-color: #1a1a1a; border-radius: 8px; overflow: hidden;">
                    <thead>
                        <tr style="background-color: #FAD370; color: #000;">
                            <th style="padding: 12px; font-size: 10px; text-transform: uppercase; text-align: center; width: 50px; font-weight: bold;">Tipo</th>
                            <th style="padding: 12px; font-size: 10px; text-transform: uppercase; text-align: left; font-weight: bold;">Detalle de la Acción</th>
                            <th style="padding: 12px; font-size: 10px; text-transform: uppercase; text-align: right; width: 100px; font-weight: bold;">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tableRows}
                    </tbody>
                </table>

                <div style="margin-top: 60px; text-align: center; border-top: 1px solid #333; padding-top: 20px;">
                    <p style="font-size: 10px; color: #555;">Este documento es un reporte generado automáticamente por el sistema StyleNow.<br>Contiene información confidencial.</p>
                </div>
            </div>
        `;

        // 3. Configuración Óptima para PDF
        const opt = {
            margin:       0, // Sin márgenes blancos feos
            filename:     `Reporte_Actividad_StyleNow_${new Date().toISOString().slice(0,10)}.pdf`,
            image:        { type: 'jpeg', quality: 1 },
            html2canvas:  { 
                scale: 2, // Alta resolución (para que no se vea borroso)
                useCORS: true, 
                backgroundColor: '#0a0a0a' // Forzar fondo negro
            },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // 4. Generar y Descargar
        html2pdf().set(opt).from(content).save().then(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
</script>
@endsection