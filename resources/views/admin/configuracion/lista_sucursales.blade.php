@extends('layouts.app')

@section('title', 'Gestión de Sucursales - StyleNow')

@push('styles')
<style>
    /* Conserva aquí todo el CSS que me pasaste anteriormente (root, stats-grid, etc.) */
    :root { --accent-primary: #fad370; --bg-card: #2a2a2a; --text-primary: #ffffff; --border-color: rgba(0, 188, 212, 0.3); }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: var(--bg-card); padding: 20px; border-radius: 15px; border: 1px solid var(--border-color); }
    
    /* Modal */
    .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1000; align-items:center; justify-content:center; }
    .modal.active { display:flex; }
    .modal-content { background:#1a1a1a; padding:30px; border-radius:15px; width:500px; border: 1px solid var(--accent-primary); }
    
    /* Form */
    .form-input, .form-select { width:100%; padding:10px; margin-bottom:15px; background:#333; border:1px solid #444; color:#fff; border-radius:5px; }
    
    /* Tabla */
    table { width:100%; border-collapse:collapse; background: var(--bg-card); color: #fff; }
    th, td { padding:15px; text-align:left; border-bottom:1px solid var(--border-color); }
    
    /* Badges */
    .badge { padding: 4px 8px; border-radius: 6px; font-size: 0.8rem; }
    .badge-active { background: rgba(76, 175, 80, 0.2); color: #4caf50; border: 1px solid #4caf50; }
    .badge-inactive { background: rgba(244, 67, 54, 0.2); color: #f44336; border: 1px solid #f44336; }

    /* Botones de acción */
    .btn-action {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 18px;
        padding: 0 5px;
        transition: 0.3s;
    }
    .btn-action:hover { transform: scale(1.2); }
</style>
@endpush

@section('content')
    @if(session('success')) <div style="color: #4caf50; padding: 10px; background: rgba(76,175,80,0.1); border-radius: 5px; margin-bottom: 20px;">{{ session('success') }}</div> @endif
    @if(session('error')) <div style="color: #f44336; padding: 10px; background: rgba(244,67,54,0.1); border-radius: 5px; margin-bottom: 20px;">{{ session('error') }}</div> @endif

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Sucursales</div>
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-subvalue">{{ $stats['activas'] }} activas</div>
        </div>
    </div>

    <div style="margin-bottom: 20px;">
        <button onclick="openCreateModal()" style="background:var(--accent-primary); padding:10px 20px; border-radius:10px; cursor:pointer; border:none; color:black; font-weight:bold;">
            ➕ Nueva Sucursal
        </button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Sucursal</th>
                <th>Dirección</th>
                <th>Contacto</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sucursales as $suc)
            <tr>
                <td>{{ $suc->suc_id }}</td>
                <td><strong>{{ $suc->suc_nombre }}</strong></td>
                <td>{{ $suc->suc_direccion }}</td>
                <td>{{ $suc->suc_telefono }}<br><small>{{ $suc->suc_email }}</small></td>
                <td>
                    <span class="badge {{ $suc->suc_estado == '1' ? 'badge-active' : 'badge-inactive' }}">
                        {{ $suc->suc_estado == '1' ? 'Activa' : 'Inactiva' }}
                    </span>
                </td>
                <td>
                    <button class="btn-action" 
                            style="color: var(--accent-primary);"
                            title="Editar"
                            data-id="{{ $suc->suc_id }}"
                            data-nombre="{{ $suc->suc_nombre }}"
                            data-direccion="{{ $suc->suc_direccion }}"
                            data-telefono="{{ $suc->suc_telefono }}"
                            data-email="{{ $suc->suc_email }}"
                            data-estado="{{ $suc->suc_estado }}"
                            onclick="editSucursal(this)">
                        ✏️
                    </button>
                    <button class="btn-action" 
                            style="color: #f44336;"
                            title="Eliminar"
                            data-id="{{ $suc->suc_id }}"
                            onclick="deleteSucursal(this)">
                        🗑️
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="modal" id="modalSucursal">
        <div class="modal-content">
            <h2 id="modalTitle" style="color:var(--accent-primary); margin-bottom: 20px;">Nueva Sucursal</h2>
            
            <form id="formSucursal" action="{{ route('sucursales.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodField" value="POST">

                <label>Nombre</label>
                <input type="text" name="suc_nombre" id="suc_nombre" class="form-input" required>
                
                <label>Dirección</label>
                <input type="text" name="suc_direccion" id="suc_direccion" class="form-input" required>
                
                <label>Teléfono</label>
                <input type="text" name="suc_telefono" id="suc_telefono" class="form-input">
                
                <label>Email</label>
                <input type="email" name="suc_email" id="suc_email" class="form-input">

                <label>Estado</label>
                <select name="suc_estado" id="suc_estado" class="form-select">
                    <option value="1">Activa</option>
                    <option value="0">Inactiva</option>
                </select>

                <div style="display:flex; gap:10px; margin-top: 15px;">
                    <button type="submit" style="background:var(--accent-primary); border:none; padding:10px; flex:1; border-radius:5px; cursor:pointer; font-weight:bold;">Guardar</button>
                    <button type="button" onclick="closeModal()" style="background:#444; color:#fff; border:none; padding:10px; flex:1; border-radius:5px; cursor:pointer;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <form id="deleteForm" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        const modal = document.getElementById('modalSucursal');
        const form = document.getElementById('formSucursal');
        const methodField = document.getElementById('methodField');
        const modalTitle = document.getElementById('modalTitle');

        function openCreateModal() {
            form.reset();
            form.action = "{{ route('sucursales.store') }}";
            methodField.value = "POST";
            modalTitle.innerText = "Nueva Sucursal";
            // Valor por defecto para nueva sucursal
            document.getElementById('suc_estado').value = "1"; 
            modal.classList.add('active');
        }

        // Función corregida: lee los data-attributes del botón
        function editSucursal(btn) {
            // Leer datos desde data-attributes
            const id = btn.getAttribute('data-id');
            const nombre = btn.getAttribute('data-nombre');
            const direccion = btn.getAttribute('data-direccion');
            const telefono = btn.getAttribute('data-telefono');
            const email = btn.getAttribute('data-email');
            const estado = btn.getAttribute('data-estado');

            // Rellenar formulario
            document.getElementById('suc_nombre').value = nombre;
            document.getElementById('suc_direccion').value = direccion;
            document.getElementById('suc_telefono').value = telefono;
            document.getElementById('suc_email').value = email;
            document.getElementById('suc_estado').value = estado;

            // Configurar formulario para actualizar
            form.action = `/admin/sucursales/${id}`;
            methodField.value = "PUT";
            modalTitle.innerText = "Editar Sucursal";
            
            modal.classList.add('active');
        }

        // Función corregida: lee el data-id del botón
        function deleteSucursal(btn) {
            const id = btn.getAttribute('data-id');
            
            if(confirm('¿Estás seguro de eliminar esta sucursal?')) {
                const deleteForm = document.getElementById('deleteForm');
                deleteForm.action = `/admin/sucursales/${id}`;
                deleteForm.submit();
            }
        }

        function closeModal() {
            modal.classList.remove('active');
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            if (event.target == modal) closeModal();
        }
    </script>
@endsection