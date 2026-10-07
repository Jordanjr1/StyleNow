<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'StyleNow')</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=David+Libre:wght@400;500;700&display=swap" rel="stylesheet">

    @stack('styles')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --dorado: #FAD370;
            --negro: #0a0a0a;
            --gris-oscuro: #1a1a1a;
            --gris-medio: #2a2a2a;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --bg-primary: #0a0a0a;
            --bg-secondary: #1a1a1a;
            --bg-card: #1a1a1a;
            --border-color: rgba(250, 211, 112, 0.2);
        }

        [data-theme="light"] {
            --negro: #ffffff;
            --gris-oscuro: #f5f5f5;
            --gris-medio: #e5e5e5;
            --text-primary: #0a0a0a;
            --text-secondary: #666666;
            --bg-primary: #ffffff;
            --bg-secondary: #f5f5f5;
            --bg-card: #fad3701a;
            --border-color: rgba(250, 211, 112, 0.3);
        }

        body {
            font-family: 'David Libre', serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            padding: 2rem 0;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
        }

        .logo-sidebar {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            margin-bottom: 1.5rem;
        }

        .logo-sidebar img {
            width: 45px;
            height: 45px;
        }

        .logo-sidebar span {
            font-family: 'Abril Fatface', cursive;
            font-size: 1.5rem;
            color: var(--text-primary);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(250, 211, 112, 0.1);
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--dorado);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--negro);
        }

        .user-details h4 {
            font-size: 1rem;
            color: var(--text-primary);
            margin-bottom: 0.2rem;
        }

        .user-role {
            font-size: 0.85rem;
            color: var(--dorado);
            font-weight: 600;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            padding: 2rem;
            transition: margin-left 0.3s ease;
        }

        /* TOPBAR */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: var(--bg-card);
            border-radius: 15px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .theme-toggle {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(250, 211, 112, 0.1);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.3rem;
        }

        .theme-toggle:hover {
            background: var(--dorado);
            transform: scale(1.1);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .topbar-left h1 {
            font-family: 'Abril Fatface', cursive;
            font-size: 2rem;
            color: var(--text-primary);
            margin-bottom: 0.3rem;
        }

        .topbar-left p {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>

    <style>
        /* Estilos del sidebar-nav */
    .sidebar-nav {
        padding: 2rem 0;
    }

    .nav-section {
        margin-bottom: 1.5rem;
    }

    .nav-section-title {
        padding: 0 1.5rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
        font-weight: 700;
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.9rem 1.5rem;
        color: var(--text-primary);
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .nav-item:hover {
        background: rgba(250, 211, 112, 0.1);
        border-left: 3px solid var(--dorado);
        padding-left: calc(1.5rem - 3px);
    }

    .nav-item.active {
        background: rgba(250, 211, 112, 0.15);
        border-left: 3px solid var(--dorado);
        padding-left: calc(1.5rem - 3px);
        color: var(--dorado);
    }

    .nav-icon {
        font-size: 1.2rem;
        width: 24px;
        text-align: center;
    }

    .nav-text {
        flex: 1;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .nav-arrow {
        font-size: 0.8rem;
        transition: transform 0.3s ease;
    }

    .nav-item.expanded .nav-arrow {
        transform: rotate(90deg);
    }

    .submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        background: rgba(0, 0, 0, 0.2);
    }

    .submenu.open {
        max-height: 500px;
    }

    .submenu-item {
        display: block;
        padding: 0.7rem 1.5rem 0.7rem 4rem;
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .submenu-item:hover {
        color: var(--dorado);
        background: rgba(250, 211, 112, 0.05);
    }

    .view-all-btn {
        padding: 12px 24px;
        background: rgba(250, 211, 112, 0.1);
        border: 1px solid var(--border-color);
        color: var(--dorado);
        font-size: 0.85rem;
        font-weight: 600;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .view-all-btn:hover {
        background: var(--dorado);
        color: var(--negro);
    }

    /* STATS GRID */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--bg-card);
        padding: 1.5rem;
        border-radius: 15px;
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: var(--dorado);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(250, 211, 112, 0.2);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        background: rgba(250, 211, 112, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-trend {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.85rem;
        padding: 0.3rem 0.6rem;
        border-radius: 20px;
    }

    .stat-trend.positive {
        color: #4ade80;
        background: rgba(74, 222, 128, 0.1);
    }

    .stat-trend.negative {
        color: #ff4444;
        background: rgba(255, 68, 68, 0.1);
    }

   .notification-btn {
        position: relative;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: rgba(250, 211, 112, 0.1);
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1.2rem;
    }

    .notification-btn:hover {
        background: var(--dorado);
        transform: scale(1.1);
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 20px;
        height: 20px;
        background: #ff4444;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        color: white;
        font-weight: 700;
    }

    .user-date {
        text-align: right;
    }

    .user-greeting {
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    .current-date {
        font-size: 0.85rem;
        color: var(--dorado);
        font-weight: 600;
    }

    .topbar-left h1 {
        font-family: 'Abril Fatface', cursive;
        font-size: 2rem;
        color: var(--text-primary);
        margin-bottom: 0.3rem;
    }

    .topbar-left p {
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    /* RESPONSIVE */
    @media (max-width: 1400px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 1024px) {
        .actions-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .stats-grid,
        .actions-grid {
            grid-template-columns: 1fr;
        }
    }
  
    </style>
</head>
<body data-theme="dark">

     <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="/" class="logo-sidebar">
                <img src="/images/SN_icon.png" alt="StyleNow">
                <span>StyleNow</span>
            </a>

            <div class="user-info">
                <div class="user-avatar">
                    {{ substr(Auth::user()->usr_nombre, 0, 1) }}
                </div>
                <div class="user-details">
                    <h4>{{ Auth::user()->usr_nombre }}</h4>
                    @php
                        $rolId = Auth::user()->usr_rolId;
                        $rolText = '';
                        switch($rolId) {
                            case 1: $rolText = 'Administrador'; break;
                            case 2: $rolText = 'Empleado'; break;
                            case 3: $rolText = 'Cliente'; break;
                            default: $rolText = 'Usuario';
                        }
                    @endphp
                    <p class="user-role">{{ $rolText }}</p>
                </div>
            </div>
        </div>

        <!-- MENÚ DINÁMICO SEGÚN ROL -->
        @php
            $rolId = Auth::user()->usr_rolId;
        @endphp

        @if($rolId == 1) <!-- ADMINISTRADOR -->
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <p class="nav-section-title">Principal</p>
                    <a href="{{ route('admin.dashboard') }}" 
               class="nav-item {{ request()->routeIs('admin.dashboard') || request()->is('admin/dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </div>

                <div class="nav-section">
                    <p class="nav-section-title">Gestión</p>
                    
                    <div class="nav-item {{ request()->is('admin/usuarios*') ? 'active' : '' }}" onclick="toggleSubmenu(this)">
                        <span class="nav-icon">👥</span>
                        <span class="nav-text">Usuarios</span>
                        <span class="nav-arrow">▸</span>
                    </div>
                    <div class="submenu">
                        <a href="/admin/usuarios" class="submenu-item">Listar Usuarios</a>
                    </div>

                    <div class="nav-item {{ request()->is('admin/sucursales*') || request()->is('admin/servicios*') || request()->is('admin/promociones*') || request()->is('admin/categorias*') ? 'active' : '' }}" onclick="toggleSubmenu(this)">
                        <span class="nav-icon">⚙️</span>
                        <span class="nav-text">Configuración</span>
                        <span class="nav-arrow">▸</span>
                    </div>
                    <div class="submenu">
                        <a href="/admin/sucursales" class="submenu-item {{ request()->is('admin/sucursales*') ? 'active' : '' }}">
                            Sucursales
                        </a>
                        <a href="/admin/servicios" class="submenu-item {{ request()->is('admin/servicios*') ? 'active' : '' }}">
                            Servicios
                        </a>
                        <a href="/admin/promociones" class="submenu-item {{ request()->is('admin/promociones*') ? 'active' : '' }}">
                            Promociones
                        </a>
                        <a href="/admin/categorias" class="submenu-item {{ request()->is('admin/categorias*') ? 'active' : '' }}">
                            Categorías
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <p class="nav-section-title">Operaciones</p>
                    
                    <div class="nav-item {{ request()->is('admin/citas*') || request()->is('admin/notificaciones*') ? 'active' : '' }}" onclick="toggleSubmenu(this)">
                        <span class="nav-icon">📅</span>
                        <span class="nav-text">Reservas</span>
                        <span class="nav-arrow">▸</span>
                    </div>
                    <div class="submenu">
                        <a href="/admin/citas" class="submenu-item {{ request()->is('admin/citas*') ? 'active' : '' }}">Ver Citas</a>
                        <a href="/admin/recordatorios" class="submenu-item {{ request()->is('admin/recordatorios*') ? 'active' : '' }}">Notificaciones</a>
                    </div>

                    <div class="nav-item {{ request()->is('admin/productos*') || request()->is('admin/proveedores*') || request()->is('admin/inventario/solicitudes*') ? 'active' : '' }}" onclick="toggleSubmenu(this)">
    <span class="nav-icon">📦</span>
    <span class="nav-text">Inventario</span>
    <span class="nav-arrow">▸</span>
</div>
<div class="submenu">
    <a href="/admin/productos" class="submenu-item {{ request()->is('admin/productos*') ? 'active' : '' }}">
        Productos
    </a>
    <a href="/admin/proveedores" class="submenu-item {{ request()->is('admin/proveedores*') ? 'active' : '' }}">
        Proveedores
    </a>
    <a href="/admin/inventario/solicitudes" class="submenu-item {{ request()->is('admin/inventario/solicitudes*') ? 'active' : '' }}">
        Alertas
    </a>
</div>

                    <div class="nav-item {{ request()->is('admin/reportes/financieros*') || request()->is('admin/reportes/inventario*') || request()->is('admin/reportes/productividad*') ? 'active' : '' }}" onclick="toggleSubmenu(this)">
                        <span class="nav-icon">📈</span>
                        <span class="nav-text">Reportes</span>
                        <span class="nav-arrow">▸</span>
                    </div>
                    <div class="submenu">
                        <a href="/admin/reportes/financieros" class="submenu-item {{ request()->is('admin/reportes/financieros*') ? 'active' : '' }}">Financieros</a>
                        <a href="/admin/reportes/productividad" class="submenu-item {{ request()->is('admin/reportes/productividad*') ? 'active' : '' }}">Productividad</a>
                        <a href="/admin/reportes/inventario" class="submenu-item {{ request()->is('admin/reportes/inventario*') ? 'active' : '' }}">Inventario</a>
                    </div>
                </div>

                <div class="nav-section">
                    <p class="nav-section-title">Cuenta</p>
                    <a href="/admin/perfil" class="nav-item {{ request()->is('admin/perfil*') ? 'active' : '' }}">
                        <span class="nav-icon">👤</span>
                        <span class="nav-text">Mi Perfil</span>
                    </a>
                </div>
            </nav>

        @elseif($rolId == 2) <!-- EMPLEADO -->
            <!-- El menú de empleado se cargará desde @yield('sidebar-empleado') -->
            @yield('sidebar-empleado')

        @elseif($rolId == 3) <!-- CLIENTE -->
            <!-- El menú de cliente se cargará desde @yield('sidebar-cliente') -->
            @yield('sidebar-cliente')
            
        @endif
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- TOPBAR -->
        <div class="topbar">
            <div class="topbar-left">
                @yield('topbar-left')
            </div>
            <div class="topbar-right">
                <div class="theme-toggle" onclick="toggleTheme()" title="Cambiar tema">
                    <span id="theme-icon">🌙</span>
                </div>
                
                <div class="notification-wrapper" style="position: relative;">
    @php $rolUser = Auth::user()->usr_rolId; @endphp
    
    @if($rolUser == 3) 
        <div class="notification-btn" onclick="toggleNotificacionesCliente()" style="cursor: pointer;" id="bellClientBtn">
            <span class="nav-icon">🔔</span>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <span class="badge" style="position: absolute; top: -5px; right: -5px; background: #ef4444; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; font-weight: bold;">
                    {{ auth()->user()->unreadNotifications->count() }}
                </span>
            @endif
        </div>

        <div id="notifDropdownCliente" style="display: none; position: absolute; top: 120%; right: 0; width: 320px; background: #1a1a1a; border: 1px solid rgba(250, 211, 112, 0.3); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); z-index: 9999; overflow: hidden; text-align: left;">
            <div style="padding: 15px; border-bottom: 1px solid #333; font-weight: bold; color: #fff;">
                Mis Notificaciones
            </div>
            <div style="max-height: 300px; overflow-y: auto;">
                @if(auth()->user()->unreadNotifications->count() > 0)
                    @foreach(auth()->user()->unreadNotifications as $notif)
                        <a href="/notificaciones/{{ $notif->id }}/leer" style="display: flex; gap: 15px; padding: 15px; text-decoration: none; border-bottom: 1px solid #222; transition: 0.3s; background: rgba(250, 211, 112, 0.05);" onmouseover="this.style.background='#252525'" onmouseout="this.style.background='rgba(250, 211, 112, 0.05)'">
                            <div style="font-size: 1.5rem;">{{ $notif->data['icono'] ?? '💬' }}</div>
                            <div style="flex-grow: 1;">
                                <div style="color: #fad370; font-size: 0.9rem; font-weight: bold;">{{ $notif->data['titulo'] }}</div>
                                <div style="color: #ccc; font-size: 0.8rem; margin-top: 3px; line-height: 1.3;">{{ $notif->data['mensaje'] }}</div>
                                <div style="color: #ef4444; font-size: 0.7rem; margin-top: 6px; font-weight: bold;">NUEVA ✨</div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <div style="padding: 20px; text-align: center; color: #888; font-size:13px;">📭 No tienes notificaciones nuevas</div>
                @endif
            </div>
        </div>
    @else
        <div class="notification-btn" id="bellIconBtn" style="cursor: pointer;">
            <span class="nav-icon">🔔</span>
            <span class="badge" id="globalNotifBadge" style="display: none; position: absolute; top: -5px; right: -5px; background: #ef4444; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; font-weight: bold;">0</span>
        </div>
        
        <div id="notifDropdown" style="display: none; position: absolute; top: 120%; right: 0; width: 320px; background: #1a1a1a; border: 1px solid rgba(250, 211, 112, 0.3); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); z-index: 9999; overflow: hidden; text-align: left;">
            <div style="padding: 15px; border-bottom: 1px solid #333; font-weight: bold; color: #fff; display:flex; justify-content:space-between;">
                Notificaciones <span style="color: #fad370; font-size:12px; cursor:pointer;" onclick="window.location.href='/admin/inventario/solicitudes'">Ver todas</span>
            </div>
            <div id="notifList" style="max-height: 300px; overflow-y: auto;">
                <div style="padding: 20px; text-align: center; color: #888; font-size:13px;">No hay notificaciones nuevas</div>
            </div>
        </div>
    @endif
</div>
                <div class="user-date">
                    <p class="user-greeting">Hoy</p>
                    <p class="current-date" id="current-date"></p>
                </div>
                
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="theme-toggle" title="Cerrar Sesión" style="border: none;">
                        🚪
                    </button>
                </form>
            </div>
        </div>

        @yield('content')
    </main>

   <script>
        // Toggle theme
        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.getElementById('theme-icon');
            const currentTheme = body.getAttribute('data-theme');
            
            if (currentTheme === 'dark') {
                body.setAttribute('data-theme', 'light');
                themeIcon.textContent = '☀️';
                localStorage.setItem('theme', 'light');
            } else {
                body.setAttribute('data-theme', 'dark');
                themeIcon.textContent = '🌙';
                localStorage.setItem('theme', 'dark');
            }
        }

        // Load saved theme
        window.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            const themeIcon = document.getElementById('theme-icon');
            document.body.setAttribute('data-theme', savedTheme);
            themeIcon.textContent = savedTheme === 'dark' ? '🌙' : '☀️';
        });

        // Toggle submenu
    function toggleSubmenu(element) {
        const submenu = element.nextElementSibling;
        const allSubmenus = document.querySelectorAll('.submenu');
        const allNavItems = document.querySelectorAll('.nav-item');
        
        // Close other submenus
        allSubmenus.forEach(sub => {
            if (sub !== submenu) {
                sub.classList.remove('open');
            }
        });
        
        allNavItems.forEach(item => {
            if (item !== element) {
                item.classList.remove('expanded');
            }
        });
        
        // Toggle current submenu
        submenu.classList.toggle('open');
        element.classList.toggle('expanded');
    }

    // Update date
    function updateDate() {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        const today = new Date().toLocaleDateString('es-ES', options);
        const dateElement = document.getElementById('current-date');
        if (dateElement) {
            dateElement.textContent = today;
        }
    }

    updateDate();
    </script>

    @yield('scripts')



<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bellBtn = document.getElementById('bellIconBtn');
        const dropdown = document.getElementById('notifDropdown');
        
        if(bellBtn && dropdown) {
            bellBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
            });

            document.addEventListener('click', function(e) {
                if (!bellBtn.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });

            fetch('/admin/notificaciones')
                .then(r => r.ok ? r.json() : null)
                .then(data => {
                    if(!data) return;
                    
                    const badge = document.getElementById('globalNotifBadge');
                    const list = document.getElementById('notifList');
                    
                    if(data.count > 0) {
                        badge.innerText = data.count;
                        badge.style.display = 'block';
                        
                        list.innerHTML = '';
                        data.data.forEach(n => {
                            // Si es una alerta automática del sistema (Bajo Stock)
                            if(n.tipo === 'alerta_stock') {
                                list.innerHTML += `
                                    <div style="padding: 15px; border-bottom: 1px solid #333; transition: 0.3s; cursor: pointer; border-left: 3px solid #ef4444;" onmouseover="this.style.background='#252525'" onmouseout="this.style.background='transparent'" onclick="window.location.href='/admin/productos'">
                                        <div style="color: #ef4444; font-weight: bold; font-size: 12px; margin-bottom:3px;">⚠️ ALERTA AUTOMÁTICA</div>
                                        <div style="color: #fff; font-size: 13px;">Quedan solo <b>${n.stock}</b> unidades de <b>${n.producto}</b></div>
                                    </div>
                                `;
                            } 
                            // Si es una petición manual de un empleado
                            else {
                                list.innerHTML += `
                                    <div style="padding: 15px; border-bottom: 1px solid #333; transition: 0.3s; cursor: pointer; border-left: 3px solid #fad370;" onmouseover="this.style.background='#252525'" onmouseout="this.style.background='transparent'" onclick="window.location.href='/admin/inventario/solicitudes'">
                                        <div style="color: #fad370; font-weight: bold; font-size: 12px; margin-bottom:3px;">📢 REPORTE DE EMPLEADO</div>
                                        <div style="color: #fff; font-size: 13px;"><b>${n.creador}</b> pidió <b>${n.producto}</b></div>
                                    </div>
                                `;
                            }
                        });
                    }
                })
                .catch(e => console.log("Error cargando notificaciones"));
        }
    });


    
</script>

<script>
    function toggleNotificacionesCliente() {
        const dropdown = document.getElementById('notifDropdownCliente');
        if(dropdown) {
            dropdown.style.display = dropdown.style.display === 'none' || dropdown.style.display === '' ? 'block' : 'none';
        }
    }

    // Cerrar la campana del cliente si hace clic afuera
    document.addEventListener('click', function(e) {
        const bellBtn = document.getElementById('bellClientBtn');
        const dropdown = document.getElementById('notifDropdownCliente');
        if (bellBtn && dropdown && !bellBtn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
</script>

</body>
</html>