<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function listarUsuarios() { return view('admin.usuarios.lista_usuarios'); }

    public function index()
    {
        $usuarios = Usuario::with('rol')
            ->leftJoin('tbl_empleado', 'tbl_usuario.usr_id', '=', 'tbl_empleado.emp_usuarioId')
            ->select(
                'tbl_usuario.usr_id', 'usr_nombre', 'usr_apellido', 'usr_cedula', 'usr_email', 
                'usr_telefono', 'usr_rolId', 'usr_estado', 'usr_fechaRegistro', 
                'tbl_empleado.emp_sucursalId', 'tbl_empleado.emp_id', 
                // NUEVOS CAMPOS FINANCIEROS DEL MODELO PROFESIONAL
                'tbl_empleado.emp_sueldoBase', 'tbl_empleado.emp_comisionProductos',
                'tbl_empleado.emp_metaMensual', 'tbl_empleado.emp_bonoMeta'
            )
            ->get()
            ->map(function ($u) {
                $especialidades = [];
                if ($u->emp_id) {
                    $especialidades = DB::table('tbl_empleado_especialidad')
                                        ->where('emp_id', $u->emp_id)
                                        ->pluck('cats_id')->toArray();
                }

                return [
                    'usr_id' => $u->usr_id,
                    'usr_nombre' => $u->usr_nombre,
                    'usr_apellido' => $u->usr_apellido,
                    'usr_cedula' => $u->usr_cedula,
                    'usr_email' => $u->usr_email,
                    'usr_telefono' => $u->usr_telefono,
                    'rol_nombre' => $u->rol ? $u->rol->rol_nombre : 'Sin rol',
                    'usr_rolId' => $u->usr_rolId,
                    'usr_estado' => $u->usr_estado,
                    'usr_fechaRegistro' => $u->usr_fechaRegistro,
                    'emp_sucursalId' => $u->emp_sucursalId, 
                    
                    // PASAMOS LOS DATOS FINANCIEROS A LA VISTA
                    'emp_sueldoBase' => $u->emp_sueldoBase,
                    'emp_comisionProductos' => $u->emp_comisionProductos,
                    'emp_metaMensual' => $u->emp_metaMensual,
                    'emp_bonoMeta' => $u->emp_bonoMeta,

                    'especialidades' => $especialidades, 
                    'tipo_usuario' => match($u->usr_rolId) { 1=>'administrador', 2=>'empleado', 3=>'cliente', default=>'desconocido' }
                ];
            });
        return response()->json($usuarios);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usr_nombre' => 'required|string|max:100',
            'usr_apellido' => 'required|string|max:100',
            'usr_cedula' => 'required|string|max:20|unique:Tbl_Usuario,usr_cedula',
            'usr_email' => 'required|email|max:100|unique:Tbl_Usuario,usr_email',
            'usr_telefono' => 'required|string|max:15',
            'usr_password' => 'required|string|min:6|confirmed',
            'usr_rolId' => 'required|integer',
            'usr_estado' => 'required|in:A,I',
        ]);

        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        DB::beginTransaction();
        try {
            $data = $request->only(['usr_nombre', 'usr_apellido', 'usr_cedula', 'usr_email', 'usr_telefono', 'usr_rolId', 'usr_estado']);
            
            $data['usr_password'] = base64_encode(hash('sha256', $request->usr_password, true));
            $data['usr_fechaRegistro'] = now()->format('Y-m-d');

            $usuario = Usuario::create($data);

            if ($request->usr_rolId == 3) { 
                Cliente::create(['cli_usuarioId' => $usuario->usr_id, 'cli_puntosFidelizacion' => 0]);
            } 
            elseif ($request->usr_rolId == 2) { 
                $sucursalId = $request->emp_sucursalId ?? DB::table('tbl_sucursal')->value('suc_id') ?? 1;

                // GUARDAMOS EL EMPLEADO CON SU ESTRUCTURA SALARIAL COMPLETA
                $empId = DB::table('tbl_empleado')->insertGetId([
                    'emp_usuarioId' => $usuario->usr_id,
                    'emp_sucursalId' => $sucursalId, 
                    'emp_sueldoBase' => $request->emp_sueldoBase ?? 0.00,
                    'emp_comisionProductos' => $request->emp_comisionProductos ?? 10, // 10% por defecto
                    'emp_metaMensual' => $request->emp_metaMensual ?? 0.00,
                    'emp_bonoMeta' => $request->emp_bonoMeta ?? 0.00,
                    'emp_especialidadId' => 1,
                    'emp_citasCompletadas' => 0,
                    'emp_comisionTotal' => 0.00,
                    'emp_estado' => 'A'
                ]);

                if ($request->has('especialidades') && is_array($request->especialidades)) {
                    $espData = [];
                    foreach($request->especialidades as $catId) {
                        $espData[] = ['emp_id' => $empId, 'cats_id' => $catId];
                    }
                    DB::table('tbl_empleado_especialidad')->insert($espData);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'id' => $usuario->usr_id], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $data = $request->only(['usr_nombre', 'usr_apellido', 'usr_cedula', 'usr_email', 'usr_telefono', 'usr_rolId', 'usr_estado']);
            
            if ($request->filled('usr_password')) {
                $data['usr_password'] = base64_encode(hash('sha256', $request->usr_password, true));
            }

            $usuario->update($data);

            $nuevoRol = (int) $request->usr_rolId;

            if ($nuevoRol === 3) { 
                Cliente::firstOrCreate(['cli_usuarioId' => $usuario->usr_id], ['cli_puntosFidelizacion' => 0]);
                $empId = DB::table('tbl_empleado')->where('emp_usuarioId', $usuario->usr_id)->value('emp_id');
                if($empId) DB::table('tbl_empleado_especialidad')->where('emp_id', $empId)->delete();
                DB::table('tbl_empleado')->where('emp_usuarioId', $usuario->usr_id)->delete();
            } 
            elseif ($nuevoRol === 2) { 
                $sucursalId = $request->emp_sucursalId ?? DB::table('tbl_sucursal')->value('suc_id') ?? 1;

                $empId = DB::table('tbl_empleado')->where('emp_usuarioId', $usuario->usr_id)->value('emp_id');
                
                if (!$empId) {
                    $empId = DB::table('tbl_empleado')->insertGetId([
                        'emp_usuarioId' => $usuario->usr_id,
                        'emp_sucursalId' => $sucursalId, 
                        'emp_sueldoBase' => $request->emp_sueldoBase ?? 0.00,
                        'emp_comisionProductos' => $request->emp_comisionProductos ?? 10,
                        'emp_metaMensual' => $request->emp_metaMensual ?? 0.00,
                        'emp_bonoMeta' => $request->emp_bonoMeta ?? 0.00,
                        'emp_especialidadId' => 1,
                        'emp_estado' => 'A'
                    ]);
                } else {
                    DB::table('tbl_empleado')->where('emp_id', $empId)->update([
                        'emp_sucursalId' => $sucursalId, 
                        'emp_sueldoBase' => $request->emp_sueldoBase ?? 0.00,
                        'emp_comisionProductos' => $request->emp_comisionProductos ?? 10,
                        'emp_metaMensual' => $request->emp_metaMensual ?? 0.00,
                        'emp_bonoMeta' => $request->emp_bonoMeta ?? 0.00,
                        'emp_estado' => 'A'
                    ]);
                }

                DB::table('tbl_empleado_especialidad')->where('emp_id', $empId)->delete(); 
                if ($request->has('especialidades') && is_array($request->especialidades)) {
                    $espData = [];
                    foreach($request->especialidades as $catId) {
                        $espData[] = ['emp_id' => $empId, 'cats_id' => $catId];
                    }
                    DB::table('tbl_empleado_especialidad')->insert($espData); 
                }

                Cliente::where('cli_usuarioId', $usuario->usr_id)->delete();
            } 
            else { 
                Cliente::where('cli_usuarioId', $usuario->usr_id)->delete();
                $empId = DB::table('tbl_empleado')->where('emp_usuarioId', $usuario->usr_id)->value('emp_id');
                if($empId) DB::table('tbl_empleado_especialidad')->where('emp_id', $empId)->delete();
                DB::table('tbl_empleado')->where('emp_usuarioId', $usuario->usr_id)->delete();
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id) {
        $usuario = Usuario::findOrFail($id);
        DB::beginTransaction();
        try {
            Cliente::where('cli_usuarioId', $id)->delete();
            $empId = DB::table('tbl_empleado')->where('emp_usuarioId', $id)->value('emp_id');
            if($empId) DB::table('tbl_empleado_especialidad')->where('emp_id', $empId)->delete();
            DB::table('tbl_empleado')->where('emp_usuarioId', $id)->delete();
            
            $usuario->delete();
            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getSucursales() {
        return response()->json(DB::table('tbl_sucursal')->select('suc_id as id', 'suc_nombre as nombre')->get());
    }

    public function getCategorias() {
        return response()->json(DB::table('tbl_categoriaservicio')->select('cats_id as id', 'cats_nombre as nombre')->get());
    }
}