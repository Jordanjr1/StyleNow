<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServicioController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'srv_nombre' => 'required|string|max:100',
            'srv_categoriaId' => 'required|integer',
            'srv_precio' => 'required|numeric|min:0',
            'srv_duracionMinutos' => 'required|integer|min:5',
            'srv_puntosFidelizacion' => 'nullable|integer',
            // CORRECCIÓN AQUÍ: Ahora aceptamos 1 y 0
            'srv_estado' => 'required|in:1,0' 
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $id = DB::table('tbl_servicio')->insertGetId([
                'srv_nombre' => $request->srv_nombre,
                'srv_categoriaId' => $request->srv_categoriaId,
                'srv_precio' => $request->srv_precio,
                'srv_duracionMinutos' => $request->srv_duracionMinutos,
                'srv_puntosFidelizacion' => $request->srv_puntosFidelizacion ?? 0,
                'srv_estado' => $request->srv_estado, // Se guardará como 1 o 0
            ]);

            return response()->json(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Validamos también en el update para evitar el mismo error al editar
        $validator = Validator::make($request->all(), [
            'srv_estado' => 'sometimes|in:1,0'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::table('tbl_servicio')->where('srv_id', $id)->update($request->only([
                'srv_nombre', 'srv_categoriaId', 'srv_precio', 
                'srv_duracionMinutos', 'srv_puntosFidelizacion', 'srv_estado'
            ]));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $tieneCitas = DB::table('tbl_cita')->where('cit_servicioId', $id)->exists();
            
            if($tieneCitas) {
                // Soft delete: Cambiar estado a 0 (Inactivo)
                DB::table('tbl_servicio')->where('srv_id', $id)->update(['srv_estado' => '0']);
                return response()->json(['success' => true, 'message' => 'Servicio desactivado porque tiene historial.']);
            } else {
                DB::table('tbl_servicio')->where('srv_id', $id)->delete();
                return response()->json(['success' => true, 'message' => 'Servicio eliminado.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}