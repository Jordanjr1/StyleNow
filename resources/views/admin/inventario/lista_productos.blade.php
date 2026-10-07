@extends('layouts.app')

@section('title', 'Inventario de Productos - StyleNow')

@push('styles')
<style>
    :root {
        --accent-primary: #fad370;
        --bg-card: #2a2a2a;
        --bg-dark: #1a1a1a;
        --text-primary: #ffffff;
        --text-muted: #a0a0a0;
        --border-color: rgba(250, 211, 112, 0.2);
    }

    .inventory-container { padding: 20px; color: var(--text-primary); }

    /* Stats Grid */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: var(--bg-card); padding: 20px; border-radius: 15px; border: 1px solid var(--border-color); box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
    .stat-label { color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase; }
    .stat-value { font-size: 1.8rem; font-weight: bold; color: var(--accent-primary); margin: 5px 0; }

    /* Table Styling */
    .table-container { background: var(--bg-card); border-radius: 15px; overflow: hidden; border: 1px solid var(--border-color); overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th { background: rgba(250, 211, 112, 0.1); padding: 15px; text-align: left; color: var(--accent-primary); font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;}
    td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; vertical-align: middle;}
    tr:hover { background: rgba(255,255,255,0.02); }

    .product-info-cell { display: flex; align-items: center; gap: 15px; }
    .product-img-mini { width: 45px; height: 45px; border-radius: 8px; object-fit: cover; background: #333; border: 1px solid #444; }
    .product-img-placeholder { width: 45px; height: 45px; border-radius: 8px; background: #333; display: flex; align-items: center; justify-content: center; font-size: 20px; border: 1px solid #444;}

    /* Modal Styling */
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px); }
    .modal.active { display: flex; }
    .modal-content { background: var(--bg-dark); width: 800px; padding: 30px; border-radius: 20px; border: 1px solid var(--accent-primary); max-height: 90vh; overflow-y: auto; }

    /* Forms */
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .form-group { margin-bottom: 15px; }
    .form-group.full { grid-column: 1 / -1; }
    label { display: block; margin-bottom: 5px; color: var(--accent-primary); font-size: 0.9rem; font-weight: 600;}
    .form-input { width: 100%; padding: 10px; background: #333; border: 1px solid #444; color: white; border-radius: 8px; outline: none; }
    .form-input:focus { border-color: var(--accent-primary); }

    .img-upload-box { border: 2px dashed #444; border-radius: 10px; padding: 20px; text-align: center; background: #222; cursor: pointer; transition: 0.3s; position: relative;}
    .img-upload-box:hover { border-color: var(--accent-primary); background: rgba(250, 211, 112, 0.05); }
    .preview-img { max-height: 120px; border-radius: 8px; display: none; margin: 0 auto; }

    .btn-add { background: var(--accent-primary); color: #000; padding: 12px 25px; border-radius: 10px; border: none; font-weight: bold; cursor: pointer; transition: 0.3s; }
    .btn-add:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(250, 211, 112, 0.3); }
    
    .badge { padding: 4px 8px; border-radius: 6px; font-size: 0.8rem; font-weight: bold;}
    .badge-stock { background: rgba(255, 71, 87, 0.2); color: #ff4757; border: 1px solid #ff4757; }
    .badge-ok { background: rgba(46, 213, 115, 0.2); color: #2ed573; border: 1px solid #2ed573; }
    .badge-iva { background: rgba(59, 130, 246, 0.2); color: #3b82f6; border: 1px solid #3b82f6; font-size: 10px; padding: 2px 6px; margin-left: 5px;}

    /* Botones de acción tabla */
    .btn-action { background: none; border: none; cursor: pointer; font-size: 18px; padding: 0 5px; transition: 0.3s; }
    .btn-action:hover { transform: scale(1.2); }
</style>
@endpush

@section('content')
<div class="inventory-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>Gestión de Inventario y Retail</h1>
        <button class="btn-add" onclick="openCreateModal()">+ Nuevo Producto</button>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Productos</div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #ff4757;">
            <div class="stat-label">Stock Bajo (Alertas)</div>
            <div class="stat-value">{{ $stats['stock_bajo'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Valor de Inventario (Costo)</div>
            <div class="stat-value">${{ number_format($stats['valor_inventario'], 2) }}</div>
        </div>
    </div>

    @if(session('success'))
        <div style="background: rgba(46, 213, 115, 0.2); color: #2ed573; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #2ed573;">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div style="background: rgba(255, 71, 87, 0.2); color: #ff4757; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #ff4757;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Sucursal</th>
                    <th>Stock</th>
                    <th>P. Costo</th>
                    <th>P. Venta Público</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $p)
                <tr>
                    <td>
                        <div class="product-info-cell">
                            @if($p->prd_imagen)
                                <img src="{{ asset($p->prd_imagen) }}" alt="img" class="product-img-mini">
                            @else
                                <div class="product-img-placeholder">📦</div>
                            @endif
                            <div>
                                <strong style="font-size: 15px;">{{ $p->prd_nombre }}</strong><br>
                                @if($p->prd_tieneIva)
                                    <span class="badge-iva">IVA 15%</span>
                                @else
                                    <span class="badge-iva" style="color:#aaa; border-color:#555;">0% IVA</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $p->categoria->catp_nombre ?? 'Sin categoría' }}</td>
                    <td><span style="color: var(--accent-primary)">{{ $p->sucursal->suc_nombre ?? 'Central' }}</span></td>
                    <td>
                        <span class="badge {{ $p->prd_stockActual <= $p->prd_stockMinimo ? 'badge-stock' : 'badge-ok' }}">
                            {{ $p->prd_stockActual }} {{ $p->prd_unidadMedida }}
                        </span>
                    </td>
                    <td style="color: #a0a0a0;">${{ number_format($p->prd_precioCompra, 2) }}</td>
                    <td>
                        <strong style="color: #4caf50; font-size: 16px;">${{ number_format($p->prd_precioVenta, 2) }}</strong>
                    </td>
                    <td>
                        <button class="btn-action" 
                                style="color: var(--accent-primary);" 
                                title="Editar"
                                data-id="{{ $p->prd_id }}"
                                data-nombre="{{ $p->prd_nombre }}"
                                data-categoria="{{ $p->prd_categoriaId }}"
                                data-proveedor="{{ $p->prd_proveedorId }}"
                                data-sucursal="{{ $p->prd_sucursalId }}"
                                data-stock="{{ $p->prd_stockActual }}"
                                data-stockmin="{{ $p->prd_stockMinimo }}"
                                data-unidad="{{ $p->prd_unidadMedida }}"
                                data-preciocompra="{{ $p->prd_precioCompra }}"
                                data-precioventa="{{ $p->prd_precioVenta }}"
                                data-tieneiva="{{ $p->prd_tieneIva }}"
                                data-imagen="{{ $p->prd_imagen ? asset($p->prd_imagen) : '' }}"
                                onclick="editProduct(this)">
                            ✏️
                        </button>
                        <button class="btn-action" 
                                style="color: #ff4757;" 
                                title="Eliminar"
                                data-id="{{ $p->prd_id }}"
                                onclick="deleteProduct(this)">
                            🗑️
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal" id="modalProducto">
    <div class="modal-content">
        <h2 id="modalTitle" style="color: var(--accent-primary); margin-bottom: 20px;">Registrar Producto</h2>
        
        <form id="formProducto" action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            <input type="hidden" name="prd_id" id="prd_id">

            <div class="form-grid">
                <div class="form-group full">
                    <label>Imagen del Producto</label>
                    <div class="img-upload-box" onclick="document.getElementById('prd_imagen').click()">
                        <input type="file" name="prd_imagen" id="prd_imagen" style="display: none;" accept="image/*" onchange="previewImage(this)">
                        <span id="upload-text" style="color: #888;">Haz clic para subir una imagen (JPG, PNG)</span>
                        <img id="image-preview" class="preview-img" src="">
                    </div>
                </div>

                <div class="form-group full">
                    <label>Nombre del Producto *</label>
                    <input type="text" name="prd_nombre" id="prd_nombre" class="form-input" required placeholder="Ej. Shampoo Keratina 500ml">
                </div>

                <div class="form-group">
                    <label>Categoría *</label>
                    <select name="prd_categoriaId" id="prd_categoriaId" class="form-input" required>
                        <option value="">Seleccione...</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->catp_id }}">{{ $cat->catp_nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Proveedor *</label>
                    <select name="prd_proveedorId" id="prd_proveedorId" class="form-input" required>
                        <option value="">Seleccione...</option>
                        @foreach($proveedores as $prov)
                            <option value="{{ $prov->prv_id }}">{{ $prov->prv_nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Sucursal Destino *</label>
                    <select name="prd_sucursalId" id="prd_sucursalId" class="form-input" required>
                        <option value="">Seleccione la sucursal...</option>
                        @foreach($sucursales as $suc)
                            <option value="{{ $suc->suc_id }}">{{ $suc->suc_nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
    <label>Unidad de Medida *</label>
    <input type="text" name="prd_unidadMedida" id="prd_unidadMedida" class="form-input" required value="Unidades" readonly style="background-color: #222; color: #888; cursor: not-allowed; border-color: #333;">
</div>

                <div class="form-group">
    <label>Stock Actual *</label>
    <input type="number" name="prd_stockActual" id="prd_stockActual" 
           class="form-input" required min="0" value="0">
</div>

                <div class="form-group">
                    <label>Stock Mínimo (Alerta) *</label>
                    <input type="number" name="prd_stockMinimo" id="prd_stockMinimo" class="form-input" required value="5">
                </div>

                <div class="form-group" style="background: rgba(255, 255, 255, 0.05); padding: 15px; border-radius: 8px;">
                    <label style="color: #a0a0a0;">Costo de Compra ($) *</label>
                    <input type="number" step="0.01" name="prd_precioCompra" id="prd_precioCompra" class="form-input" required placeholder="0.00">
                </div>

                <div class="form-group" style="background: rgba(76, 175, 80, 0.1); padding: 15px; border-radius: 8px; border: 1px dashed #4caf50;">
                    <label style="color: #4caf50;">Precio Venta al Público ($) *</label>
                    <input type="number" step="0.01" name="prd_precioVenta" id="prd_precioVenta" class="form-input" required placeholder="0.00" style="border-color: #4caf50;">
                    
                    <label style="color: white; margin-top: 10px; display: flex; align-items: center; gap: 8px; font-weight: normal; cursor: pointer;">
                        <input type="hidden" name="prd_tieneIva" value="0">
                        <input type="checkbox" name="prd_tieneIva" id="prd_tieneIva" value="1" checked style="transform: scale(1.3); accent-color: #4caf50;"> 
                        Aplica IVA (15% Ecuador)
                    </label>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 25px;">
                <button type="submit" class="btn-add" style="flex: 2;">💾 Guardar Producto</button>
                <button type="button" class="btn-add" onclick="toggleModal(false)" style="flex: 1; background: #444; color: white;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<form id="deleteForm" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    const modal = document.getElementById('modalProducto');
    const form = document.getElementById('formProducto');
    const methodField = document.getElementById('methodField');
    const modalTitle = document.getElementById('modalTitle');

    function toggleModal(show) {
        if(show) modal.classList.add('active');
        else {
            modal.classList.remove('active');
            resetImagePreview();
        }
    }

    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const text = document.getElementById('upload-text');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                text.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function resetImagePreview() {
        document.getElementById('prd_imagen').value = '';
        document.getElementById('image-preview').style.display = 'none';
        document.getElementById('image-preview').src = '';
        document.getElementById('upload-text').style.display = 'block';
    }

    function openCreateModal() {
        form.reset();
        resetImagePreview();
        form.action = "{{ route('productos.store') }}";
        methodField.value = "POST";
        modalTitle.innerText = "Registrar Nuevo Producto";
        document.getElementById('prd_id').value = '';
        document.getElementById('prd_tieneIva').checked = true; // IVA por defecto
        
        document.getElementById('prd_unidadMedida').value = 'Unidades';
        toggleModal(true);
    }

    function editProduct(btn) {
        resetImagePreview();
        const id = btn.dataset.id;
        
        document.getElementById('prd_id').value = id;
        document.getElementById('prd_nombre').value = btn.dataset.nombre;
        document.getElementById('prd_categoriaId').value = btn.dataset.categoria;
        document.getElementById('prd_proveedorId').value = btn.dataset.proveedor;
        document.getElementById('prd_sucursalId').value = btn.dataset.sucursal;
        document.getElementById('prd_stockActual').value = btn.dataset.stock;
        document.getElementById('prd_stockMinimo').value = btn.dataset.stockmin;
        //document.getElementById('prd_unidadMedida').value = btn.dataset.unidad;
        document.getElementById('prd_precioCompra').value = btn.dataset.preciocompra;
        document.getElementById('prd_precioVenta').value = btn.dataset.precioventa;
        
        // Checkbox IVA
        document.getElementById('prd_tieneIva').checked = btn.dataset.tieneiva == "1";

        
        // Mostrar imagen actual si existe
        const imgUrl = btn.dataset.imagen;
        if(imgUrl) {
            document.getElementById('image-preview').src = imgUrl;
            document.getElementById('image-preview').style.display = 'block';
            document.getElementById('upload-text').style.display = 'none';
        }

        form.action = `/admin/productos/${id}`;
        methodField.value = "PUT";
        modalTitle.innerText = "Editar Producto";
        
        toggleModal(true);
    }

    function deleteProduct(btn) {
        const id = btn.dataset.id;
        if(confirm('¿Estás seguro de que deseas eliminar este producto?')) {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/admin/productos/${id}`;
            deleteForm.submit();
        }
    }

    window.onclick = function(event) {
        if (event.target == modal) toggleModal(false);
    }
    document.getElementById('prd_precioVenta').addEventListener('input', function() {
    const compra = parseFloat(document.getElementById('prd_precioCompra').value);
    const venta = parseFloat(this.value);
    
    if (venta < compra) {
        this.style.borderColor = '#ff4757'; // Rojo si hay pérdida
    } else {
        this.style.borderColor = '#4caf50'; // Verde si hay ganancia
    }
});

// 👇👇👇 AGREGA TODO ESTE BLOQUE NUEVO DESDE AQUÍ 👇👇👇
    document.getElementById('formProducto').addEventListener('submit', function(e) {
        const stockActual = parseFloat(document.getElementById('prd_stockActual').value);
        const stockMinimo = parseFloat(document.getElementById('prd_stockMinimo').value);
        const precioCompra = parseFloat(document.getElementById('prd_precioCompra').value);
        const precioVenta = parseFloat(document.getElementById('prd_precioVenta').value);

        let errores = [];

        // Validaciones de lógica de vida real
        if (stockActual < 0) errores.push("El Stock Actual no puede ser negativo.");
        if (stockMinimo < 0) errores.push("El Stock Mínimo no puede ser negativo.");
        if (precioVenta <= precioCompra) {
            errores.push(`El precio de venta ($${precioVenta}) debe dejar ganancia (Costo: $${precioCompra}).`);
        }

        // Si hay errores, detenemos el formulario y mostramos SweetAlert2
        if (errores.length > 0) {
            e.preventDefault(); // Evita que se guarde
            
            Swal.fire({
                icon: 'error',
                title: '¡Datos Inválidos!',
                html: `<ul style="text-align: left; color: #333;">${errores.map(err => `<li style="margin-bottom: 5px;">${err}</li>`).join('')}</ul>`,
                confirmButtonColor: '#fad370', // El amarillo de tu sistema
                confirmButtonText: 'Corregir'
            });
        }
    });
</script>
@endsection