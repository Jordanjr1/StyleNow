@extends('layouts.app')

@section('title', 'Promociones - StyleNow')

@push('styles')
<style>
    :root {
        --bg-card: #151515;
        --border-color: #2a2a2a;
        --text-muted: #888888;
        --dorado: #fad370;
        --radius-md: 12px;
        --radius-lg: 16px;
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
    .promo-container { max-width: 1100px; margin: 0 auto;}

    /* BANNER DESTACADO */
    .featured-banner {
        background: linear-gradient(135deg, var(--dorado), #D4AF37);
        border-radius: var(--radius-lg); padding: 30px; margin-bottom: 30px; color: #000; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(250, 211, 112, 0.15);
    }
    .banner-content { position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; gap: 30px; flex-wrap: wrap; }
    .banner-text h2 { font-family: 'Abril Fatface', cursive; font-size: 28px; margin-bottom: 5px; }
    .banner-text p { font-weight: 500; font-size: 15px; opacity: 0.9;}
    .banner-badge { background: rgba(0, 0, 0, 0.1); backdrop-filter: blur(10px); padding: 12px 24px; border-radius: var(--radius-md); font-weight: bold; font-size: 18px; border: 1px solid rgba(0, 0, 0, 0.2); }

    /* FILTROS */
    .filters { display: flex; gap: 10px; margin-bottom: 30px; flex-wrap: wrap; }
    .filter-btn { padding: 8px 20px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 20px; color: var(--text-secondary); cursor: pointer; transition: 0.3s; font-size: 14px; font-weight: 600; }
    .filter-btn:hover { border-color: var(--dorado); color: var(--dorado); }
    .filter-btn.active { background: rgba(250, 211, 112, 0.1); color: var(--dorado); border-color: var(--dorado); }

    /* GRID PROMOCIONES */
    .promotions-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; margin-bottom: 40px; }
    .promotion-card { background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color); transition: 0.3s; display: flex; flex-direction: column; overflow: hidden; }
    .promotion-card:hover { transform: translateY(-5px); border-color: var(--dorado); box-shadow: 0 10px 20px rgba(0,0,0,0.5); }
    
    .promotion-header { padding: 25px 25px 15px; position: relative; }
    .promotion-icon { width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 15px; background: rgba(250, 211, 112, 0.1); }
    .promotion-category { position: absolute; top: 20px; right: 20px; padding: 5px 12px; background: #222; color: var(--text-muted); border-radius: 20px; font-size: 11px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase;}
    .promotion-title { font-size: 1.2rem; font-weight: bold; margin-bottom: 8px; color: white; }
    .promotion-description { color: var(--text-muted); font-size: 13px; line-height: 1.5; margin-bottom: 15px; min-height: 40px;}
    
    .promotion-body { padding: 0 25px 20px; flex: 1; }
    .promotion-details { display: flex; align-items: center; gap: 15px; }
    .discount-badge { font-size: 24px; font-weight: bold; color: var(--dorado); font-family: 'Abril Fatface', cursive;}
    .code-container { flex: 1; text-align: right;}
    .code-label { font-size: 11px; color: var(--text-muted); margin-bottom: 4px; text-transform: uppercase;}
    .promotion-code { display: inline-block; background: #111; padding: 6px 12px; border-radius: 8px; font-family: monospace; font-weight: bold; font-size: 14px; color: white; border: 1px dashed #444; cursor: pointer; transition: 0.3s;}
    .promotion-code:hover { border-color: var(--dorado); color: var(--dorado);}

    .promotion-footer { padding: 15px 25px; border-top: 1px solid var(--border-color); background: #111; display: flex; justify-content: space-between; align-items: center;}
    .expiry-info { font-size: 12px; color: var(--text-muted); }
    .expiry-date { font-weight: bold; color: white; }
    .expiry-soon { color: #ef4444; }
    
    .btn-view { background: transparent; color: var(--dorado); border: 1px solid var(--dorado); padding: 6px 15px; border-radius: 8px; font-size: 13px; font-weight: bold; cursor: pointer; transition: 0.3s;}
    .btn-view:hover { background: var(--dorado); color: black;}

    /* EXPIRA PRONTO */
    .expiring-section { background: var(--bg-card); border-radius: var(--radius-lg); padding: 25px; margin-bottom: 40px; border: 1px dashed #ef4444; }
    .expiring-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px; margin-top: 15px; }
    .expiring-card { background: #111; border-radius: 12px; padding: 15px; border-left: 4px solid #ef4444; }

    /* MODAL */
    .promo-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; display: none; justify-content: center; align-items: center; }
    .promo-modal { background: var(--bg-card); width: 90%; max-width: 450px; border-radius: 16px; padding: 25px; border: 1px solid var(--border-color); position: relative;}
    .close-modal { position: absolute; top: 15px; right: 20px; font-size: 24px; color: #888; cursor: pointer; }
    .close-modal:hover { color: white; }

    /* INFO EXTRA */
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;}
    .info-item { display: flex; gap: 15px; background: var(--bg-card); padding: 20px; border-radius: 12px; border: 1px solid var(--border-color);}
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
        <a href="/cliente/puntos" class="nav-item">
            <span class="nav-icon">⭐</span><span class="nav-text">Mis Puntos</span>
        </a>
        <a href="/cliente/promo-vista" class="nav-item active">
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
    <h1 style="font-family: 'Abril Fatface', cursive; font-size: 2rem; color: var(--text-primary); margin-bottom: 0.3rem;">Promociones</h1>
    <p style="color: var(--text-secondary); font-size: 0.95rem;">Descubre ofertas exclusivas y ahorra en tu próxima visita.</p>
@endsection

@section('content')
<div class="promo-container">

    @if (count($promocionesFormateadas) > 0)
    <div class="featured-banner">
        <div class="banner-content">
            <div class="banner-text">
                <h2>✨ Ofertas Exclusivas para Ti</h2>
                <p>Aprovecha nuestros descuentos especiales antes de que caduquen.</p>
            </div>
            <div class="banner-badge">
                {{ count($promocionesFormateadas) }} Promociones Activas
            </div>
        </div>
    </div>
    @endif

    <div class="filters">
        @foreach ($categorias as $categoria)
            <button class="filter-btn {{ $categoria === 'Todas' ? 'active' : '' }}" onclick="filterPromotions('{{ $categoria }}', this)">
                {{ $categoria }}
            </button>
        @endforeach
    </div>

    <div class="promotions-grid" id="promotionsGrid">
        @if (empty($promocionesFormateadas))
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: var(--bg-card); border-radius: 16px; border: 1px dashed var(--border-color);">
                <div style="font-size: 4rem; opacity: 0.5; margin-bottom: 15px;">📭</div>
                <h3 style="color: white; margin-bottom: 10px; font-family: 'Abril Fatface', cursive;">No hay promociones activas</h3>
                <p style="color: var(--text-muted);">Vuelve pronto para descubrir nuevas ofertas en StyleNow.</p>
            </div>
        @else
            @foreach ($promocionesFormateadas as $promo)
                <div class="promotion-card promo-item" data-category="{{ $promo['categoria'] }}">
                    <div class="promotion-header">
                        <div class="promotion-icon" @style(['color: ' . $promo['color']])>
    {{ $promo['imagen'] }}
</div>
                        <span class="promotion-category">{{ $promo['categoria'] }}</span>
                        <h3 class="promotion-title">
                            {{ $promo['titulo'] }}
                            @if ($promo['destacado']) <span style="font-size: 14px;">🔥</span> @endif
                        </h3>
                        <p class="promotion-description">{{ Str::limit($promo['descripcion'], 80) }}</p>
                    </div>
                    
                    <div class="promotion-body">
                        <div class="promotion-details">
                            <div class="discount-badge">{{ $promo['descuento'] }}</div>
                            <div class="code-container">
                                <div class="code-label">CÓDIGO PROMO</div>
                                <div class="promotion-code" 
    data-codigo="{{ $promo['codigo'] }}"
    onclick="copyCode(this.dataset.codigo)" 
    title="Clic para copiar">
    {{ $promo['codigo'] }}
</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="promotion-footer">
                        <div class="expiry-info">
                            Válido hasta: <br>
                            <span class="expiry-date {{ $promo['destacado'] ? 'expiry-soon' : '' }}">
                                {{ \Carbon\Carbon::parse($promo['valido_hasta'])->format('d/m/Y') }}
                                @if ($promo['destacado']) ({{ $promo['dias_restantes'] }} días) @endif
                            </span>
                        </div>
<button class="btn-view" 
    data-id="{{ $promo['id'] }}"
    onclick="viewConditions(this.dataset.id)">Ver Detalles</button>                    </div>
                </div>
            @endforeach
        @endif
    </div>

    @if (!empty($promocionesProximas))
    <div class="expiring-section">
        <h3 style="color: white; font-size: 1.2rem; margin-bottom: 5px; display:flex; align-items:center; gap:10px;">
            <span style="color: #ef4444; font-size: 1.5rem;">⏰</span> ¡Última oportunidad!
        </h3>
        <p style="color: var(--text-muted); font-size: 13px;">Estas promociones expirarán en los próximos 7 días.</p>
        
        <div class="expiring-grid">
            @foreach ($promocionesProximas as $promo)
                <div class="expiring-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <h4 style="color: white; font-size: 14px;">{{ $promo['titulo'] }}</h4>
                        <span style="color: #ef4444; font-size: 12px; font-weight: bold; background: rgba(239,68,68,0.1); padding: 3px 8px; border-radius: 10px;">{{ $promo['dias_restantes'] }} días</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <span style="color: var(--dorado); font-weight: bold;">{{ $promo['descuento'] }}</span>
<button class="btn-view" 
    data-codigo="{{ $promo['codigo'] }}"
    onclick="copyCode(this.dataset.codigo)"
    @style(['padding: 4px 10px', 'font-size: 11px'])>Copiar Código</button>                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div style="margin-top: 40px; margin-bottom: 20px;">
        <h3 style="font-family: 'Abril Fatface', cursive; font-size: 1.5rem; color: white;">¿Cómo funciona?</h3>
        <div class="info-grid">
            <div class="info-item">
                <div style="font-size: 2rem;">📱</div>
                <div>
                    <h4 style="color: white; margin-bottom: 5px; font-size: 14px;">1. Copia el Código</h4>
                    <p style="color: var(--text-muted); font-size: 13px;">Haz clic sobre el código negro de la promoción que más te guste para copiarlo a tu portapapeles.</p>
                </div>
            </div>
            <div class="info-item">
                <div style="font-size: 2rem;">📅</div>
                <div>
                    <h4 style="color: white; margin-bottom: 5px; font-size: 14px;">2. Agenda tu Cita</h4>
                    <p style="color: var(--text-muted); font-size: 13px;">Ve a la sección "Nueva Cita" y selecciona los servicios que aplican para el descuento.</p>
                </div>
            </div>
            <div class="info-item">
                <div style="font-size: 2rem;">✂️</div>
                <div>
                    <h4 style="color: white; margin-bottom: 5px; font-size: 14px;">3. Disfruta</h4>
                    <p style="color: var(--text-muted); font-size: 13px;">Al llegar al salón, díle a recepción tu código promocional y el descuento se aplicará al pagar.</p>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="promo-modal-overlay" id="conditionsModal">
    <div class="promo-modal">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px;">
            <div id="modalIcon" style="font-size: 30px; background: rgba(250, 211, 112, 0.1); width: 60px; height: 60px; display: flex; justify-content: center; align-items: center; border-radius: 12px;"></div>
            <div>
                <h3 id="modalTitle" style="color: white; font-size: 1.2rem; margin-bottom: 3px;"></h3>
                <span id="modalCategory" style="color: var(--text-muted); font-size: 12px; text-transform: uppercase;"></span>
            </div>
        </div>
        
        <div style="background: #111; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px; border: 1px dashed var(--border-color);">
            <div style="color: var(--text-muted); font-size: 11px; margin-bottom: 5px;">CÓDIGO PROMOCIONAL</div>
            <div id="modalCode" style="color: var(--dorado); font-family: monospace; font-size: 20px; font-weight: bold;"></div>
        </div>

        <h4 style="color: white; font-size: 14px; margin-bottom: 10px;">📋 Términos y Condiciones</h4>
        <p id="modalConditions" style="color: #ccc; font-size: 13px; line-height: 1.6; margin-bottom: 20px;"></p>
        
        <button onclick="copyCodeFromModal()" style="width: 100%; background: var(--dorado); color: black; border: none; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s;">Copiar Código y Cerrar</button>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Pasar los datos de PHP a Javascript
    const promociones = JSON.parse('<?= addslashes(json_encode($promocionesFormateadas)) ?>');
    let currentModalCode = '';

    // Filtrar Promociones
    function filterPromotions(categoria, btnElement) {
        // Actualizar botones
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        btnElement.classList.add('active');
        
        // Filtrar tarjetas
        const cards = document.querySelectorAll('.promo-item');
        cards.forEach(card => {
            if (categoria === 'Todas' || card.dataset.category === categoria) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Copiar Código con SweetAlert
    function copyCode(codigo) {
        navigator.clipboard.writeText(codigo).then(() => {
            Swal.fire({
                icon: 'success',
                title: '¡Código Copiado!',
                html: `El código <b style="color: #fad370;">${codigo}</b> está listo para usarse.`,
                background: '#151515', color: '#fff',
                showConfirmButton: false, timer: 2000,
                toast: true, position: 'top-end'
            });
        });
    }

    function copyCodeFromModal() {
        copyCode(currentModalCode);
        closeModal();
    }

    // Lógica del Modal
    function viewConditions(id) {
        const promo = promociones.find(p => p.id === id);
        if(promo) {
            document.getElementById('modalIcon').innerText = promo.imagen;
            document.getElementById('modalTitle').innerText = promo.titulo;
            document.getElementById('modalCategory').innerText = promo.categoria;
            document.getElementById('modalCode').innerText = promo.codigo;
            document.getElementById('modalConditions').innerText = promo.condiciones;
            currentModalCode = promo.codigo;
            
            document.getElementById('conditionsModal').style.display = 'flex';
        }
    }

    function closeModal() {
        document.getElementById('conditionsModal').style.display = 'none';
    }

    // Cerrar modal al tocar fuera
    window.onclick = function(event) {
        const modal = document.getElementById('conditionsModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endsection