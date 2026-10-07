@extends('layouts.app')

@section('title', 'Recordatorios - StyleNow')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    /* VARIABLES (Tema Dark/Gold) */
    :root {
        --bg-primary: #0a0a0a; --bg-secondary: #1a1a1a; --bg-card: #2a2a2a;
        --text-primary: #ffffff; --text-secondary: #cccccc; --text-muted: #aeaeae;
        --accent-primary: #fad370; --accent-secondary: #eec95c;
        --border-color: rgba(250, 211, 112, 0.2); --hover-bg: rgba(250, 211, 112, 0.05);
        --badge-sent-bg: rgba(76, 175, 80, 0.2); --badge-sent-color: #4caf50;
        --badge-pending-bg: rgba(255, 193, 7, 0.2); --badge-pending-color: #ffc107;
    }

    /* ESTADÍSTICAS */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 15px; padding: 25px; position: relative; overflow: hidden; transition: 0.3s; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.5); }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary)); }
    .stat-icon { width: 50px; height: 50px; background: var(--hover-bg); color: var(--accent-primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 15px; }
    .stat-label { font-size: 13px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; font-weight: 600; }
    .stat-value { font-size: 28px; font-weight: bold; color: var(--text-primary); }

    /* TOOLBAR */
    .toolbar { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 15px; padding: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
    .view-all-btn { padding: 12px 20px; background: var(--accent-primary); border: none; border-radius: 10px; color: #000; cursor: pointer; font-weight: bold; transition: 0.3s; }
    .view-all-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(250, 211, 112, 0.3); }

    /* TABLA */
    .table-section { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 15px; padding: 25px; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 15px; text-align: left; font-size: 13px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; border-bottom: 2px solid var(--border-color); }
    td { padding: 18px 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; color: var(--text-secondary); }
    tbody tr:hover { background: var(--hover-bg); }
    
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; border: 1px solid; }
    .badge-sent { background: var(--badge-sent-bg); color: var(--badge-sent-color); border-color: var(--badge-sent-color); }
    .badge-pending { background: var(--badge-pending-bg); color: var(--badge-pending-color); border-color: var(--badge-pending-color); }

    /* BOTONES ACCIÓN */
    .action-buttons { display: flex; gap: 8px; }
    .action-btn { width: 35px; height: 35px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; transition: 0.3s; }
    .btn-view { background: rgba(250, 211, 112, 0.1); color: var(--accent-primary); }
    .btn-edit { background: rgba(33, 150, 243, 0.1); color: #2196f3; }
    .btn-delete { background: rgba(244, 67, 54, 0.1); color: #f44336; }
    .action-btn:hover { transform: scale(1.1); filter: brightness(1.2); }

    /* MODAL */
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; }
    .modal.active { display: flex; animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .modal-content { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 20px; padding: 30px; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; position: relative; }
    .modal-header { display: flex; justify-content: space-between; margin-bottom: 25px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; }
    .modal-title { font-size: 20px; font-weight: bold; color: var(--text-primary); }
    .close-btn { font-size: 28px; cursor: pointer; color: var(--text-muted); transition: 0.3s; line-height: 1; }
    .close-btn:hover { color: var(--danger); }
    
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; margin-bottom: 8px; color: var(--text-secondary); font-size: 13px; font-weight: 600; }
    .form-input, .form-select, .form-textarea { width: 100%; padding: 12px 15px; background: var(--bg-secondary); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: white; font-size: 14px; transition: 0.3s; }
    .form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: var(--accent-primary); }
    .btn-save { width: 100%; padding: 14px; background: var(--accent-primary); border: none; border-radius: 10px; color: #000; font-weight: bold; cursor: pointer; margin-top: 10px; transition: 0.3s; }
    .btn-save:hover { background: var(--accent-hover); }

    /* DETALLES EN MODAL DE VISTA */
    .detail-row { border-bottom: 1px solid rgba(255,255,255,0.05); padding: 15px 0; }
    .detail-label { color: var(--text-muted); font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
    .detail-value { color: var(--text-primary); font-size: 15px; }
    .detail-message { background: var(--bg-secondary); padding: 15px; border-radius: 8px; border: 1px solid rgba(250, 211, 112, 0.2); color: var(--accent-primary); font-style: italic; margin-top: 10px; line-height: 1.5; }
</style>
@endpush

@section('topbar-left')
    <h1>🔔 Gestión de Recordatorios</h1>
    <p>Administra las notificaciones automáticas y manuales de clientes</p>
@endsection

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📨</div>
            <div class="stat-label">Total Enviados</div>
            <div class="stat-value">{{ $recordatorios->where('rec_enviado', 1)->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-label">Pendientes de Envío</div>
            <div class="stat-value">{{ $recordatorios->where('rec_enviado', 0)->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📅</div>
            <div class="stat-label">Total Programados</div>
            <div class="stat-value">{{ $recordatorios->count() }}</div>
        </div>
    </div>

    <div class="toolbar">
        <div style="color: var(--text-muted); font-weight: 600;">📋 Listado de notificaciones del sistema</div>
        <button class="view-all-btn" onclick="openModal()">+ Crear Recordatorio</button>
    </div>

    <div class="table-section">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Destinatario</th>
                    <th>Mensaje</th>
                    <th>Relacionado a</th>
                    <th>Fecha Envío</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recordatorios as $rec)
                    <tr>
                        <td>#{{ $rec->rec_id }}</td>
                        <td>{{ $rec->rec_tipo }}</td>
                        <td>{{ $rec->rec_destinatario }}</td>
                        <td>{{ Str::limit($rec->rec_mensaje, 30) }}</td>
                        <td>
                            @if($rec->rec_citaId)
                                Cita: {{ $rec->usr_nombre ?? 'Cliente' }}
                            @else
                                Aviso General
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($rec->rec_fechaEnvio)->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($rec->rec_enviado == 1)
                                <span class="badge badge-sent">Enviado</span>
                            @else
                                <span class="badge badge-pending">Pendiente</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn btn-view" 
                                        data-id="{{ $rec->rec_id }}"
                                        onclick="viewReminder(this)" 
                                        title="Ver Detalles">👁️</button>
                                
                                @if($rec->rec_enviado == 0)
                                    <button class="action-btn btn-edit" 
                                            data-id="{{ $rec->rec_id }}"
                                            onclick="editReminder(this)" 
                                            title="Editar">✏️</button>
                                @endif
                                
                                <button class="action-btn btn-delete" 
                                        data-id="{{ $rec->rec_id }}"
                                        onclick="deleteReminder(this)" 
                                        title="Eliminar">🗑️</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center; padding:40px; color: var(--text-muted);">No hay recordatorios registrados en el sistema.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="modal" id="reminderModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Nuevo Recordatorio</h2>
                <span class="close-btn" onclick="closeModal()">&times;</span>
            </div>
            <form id="reminderForm">
                <input type="hidden" id="rec_id" name="rec_id">

                <div class="form-group">
                    <label class="form-label">Tipo de Aviso</label>
                    <select class="form-select" name="rec_tipo" id="recTipo">
                        <option value="Email">Email</option>
                        <option value="WhatsApp">WhatsApp</option>
                        <option value="SMS">SMS</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Vincular a Cita (Opcional)</label>
                    <select class="form-select" name="rec_citaId" id="citaSelect">
                        <option value="">-- Aviso General (Sin vinculación) --</option>
                        @foreach($proximasCitas as $cita)
                            <option value="{{ $cita->cit_id }}">
                                Cita #{{ $cita->cit_id }} - {{ $cita->usr_nombre }} {{ $cita->usr_apellido }} ({{ \Carbon\Carbon::parse($cita->cit_fechaCita)->format('d/m/Y H:i') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Destinatario (Email o Teléfono)</label>
                    <input type="text" class="form-input" name="rec_destinatario" id="destinatario" required placeholder="ej: cliente@email.com o 0999999999">
                </div>

                <div class="form-group">
                    <label class="form-label">Fecha y Hora Programada de Envío</label>
                    <input type="datetime-local" class="form-input" name="rec_fechaEnvio" id="fechaEnvio" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Contenido del Mensaje</label>
                    <textarea class="form-textarea" name="rec_mensaje" id="mensaje" rows="4" required placeholder="Escribe el mensaje que recibirá el cliente..."></textarea>
                </div>

                <button type="submit" class="btn-save" id="btnSubmitForm">Guardar Programación</button>
            </form>
        </div>
    </div>

    <div class="modal" id="viewModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Detalles del Recordatorio</h2>
                <span class="close-btn" onclick="closeViewModal()">&times;</span>
            </div>
            <div id="viewContent"></div>
            <div style="margin-top: 25px; text-align: right;">
                <button type="button" class="btn-save" style="width: auto; padding: 10px 25px; background: var(--bg-secondary); color: var(--text-primary);" onclick="closeViewModal()">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- Datos del servidor para JavaScript -->
    <script type="application/json" id="recordatoriosData">
        @json($recordatorios)
    </script>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        // Leer datos desde el elemento JSON
        const allRecordatorios = JSON.parse(document.getElementById('recordatoriosData').textContent);
        
        let editMode = false;
        let currentEditId = null;

        // --- MANEJO BÁSICO DE MODALES ---
        function openModal() { 
            editMode = false;
            currentEditId = null;
            document.getElementById('reminderForm').reset();
            document.getElementById('rec_id').value = '';
            document.getElementById('modalTitle').innerText = 'Nuevo Recordatorio';
            document.getElementById('btnSubmitForm').innerText = 'Programar Envío';
            document.getElementById('reminderModal').classList.add('active'); 
        }
        
        function closeModal() { document.getElementById('reminderModal').classList.remove('active'); }
        function closeViewModal() { document.getElementById('viewModal').classList.remove('active'); }

        // Utilidad: Formatear fecha para el input datetime-local
        function formatForInput(dateString) {
            if (!dateString) return '';
            return dateString.replace(' ', 'T').substring(0, 16);
        }

        // Utilidad: Formatear fecha para lectura humana
        function formatFriendlyDate(dateString) {
            if (!dateString) return 'No definida';
            const d = new Date(dateString.replace(/-/g, '/'));
            return d.toLocaleString('es-ES', { dateStyle: 'medium', timeStyle: 'short' });
        }

        // --- ACCIÓN 1: VISUALIZAR ---
        function viewReminder(btn) {
            const id = parseInt(btn.getAttribute('data-id'));
            const rec = allRecordatorios.find(r => r.rec_id === id);
            if(!rec) return;

            const estadoHTML = rec.rec_enviado == 1 
                ? '<span class="badge badge-sent">Enviado Exitosamente</span>' 
                : '<span class="badge badge-pending">Pendiente en Cola</span>';

            const citaVinculada = rec.rec_citaId 
                ? `Cita #${rec.rec_citaId} (Cliente: ${rec.usr_nombre || ''} ${rec.usr_apellido || ''})` 
                : 'Aviso General / Campaña';

            document.getElementById('viewContent').innerHTML = `
                <div class="detail-row">
                    <div class="detail-label">Estado Actual</div>
                    <div class="detail-value">${estadoHTML}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Vía de Contacto y Destinatario</div>
                    <div class="detail-value"><strong>${rec.rec_tipo}</strong> ➔ ${rec.rec_destinatario}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Programación de Envío</div>
                    <div class="detail-value">${formatFriendlyDate(rec.rec_fechaEnvio)}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Relación del Sistema</div>
                    <div class="detail-value">${citaVinculada}</div>
                </div>
                <div class="detail-row" style="border:none;">
                    <div class="detail-label">Mensaje a Enviar</div>
                    <div class="detail-message">"${rec.rec_mensaje}"</div>
                </div>
            `;
            document.getElementById('viewModal').classList.add('active');
        }

        // --- ACCIÓN 2: EDITAR ---
        function editReminder(btn) {
            const id = parseInt(btn.getAttribute('data-id'));
            const rec = allRecordatorios.find(r => r.rec_id === id);
            if(!rec) return;

            editMode = true;
            currentEditId = rec.rec_id;
            
            document.getElementById('modalTitle').innerText = 'Editar Recordatorio #' + rec.rec_id;
            document.getElementById('btnSubmitForm').innerText = 'Actualizar Recordatorio';
            
            // Cargar datos en el formulario
            document.getElementById('rec_id').value = rec.rec_id;
            document.getElementById('recTipo').value = rec.rec_tipo;
            document.getElementById('citaSelect').value = rec.rec_citaId || '';
            document.getElementById('destinatario').value = rec.rec_destinatario;
            document.getElementById('mensaje').value = rec.rec_mensaje;
            document.getElementById('fechaEnvio').value = formatForInput(rec.rec_fechaEnvio);

            document.getElementById('reminderModal').classList.add('active');
        }

        // --- ACCIÓN 3: GUARDAR (CREAR O ACTUALIZAR) ---
        document.getElementById('reminderForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(e.target));
            
            // Determinar URL y Método según el modo
            const url = editMode ? `/admin/recordatorios/${currentEditId}` : "{{ route('recordatorios.store') }}";
            const method = editMode ? 'PUT' : 'POST';

            // Feedback de carga
            const btnSubmit = document.getElementById('btnSubmitForm');
            const originalText = btnSubmit.innerText;
            btnSubmit.innerText = 'Procesando...';
            btnSubmit.disabled = true;

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });
                
                if(res.ok) { 
                    alert(editMode ? 'Recordatorio actualizado correctamente.' : 'Recordatorio programado con éxito.'); 
                    location.reload(); 
                } else { 
                    const err = await res.json();
                    alert('Ocurrió un error: ' + (err.message || 'Verifica los datos del formulario.')); 
                }
            } catch(err) { 
                console.error(err); 
                alert('Error de conexión al servidor.'); 
            } finally {
                btnSubmit.innerText = originalText;
                btnSubmit.disabled = false;
            }
        });

        // --- ACCIÓN 4: ELIMINAR ---
        async function deleteReminder(btn) {
            const id = parseInt(btn.getAttribute('data-id'));
            
            if(!confirm('¿Estás seguro de que deseas eliminar este recordatorio permanentemente?')) return;
            
            try {
                const res = await fetch(`/admin/recordatorios/${id}`, {
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': csrfToken}
                });
                
                if(res.ok) {
                    location.reload();
                } else {
                    alert('Hubo un error al intentar eliminar el registro.');
                }
            } catch(err) { 
                console.error(err); 
                alert('Error de conexión.');
            }
        }
    </script>
@endsection