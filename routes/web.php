<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\CitaController;
use App\Http\Controllers\ClienteController; 
use App\Http\Controllers\Admin\ProductoController;  
use App\Http\Controllers\Admin\SucursalController;
use App\Http\Controllers\Admin\ProveedorController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\ServicioController;
use App\Http\Controllers\Admin\PromocionController;
use App\Http\Controllers\Admin\RecordatorioController;
use App\Http\Controllers\Admin\ReporteController; // <-- Agregado para los reportes
use App\Http\Controllers\Empleado\EmpleadoController;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;


// ============================================
// RUTAS PÚBLICAS (sin autenticación)
// ============================================
Route::get('/', [InicioController::class, 'index'])->name('inicio');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/registro', [AuthController::class, 'showRegisterForm'])->name('registro');
// Ruta para confirmar cita desde el correo
Route::get('/cita/confirmar/{id}', [App\Http\Controllers\Cliente\ClienteController::class, 'confirmarCitaEmail'])->name('cita.confirmar.email');

// Procesar formularios de autenticación
Route::post('/login', [AuthController::class, 'login']);
Route::post('/registro', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/verificar-disponibilidad', [AuthController::class, 'verificarDisponibilidad'])->name('verificar.disponibilidad');
Route::post('/recuperar-contrasena', [AuthController::class, 'enviarRecuperacion'])->name('recuperar.contrasena');

// Rutas para cambio de contraseña obligatorio
Route::middleware(['auth'])->group(function () {
    Route::get('/cambio-contrasena', [AuthController::class, 'mostrarCambio'])->name('cambio.contrasena');
    Route::post('/cambio-contrasena', [AuthController::class, 'cambiar'])->name('cambiar.contrasena');
});

// ============================================
// RUTAS PROTEGIDAS - SOLO ADMINISTRADOR (rol 1)
// ============================================
Route::middleware(['auth', CheckRole::class.':1'])->group(function () {
    // Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Gestión de usuarios
    Route::get('/admin/usuarios', [AdminController::class, 'listarUsuarios'])->name('usuarios.lista');

    // ============================================
    // SECCIÓN CONFIGURACIÓN
    // ============================================
    // Sucursales
    Route::get('/admin/sucursales', [SucursalController::class, 'index'])->name('sucursales.index');
    Route::post('/admin/sucursales', [SucursalController::class, 'store'])->name('sucursales.store');
    Route::put('/admin/sucursales/{id}', [SucursalController::class, 'update'])->name('sucursales.update');
    Route::delete('/admin/sucursales/{id}', [SucursalController::class, 'destroy'])->name('sucursales.destroy');
    
    // Servicios
    Route::get('/admin/servicios', [AdminController::class, 'listarServicios'])->name('configuracion.servicios');
    Route::post('/admin/servicios', [ServicioController::class, 'store'])->name('servicios.store');
    Route::put('/admin/servicios/{id}', [ServicioController::class, 'update'])->name('servicios.update');
    Route::delete('/admin/servicios/{id}', [ServicioController::class, 'destroy'])->name('servicios.destroy');
    
    // Promociones
    Route::get('/admin/promociones', [PromocionController::class, 'index'])->name('configuracion.promociones');
    Route::post('/admin/promociones', [PromocionController::class, 'store'])->name('promociones.store');
    Route::put('/admin/promociones/{id}', [PromocionController::class, 'update'])->name('promociones.update');
    Route::delete('/admin/promociones/{id}', [PromocionController::class, 'destroy'])->name('promociones.destroy');
    Route::patch('/admin/promociones/{id}/toggle', [PromocionController::class, 'toggleStatus'])->name('promociones.toggle');
    
    // Categorías
    Route::get('/admin/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
    Route::post('/admin/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
    Route::get('/admin/categorias/delete/{tipo}/{id}', [CategoriaController::class, 'destroy'])->name('categorias.delete');

    // Recordatorios
    Route::get('/admin/recordatorios', [RecordatorioController::class, 'index'])->name('recordatorios.lista');
    Route::post('/admin/recordatorios', [RecordatorioController::class, 'store'])->name('recordatorios.store');
    Route::delete('/admin/recordatorios/{id}', [RecordatorioController::class, 'destroy'])->name('recordatorios.destroy');
    Route::put('/admin/recordatorios/{id}', [RecordatorioController::class, 'update'])->name('recordatorios.update');

    // ============================================
    // SECCIÓN RESERVAS
    // ============================================
    Route::get('/admin/citas', [CitaController::class, 'index'])->name('reservas.lista');
    Route::get('/admin/citas/productos', [CitaController::class, 'getProductos']);
    Route::post('/admin/citas/{id}/checkout', [CitaController::class, 'checkout']);

    Route::get('/admin/citas/{id}/factura', [App\Http\Controllers\Admin\CitaController::class, 'verFactura'])->name('admin.factura');
    Route::post('/admin/citas/ejecutar-marketing', [App\Http\Controllers\Admin\CitaController::class, 'enviarRecordatoriosRetorno']);
    // ============================================
    // SECCIÓN INVENTARIO
    // ============================================
    // Productos
    Route::get('/admin/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::post('/admin/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::put('/admin/productos/{id}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/admin/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    
    // Proveedores
    Route::get('/admin/proveedores', [ProveedorController::class, 'index'])->name('inventario.proveedores');
    Route::post('/admin/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
    Route::put('/admin/proveedores/{id}', [ProveedorController::class, 'update'])->name('proveedores.update');
    Route::delete('/admin/proveedores/{id}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');

    // ============================================
    // SECCIÓN REPORTES
    // ============================================
    // Reportes Financieros
    Route::get('/admin/reportes/financieros', [ReporteController::class, 'index'])->name('reportes.financieros');
    Route::post('/admin/reportes/financieros/data', [ReporteController::class, 'getReportData']);

    // Reportes de Inventario
    Route::get('/admin/reportes/inventario', [ReporteController::class, 'inventario'])->name('reportes.inventario');
    Route::post('/admin/reportes/inventario/data', [ReporteController::class, 'getInventoryData']);

    // Reportes de Productividad
    Route::get('/admin/reportes/productividad', [AdminController::class, 'reporteProductividad'])->name('reportes.productividad');

    // Editar perfil admin
    Route::get('/admin/perfil', [AdminController::class, 'editarPerfil'])->name('admin.perfil');
    Route::put('/admin/perfil/update', [AdminController::class, 'updatePerfil'])->name('admin.perfil.update');
    Route::put('/admin/perfil/password', [AdminController::class, 'updatePassword'])->name('admin.perfil.password');


    // ============================================
    // SECCIÓN INVENTARIO Y NOTIFICACIONES (ADMIN)
    // ============================================
    
    // Ruta para la Campana (Global)
    Route::get('/admin/notificaciones', [App\Http\Controllers\AdminController::class, 'getNotificaciones']);

    // Rutas de Solicitudes de Inventario
    Route::get('/admin/inventario/solicitudes', [App\Http\Controllers\Admin\ProductoController::class, 'solicitudes'])->name('admin.inventario.solicitudes');
    Route::post('/admin/inventario/solicitudes/{id}/atender', [App\Http\Controllers\Admin\ProductoController::class, 'atenderSolicitud']);


});

// ============================================
// PREFIJOS DE ADMINISTRACIÓN (USUARIOS Y CITAS)
// ============================================
Route::prefix('admin/usuarios')->middleware(['auth', CheckRole::class.':1'])->group(function () {
    // ⚠️ RUTAS DE BÚSQUEDA DEBEN IR PRIMERO
    Route::get('/sucursales', [UsuarioController::class, 'getSucursales'])->name('admin.usuarios.sucursales');
    Route::get('/categorias', [UsuarioController::class, 'getCategorias'])->name('admin.usuarios.categorias');
    
    Route::get('/', [UsuarioController::class, 'listarUsuarios'])->name('admin.usuarios.lista');
    Route::get('/data', [UsuarioController::class, 'index'])->name('usuarios.data');
    Route::post('/', [UsuarioController::class, 'store'])->name('usuarios.store');
    
    // Rutas con variables {id} van al final
    Route::put('/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
    
    // 👇 AQUÍ ESTÁ EL CAMBIO: Ahora apunta al AdminController que tiene la función del borrado lógico
    Route::delete('/{id}', [AdminController::class, 'destroyUsuario'])->name('usuarios.destroy');
    // 👆 ==============================================================================================

    Route::post('/{id}/reset-password', [UsuarioController::class, 'resetPassword'])->name('usuarios.reset-password');
});

Route::prefix('admin/citas')->middleware(['auth', CheckRole::class.':1'])->group(function () {
    Route::get('/promociones-activas', [CitaController::class, 'getPromocionesActivas'])->name('citas.promociones');
    
    Route::get('/', [CitaController::class, 'index'])->name('citas.lista');
    Route::get('/data', [CitaController::class, 'data'])->name('citas.data');
    Route::post('/', [CitaController::class, 'store'])->name('citas.store');
    Route::put('/{id}', [CitaController::class, 'update'])->name('citas.update');
    Route::delete('/{id}', [CitaController::class, 'destroy'])->name('citas.destroy');
    Route::post('/{id}/checkin', [CitaController::class, 'checkin']);
    
    // API ENDPOINTS PARA CATALOGOS
    Route::get('/clientes', [CitaController::class, 'getClientes'])->name('citas.clientes');
    Route::get('/empleados', [CitaController::class, 'getEmpleados'])->name('citas.empleados');
    Route::get('/servicios', [CitaController::class, 'getServicios'])->name('citas.servicios');
    Route::get('/sucursales', [CitaController::class, 'getSucursales'])->name('citas.sucursales');
});
// ============================================
// RUTAS PROTEGIDAS - SOLO EMPLEADO (rol 2)
// ============================================
Route::middleware(['auth', CheckRole::class.':2'])->group(function () {
    
    // ✅ CÁMBIALO POR ESTO:
Route::get('/empleado/dashboard', [App\Http\Controllers\Empleado\EmpleadoController::class, 'dashboard'])->name('empleado.dashboard');
    
    Route::get('/empleado/Citas_pendients', [EmpleadoController::class, 'citasPendientes'])->name('empleado.citas.pendientes');
    Route::post('/empleado/citas/{id}/estado', [App\Http\Controllers\Empleado\EmpleadoController::class, 'actualizarEstadoCita']);
    Route::get('/empleado/Citas_atendidas', [EmpleadoController::class, 'citasAtendidas'])->name('empleado.citas.atendidas');
    Route::get('/empleado/historial', [EmpleadoController::class, 'historial'])->name('empleado.historial');
    
    
    // ESTO ESTÁ BIEN:
    Route::get('/empleado/comisiones', [App\Http\Controllers\Empleado\EmpleadoController::class, 'comisiones'])->name('empleado.comisiones');
    
   Route::get('/empleado/calificaciones', [App\Http\Controllers\Empleado\EmpleadoController::class, 'calificaciones'])->name('empleado.calificaciones');
    Route::post('/empleado/calificaciones/{id}/responder', [App\Http\Controllers\Empleado\EmpleadoController::class, 'responderCalificacion']);
    
    // RUTAS DE PERFIL (EMPLEADO)
    Route::get('/empleado/perfil', [App\Http\Controllers\Empleado\EmpleadoController::class, 'perfil'])->name('empleado.perfil');
    Route::post('/empleado/perfil/actualizar', [App\Http\Controllers\Empleado\EmpleadoController::class, 'actualizarPerfil']);
    Route::post('/empleado/perfil/password', [App\Http\Controllers\Empleado\EmpleadoController::class, 'cambiarPassword']);

    // RUTAS DE INVENTARIO Y VENTAS (EMPLEADO)
    Route::get('/empleado/reportar-falta', [App\Http\Controllers\Empleado\EmpleadoController::class, 'reportarFalta'])->name('empleado.reportar.falta');
    Route::post('/empleado/reportar-falta', [App\Http\Controllers\Empleado\EmpleadoController::class, 'guardarReporteFalta']);
    
    Route::get('/empleado/ventas', [App\Http\Controllers\Empleado\EmpleadoController::class, 'ventas'])->name('empleado.ventas');
    Route::post('/empleado/ventas', [App\Http\Controllers\Empleado\EmpleadoController::class, 'registrarVenta']);

});

// ============================================
// RUTAS PROTEGIDAS - SOLO CLIENTE (rol 3)
// ============================================
Route::middleware(['auth', CheckRole::class.':3'])->group(function () {
    // Dashboard y navegación principal
    Route::get('/cliente/dashboard', [ClienteController::class, 'dashboard'])->name('cliente.dashboard');
    Route::get('/cliente/reservas', [ClienteController::class, 'reservas'])->name('cliente.reservas');
    Route::get('/cliente/nueva-cita', [ClienteController::class, 'nuevaCita'])->name('cliente.nueva-cita');
    Route::get('/cliente/mis-citas', [ClienteController::class, 'misCitas'])->name('cliente.mis-citas');
    Route::get('/cliente/historial', [ClienteController::class, 'historial'])->name('cliente.historial');
    Route::get('/cliente/beneficios', [ClienteController::class, 'beneficios'])->name('cliente.beneficios');
    Route::get('/cliente/mis-puntos', [ClienteController::class, 'misPuntos'])->name('cliente.mis-puntos');
    Route::get('/cliente/promociones', [ClienteController::class, 'promociones'])->name('cliente.promociones');
    Route::get('/cliente/cuenta', [ClienteController::class, 'cuenta'])->name('cliente.cuenta');
    Route::get('/cliente/mi-perfil', [ClienteController::class, 'miPerfil'])->name('cliente.mi-perfil');
    Route::get('/cliente/notificaciones', [ClienteController::class, 'notificaciones'])->name('cliente.notificaciones');


    // =========================================================
    // RUTAS DEL CLIENTE
    // =========================================================

    Route::get('/cliente/dashboard', [App\Http\Controllers\Cliente\ClienteController::class, 'dashboard'])->name('cliente.dashboard');
    Route::get('/cliente/agendar', [App\Http\Controllers\Cliente\ClienteController::class, 'agendar'])->name('cliente.agendar');
    
    // Rutas AJAX para el Wizard de reserva
    Route::post('/cliente/agendar/horas-disponibles', [App\Http\Controllers\Cliente\ClienteController::class, 'horasDisponibles']);
    Route::post('/cliente/agendar/guardar', [App\Http\Controllers\Cliente\ClienteController::class, 'guardarCita']);

    // Validar código promocional en el Wizard
    Route::post('/cliente/agendar/validar-promo', [App\Http\Controllers\Cliente\ClienteController::class, 'validarPromo']);
    // ✅ DÉJALO ASÍ (Apunta la URL al controlador):
    Route::get('/cliente/citas/nueva', [App\Http\Controllers\Cliente\ClienteController::class, 'agendar'])->name('cliente.agendar.citas');
   
    
    // Rutas de visualización (Asegúrate de tener esta apuntando al controlador)
    Route::get('/cliente/mis-citas', [App\Http\Controllers\Cliente\ClienteController::class, 'misCitas'])->name('cliente.mis-citas');
    
    // Ruta AJAX para que el cliente pueda cancelar su propia cita
    Route::post('/cliente/citas/{id}/cancelar', [App\Http\Controllers\Cliente\ClienteController::class, 'cancelarCita']);

    // Ruta para guardar la calificación y comentario
Route::post('/cliente/citas/{id}/calificar', [App\Http\Controllers\Cliente\ClienteController::class, 'calificarCita']);

    // Ruta para leer notificaciones y redirigir
    Route::get('/notificaciones/{id}/leer', function($id) {
    /** @var Usuario $user */
    $user = Auth::user();
    $notificacion = $user->notifications()->find($id);
    if($notificacion) {
        $notificacion->markAsRead();
        return redirect($notificacion->data['url']);
    }
    return back();
});

    Route::get('/cliente/historial', [App\Http\Controllers\Cliente\ClienteController::class, 'historial'])->name('cliente.historial');
    
    // Rutas de Puntos de Fidelización
    Route::get('/cliente/puntos', [App\Http\Controllers\Cliente\ClienteController::class, 'puntosFidelizacion'])->name('cliente.puntos.fidelizacion');
    Route::post('/cliente/puntos/canjear', [App\Http\Controllers\Cliente\ClienteController::class, 'canjearPremio']);
    
    
    // Ruta de Promociones
    Route::get('/cliente/promo-vista', [App\Http\Controllers\Cliente\ClienteController::class, 'promociones'])->name('cliente.vista.promociones');
    
    // Rutas del Perfil del Cliente
    Route::get('/cliente/perfil-vista', [App\Http\Controllers\Cliente\ClienteController::class, 'perfil'])->name('cliente.perfil.vista');
    Route::post('/cliente/perfil/actualizar', [App\Http\Controllers\Cliente\ClienteController::class, 'actualizarPerfil']);
    Route::post('/cliente/perfil/password', [App\Http\Controllers\Cliente\ClienteController::class, 'cambiarPassword']);
});