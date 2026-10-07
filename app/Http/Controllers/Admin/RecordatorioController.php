<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RecordatorioController extends Controller
{
    public function index()
    {
        // 1. Obtener recordatorios con información del cliente (Si está ligado a una cita)
        $recordatorios = DB::table('tbl_recordatorio')
            ->leftJoin('tbl_cita', 'tbl_recordatorio.rec_citaId', '=', 'tbl_cita.cit_id')
            ->leftJoin('tbl_cliente', 'tbl_cita.cit_clienteId', '=', 'tbl_cliente.cli_id')
            ->leftJoin('tbl_usuario', 'tbl_cliente.cli_usuarioId', '=', 'tbl_usuario.usr_id')
            ->select(
                'tbl_recordatorio.*',
                'tbl_usuario.usr_nombre',
                'tbl_usuario.usr_apellido',
                'tbl_cita.cit_fechaCita'
            )
            ->orderBy('rec_fechaEnvio', 'desc')
            ->get();

        // 2. Obtener próximas citas para el select del formulario "Nuevo Recordatorio"
        $proximasCitas = DB::table('tbl_cita')
            ->join('tbl_cliente', 'tbl_cita.cit_clienteId', '=', 'tbl_cliente.cli_id')
            ->join('tbl_usuario', 'tbl_cliente.cli_usuarioId', '=', 'tbl_usuario.usr_id')
            ->where('cit_fechaCita', '>=', now())
            ->where('cit_estadoCita', '!=', 'Cancelada')
            ->select('cit_id', 'cit_fechaCita', 'usr_nombre', 'usr_apellido')
            ->orderBy('cit_fechaCita', 'asc')
            ->get();

        return view('admin.recordatorios.lista_recordatorios', compact('recordatorios', 'proximasCitas'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rec_tipo' => 'required|string',
            'rec_citaId' => 'nullable|integer', // Puede ser nulo si es un aviso general
            'rec_destinatario' => 'required|string', // Email o Teléfono
            'rec_mensaje' => 'required|string',
            'rec_fechaEnvio' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::table('tbl_recordatorio')->insert([
                'rec_tipo' => $request->rec_tipo,
                'rec_citaId' => $request->rec_citaId,
                'rec_productoId' => null, // Por ahora null, luego puedes agregar lógica de productos
                'rec_destinatario' => $request->rec_destinatario,
                'rec_mensaje' => $request->rec_mensaje,
                'rec_fechaEnvio' => $request->rec_fechaEnvio,
                'rec_enviado' => 0, // 0 = Pendiente, 1 = Enviado
                'rec_fechaEnviado' => null
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::table('tbl_recordatorio')->where('rec_id', $id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    // Actualizar recordatorio existente
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rec_tipo' => 'required|string',
            'rec_citaId' => 'nullable|integer',
            'rec_destinatario' => 'required|string',
            'rec_mensaje' => 'required|string',
            'rec_fechaEnvio' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::table('tbl_recordatorio')->where('rec_id', $id)->update([
                'rec_tipo' => $request->rec_tipo,
                'rec_citaId' => $request->rec_citaId,
                'rec_destinatario' => $request->rec_destinatario,
                'rec_mensaje' => $request->rec_mensaje,
                'rec_fechaEnvio' => $request->rec_fechaEnvio,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}