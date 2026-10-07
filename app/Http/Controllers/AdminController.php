<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Sucursal;
use App\Models\CategoriaServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon; // <--- IMPRESCINDIBLE PARA LAS FECHAS

class AdminController extends Controller
{
    /**
     * Mostrar dashboard del administrador CON DATOS REALES
     */
    public function dashboard()
    {
        // --- 1. ESTADÍSTICAS PARA LAS TARJETAS (STATS) ---
        
        // Citas para hoy (Excluyendo canceladas)
        $citasHoy = DB::table('tbl_cita')
            ->whereDate('cit_fechaCita', Carbon::today())
            ->where('cit_estadoCita', '!=', 'Cancelada')
            ->count();

        // Ingresos del Mes (Suma de precios de servicios en citas de este mes)
        $ingresosMes = DB::table('tbl_cita')
            ->join('tbl_servicio', 'tbl_cita.cit_servicioId', '=', 'tbl_servicio.srv_id')
            ->whereMonth('cit_fechaCita', Carbon::now()->month)
            ->whereYear('cit_fechaCita', Carbon::now()->year)
            ->where('cit_estadoCita', '!=', 'Cancelada')
            ->sum('tbl_servicio.srv_precio');

        // Clientes Activos (Rol 3)
        $clientesActivos = Usuario::where('usr_rolId', 3)
            ->where('usr_estado', 'A')
            ->count();

        // Puntos Totales (Suma global)
        $puntosTotales = DB::table('tbl_cliente')->sum('cli_puntosFidelizacion');

        // --- 2. TABLA CITAS RECIENTES (Últimas 5) ---
        $citasRecientes = DB::table('tbl_cita')
            ->join('tbl_cliente', 'tbl_cita.cit_clienteId', '=', 'tbl_cliente.cli_id')
            ->join('tbl_usuario', 'tbl_cliente.cli_usuarioId', '=', 'tbl_usuario.usr_id')
            ->join('tbl_servicio', 'tbl_cita.cit_servicioId', '=', 'tbl_servicio.srv_id')
            ->select(
                'tbl_usuario.usr_nombre',
                'tbl_usuario.usr_apellido',
                'tbl_servicio.srv_nombre',
                'tbl_servicio.srv_precio',
                'tbl_cita.cit_fechaCita'
            )
            ->orderBy('cit_fechaCita', 'desc')
            ->limit(5)
            ->get();

        // --- 3. TOP EMPLEADOS (Por número de citas atendidas) ---
        $topEmpleados = DB::table('tbl_empleado')
            ->join('tbl_usuario', 'tbl_empleado.emp_usuarioId', '=', 'tbl_usuario.usr_id')
            ->leftJoin('tbl_cita', 'tbl_empleado.emp_id', '=', 'tbl_cita.cit_empleadoId')
            ->select(
                'tbl_usuario.usr_nombre', 
                'tbl_usuario.usr_apellido',
                DB::raw('COUNT(tbl_cita.cit_id) as total_citas')
            )
            ->groupBy('tbl_empleado.emp_id', 'tbl_usuario.usr_nombre', 'tbl_usuario.usr_apellido')
            ->orderByDesc('total_citas')
            ->limit(3)
            ->get();

        // AQUÍ ESTÁ LA SOLUCIÓN: Enviamos las variables exactas que pide la vista
        return view('admin.dashboard', compact(
            'citasHoy', 
            'ingresosMes', 
            'clientesActivos', 
            'puntosTotales', 
            'citasRecientes', 
            'topEmpleados'
        ));
    }

    /**
     * Crear nuevo empleado 
     */
    public function crearEmpleado(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'cedula' => 'required|string|max:10|unique:Tbl_Usuario,usr_cedula',
            'email' => 'required|email|unique:Tbl_Usuario,usr_email',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'sucursal_id' => 'required|exists:Tbl_Sucursal,suc_id',
            'especialidad_id' => 'required|exists:Tbl_CategoriaServicio,cats_id',
        ]);

        try {
            $usuario = Usuario::create([
                'usr_nombre' => $request->nombre,
                'usr_apellido' => $request->apellido,
                'usr_cedula' => $request->cedula,
                'usr_email' => $request->email,
                'usr_telefono' => $request->telefono,
                'usr_password' => Hash::make($request->password),
                'usr_rolId' => 2,
                'usr_estado' => 'A',
            ]);

            return redirect()->route('admin.empleados.index')
                ->with('success', 'Empleado creado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function listarUsuarios()
    {
        $usuarios = Usuario::all();
        return view('admin.usuarios.lista_usuarios', compact('usuarios'));
    }

    public function listarSucursales()
    {
        return view('admin.configuracion.lista_sucursales');
    }

    public function listarServicios()
    {
        $servicios = DB::table('tbl_servicio')
            ->join('tbl_categoriaservicio', 'tbl_servicio.srv_categoriaId', '=', 'tbl_categoriaservicio.cats_id')
            ->leftJoin('tbl_cita', 'tbl_servicio.srv_id', '=', 'tbl_cita.cit_servicioId')
            ->select(
                'tbl_servicio.*',
                'tbl_categoriaservicio.cats_nombre',
                'tbl_categoriaservicio.cats_id',
                DB::raw('COUNT(tbl_cita.cit_id) as servicios_realizados')
            )
            ->groupBy(
                'tbl_servicio.srv_id', 'tbl_servicio.srv_nombre', 'tbl_servicio.srv_precio', 
                'tbl_servicio.srv_duracionMinutos', 'tbl_servicio.srv_categoriaId', 
                'tbl_servicio.srv_puntosFidelizacion', 'tbl_servicio.srv_estado',
                'tbl_categoriaservicio.cats_nombre', 'tbl_categoriaservicio.cats_id'
            )
            ->orderBy('servicios_realizados', 'desc')
            ->get();

        $maxCitas = $servicios->max('servicios_realizados');
        $maxCitas = $maxCitas == 0 ? 1 : $maxCitas;

        $servicios->transform(function($s) use ($maxCitas) {
            $s->popularidad = round(($s->servicios_realizados / $maxCitas) * 100);
            $s->descuento_activo = false;
            return $s;
        });

        $categorias = DB::table('tbl_categoriaservicio')
            ->where('cats_estado', '1')
            ->select('cats_id', 'cats_nombre')
            ->get();

        return view('admin.configuracion.lista_servicios', compact('servicios', 'categorias'));
    }

    public function listarPromociones() { return view('admin.configuracion.lista_promociones'); }
    public function listarCategorias() { return view('admin.configuracion.lista_categorias'); }
    public function listarCitas() { return view('admin.reservas.lista_citas'); }
    public function listarProductos() { return view('admin.inventario.lista_productos'); }
    public function listarProveedores() { return view('admin.inventario.lista_proveedores'); }
    public function reporteFinancieros() { return view('admin.reportes.financieros'); }
    public function reporteProductividad() { return view('admin.reportes.productividad'); }
    public function reporteInventario() { return view('admin.reportes.inventario'); }

    // ==========================================
    // SECCIÓN PERFIL DE ADMINISTRADOR
    // ==========================================

    public function editarPerfil()
    {
        $admin = Auth::user();
        $fechaRegistro = Carbon::parse($admin->usr_fechaRegistro);
        $antiguedad = $fechaRegistro->format('d/m/Y');
        $diasActivo = $fechaRegistro->diffInDays(now());

        return view('admin.editarAdmin', compact('admin', 'antiguedad', 'diasActivo'));
    }

    public function updatePerfil(Request $request)
    {
        $id = Auth::id();
        $request->validate([
            'usr_nombre'   => 'required|string|max:100',
            'usr_apellido' => 'required|string|max:100',
            'usr_email'    => ['required', 'email', Rule::unique('tbl_usuario')->ignore($id, 'usr_id')],
            'usr_telefono' => 'required|string|max:15',
            'usr_cedula'   => ['required', 'string', 'max:20', Rule::unique('tbl_usuario')->ignore($id, 'usr_id')],
        ]);

        try {
            DB::table('tbl_usuario')->where('usr_id', $id)->update([
                'usr_nombre'   => $request->usr_nombre,
                'usr_apellido' => $request->usr_apellido,
                'usr_email'    => $request->usr_email,
                'usr_telefono' => $request->usr_telefono,
                'usr_cedula'   => $request->usr_cedula,
                'updated_at'   => now()
            ]);
            return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();
        $current_encoded = base64_encode(hash('sha256', $request->current_password, true));

        if ($current_encoded !== $user->usr_password) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        $new_encoded = base64_encode(hash('sha256', $request->new_password, true));

        DB::table('tbl_usuario')->where('usr_id', $user->usr_id)->update([
            'usr_password' => $new_encoded,
            'updated_at'   => now()
        ]);

        return redirect()->back()->with('success', 'Contraseña actualizada correctamente.');
    }

    // ==========================================
    // SECCIÓN GESTIÓN DE USUARIOS (ELIMINAR / INACTIVAR)
    // ==========================================

    /**
     * Eliminar (Inactivar) un usuario
     */
    public function destroyUsuario($id)
    {
        try {
            // Verificar si el usuario existe
            $usuario = DB::table('tbl_usuario')->where('usr_id', $id)->first();
            
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado.'
                ], 404);
            }

            // Opcional: Evitar que el administrador principal se elimine a sí mismo
            if ($usuario->usr_id == Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propia cuenta mientras estás en sesión.'
                ], 403);
            }

            // Realizar borrado lógico (Cambiar estado a 'I' de Inactivo)
            DB::table('tbl_usuario')->where('usr_id', $id)->update([
                'usr_estado' => 'I',
                'updated_at' => now() // Si tienes esta columna en tu BD
            ]);

            // Si es un empleado, también podríamos inactivarlo en la tabla tbl_empleado si tuvieras un campo de estado ahí, 
            // pero con inactivar el usuario principal suele ser suficiente para que no pueda entrar al sistema ni aparecer en las listas.

            return response()->json([
                'success' => true,
                'message' => 'Usuario inactivado correctamente.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getNotificaciones() 
    {
        // 1. Solicitudes manuales de los empleados
        $reportes = DB::table('tbl_reporte_falta')
            ->join('tbl_producto', 'tbl_reporte_falta.rep_productoId', '=', 'tbl_producto.prd_id')
            ->join('tbl_empleado', 'tbl_reporte_falta.rep_empleadoId', '=', 'tbl_empleado.emp_id')
            ->join('tbl_usuario', 'tbl_empleado.emp_usuarioId', '=', 'tbl_usuario.usr_id')
            ->where('rep_estado', 'Pendiente')
            ->select('tbl_reporte_falta.rep_id as id', 'tbl_producto.prd_nombre as producto', 'tbl_usuario.usr_nombre as creador', 'rep_fecha as fecha', DB::raw("'solicitud' as tipo"), DB::raw("0 as stock"))
            ->get();

        // 2. Alertas Automáticas del Sistema (Stock <= 5)
        $alertasSistema = DB::table('tbl_producto')
            ->where('prd_estado', 'A')
            ->where('prd_stockActual', '<=', 5)
            ->select('prd_id as id', 'prd_nombre as producto', DB::raw("'Sistema' as creador"), DB::raw("NOW() as fecha"), DB::raw("'alerta_stock' as tipo"), 'prd_stockActual as stock')
            ->get();

        // Unimos las dos alertas y las ordenamos
        $notificaciones = collect($reportes)->merge($alertasSistema)->sortByDesc('fecha')->values()->all();

        return response()->json([
            'count' => count($notificaciones),
            'data' => $notificaciones
        ]);
    }
}