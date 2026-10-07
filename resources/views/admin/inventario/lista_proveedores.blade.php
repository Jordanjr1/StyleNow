@extends('layouts.app')

@section('title', 'Gestión de Proveedores - StyleNow')

@push('styles')
    <style>
        :root {
            --bg-primary: #0a0a0a;
            --bg-secondary: #1a1a1a;
            --bg-card: #2a2a2a;
            --text-primary: #ffffff;
            --text-secondary: #cccccc;
            --text-muted: #888888;
            --accent-primary: #fad370;
            --border-color: rgba(255, 152, 0, 0.3);
            --hover-bg: rgba(255, 225, 107, 0.1);
            --badge-active-bg: rgba(76, 175, 80, 0.2);
            --badge-active-color: #4caf50;
            --badge-inactive-bg: rgba(244, 67, 54, 0.2);
            --badge-inactive-color: #f44336;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--bg-card);
            border: 2px solid var(--border-color);
            border-radius: 15px;
            padding: 25px;
            position: relative;
            transition: all 0.3s;
        }

        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(255, 152, 0, 0.2); }

        .stat-icon { width: 50px; height: 50px; background: var(--hover-bg); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 15px; }

        .stat-label { font-size: 13px; color: var(--text-muted); text-transform: uppercase; }

        .stat-value { font-size: 28px; font-weight: bold; color: var(--text-primary); }

        .toolbar {
            background: var(--bg-card);
            border: 2px solid var(--border-color);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-section {
            background: var(--bg-card);
            border: 2px solid var(--border-color);
            border-radius: 15px;
            padding: 25px;
            overflow-x: auto;
        }

        table { width: 100%; border-collapse: collapse; }
        th { padding: 15px; text-align: left; color: var(--text-muted); border-bottom: 2px solid var(--border-color); font-size: 13px; }
        td { padding: 18px 15px; border-bottom: 1px solid var(--border-color); color: var(--text-secondary); font-size: 14px; }

        .badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-active { background: var(--badge-active-bg); color: var(--badge-active-color); border: 1px solid var(--badge-active-color); }
        .badge-inactive { background: var(--badge-inactive-bg); color: var(--badge-inactive-color); border: 1px solid var(--badge-inactive-color); }

        .action-btn { width: 35px; height: 35px; border-radius: 8px; border: none; cursor: pointer; transition: 0.3s; margin-right: 5px; }
        .btn-add { background: var(--accent-primary); color: #000; padding: 10px 20px; border-radius: 10px; font-weight: bold; border: none; cursor: pointer; }

        /* Estilos del Modal */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 20px; padding: 30px; width: 90%; max-width: 600px; }
        .form-group { margin-bottom: 15px; }
        .form-label { display: block; margin-bottom: 5px; color: var(--text-secondary); }
        .form-input { width: 100%; padding: 10px; background: var(--bg-secondary); border: 1px solid var(--border-color); color: white; border-radius: 8px; }
    </style>
@endpush

@section('topbar-left')
    <h1>🏢 Gestión de Proveedores</h1>
    <p>Panel administrativo de suministros</p>
@endsection

@section('content')
    @if(session('success'))
        <div style="background: var(--badge-active-bg); color: var(--badge-active-color); padding: 15px; border-radius: 10px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-label">Total</div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-label">Activos</div>
            <div class="stat-value">{{ $stats['activos'] }}</div>
        </div>
    </div>

    <div class="toolbar">
        <input type="text" placeholder="Buscar proveedor..." class="form-input" style="max-width: 300px;">
        <button class="btn-add" onclick="openCreateModal()">+ Nuevo Proveedor</button>
    </div>

    <div class="table-section">
        <table>
            <thead>
                <tr>
                    <th>Nombre / Empresa</th>
                    <th>Estado</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Dirección</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proveedores as $prov)
                <tr>
                    <td><strong>{{ $prov->prv_nombre }}</strong></td>
                    <td>
                        <span class="badge {{ $prov->prv_estado == 'A' ? 'badge-active' : 'badge-inactive' }}">
                            {{ $prov->prv_estado == 'A' ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>{{ $prov->prv_telefono }}</td>
                    <td>{{ $prov->prv_email }}</td>
                    <td>{{ $prov->prv_direccion ?? 'N/A' }}</td>
                    <td>
                        <button class="action-btn" 
                                style="background: rgba(33,150,243,0.2); color: #2196f3;"
                                title="Editar"
                                data-id="{{ $prov->prv_id }}"
                                data-nombre="{{ $prov->prv_nombre }}"
                                data-email="{{ $prov->prv_email }}"
                                data-telefono="{{ $prov->prv_telefono }}"
                                data-direccion="{{ $prov->prv_direccion ?? '' }}"
                                data-estado="{{ $prov->prv_estado }}"
                                onclick="editProveedor(this)">
                            ✏️
                        </button>
                        
                        <button class="action-btn" 
                                style="background: rgba(244,67,54,0.2); color: #f44336;"
                                title="Eliminar"
                                data-id="{{ $prov->prv_id }}"
                                onclick="deleteProveedor(this)">
                            🗑️
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 50px;">No hay proveedores registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="modal" id="modalProveedor">
        <div class="modal-content">
            <h2 id="modalTitle" style="margin-bottom: 20px; color: var(--accent-primary);">Registrar Proveedor</h2>
            
            <form id="formProveedor" action="{{ route('proveedores.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodField" value="POST">

                <div class="form-group">
                    <label class="form-label">Nombre de la Empresa</label>
                    <input type="text" name="prv_nombre" id="prv_nombre" class="form-input" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="prv_email" id="prv_email" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="prv_telefono" id="prv_telefono" class="form-input" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="prv_direccion" id="prv_direccion" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Estado</label>
                    <select name="prv_estado" id="prv_estado" class="form-input">
                        <option value="A">Activo</option>
                        <option value="I">Inactivo</option>
                    </select>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="button" class="btn-add" style="background: #444; color: white;" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn-add" style="flex: 1;">Guardar Proveedor</button>
                </div>
            </form>
        </div>
    </div>

    <form id="deleteForm" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        // Referencias al DOM
        const modal = document.getElementById('modalProveedor');
        const form = document.getElementById('formProveedor');
        const methodField = document.getElementById('methodField');
        const modalTitle = document.getElementById('modalTitle');

        // Función para abrir modal en modo CREAR
        function openCreateModal() {
            form.reset(); // Limpia los campos
            form.action = "{{ route('proveedores.store') }}"; // Ruta para guardar nuevo
            methodField.value = "POST"; // Método HTTP
            modalTitle.innerText = "Registrar Proveedor";
            modal.classList.add('active');
        }

        // Función para abrir modal en modo EDITAR (Recibe el botón "this")
        function editProveedor(btn) {
            // Extraemos los datos de los atributos data-* del botón
            const id = btn.getAttribute('data-id');
            const nombre = btn.getAttribute('data-nombre');
            const email = btn.getAttribute('data-email');
            const telefono = btn.getAttribute('data-telefono');
            const direccion = btn.getAttribute('data-direccion');
            const estado = btn.getAttribute('data-estado');

            // Rellenamos el formulario
            document.getElementById('prv_nombre').value = nombre;
            document.getElementById('prv_email').value = email;
            document.getElementById('prv_telefono').value = telefono;
            document.getElementById('prv_direccion').value = direccion;
            document.getElementById('prv_estado').value = estado;

            // Configuramos el formulario para actualizar
            form.action = `/admin/proveedores/${id}`; // Ruta para actualizar
            methodField.value = "PUT"; // Simula método PUT
            modalTitle.innerText = "Editar Proveedor";
            
            modal.classList.add('active');
        }

        // Función para eliminar (Recibe el botón "this")
        function deleteProveedor(btn) {
            const id = btn.getAttribute('data-id');
            
            if(confirm('¿Estás seguro de eliminar este proveedor?')) {
                const deleteForm = document.getElementById('deleteForm');
                deleteForm.action = `/admin/proveedores/${id}`;
                deleteForm.submit();
            }
        }

        // Cerrar modal
        function closeModal() {
            modal.classList.remove('active');
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            if (event.target == modal) closeModal();
        }
    </script>
@endsection