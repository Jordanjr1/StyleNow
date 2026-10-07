<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PromocionController extends Controller
{
    public function index()
    {
        // Obtener todas las promociones
        $promociones = DB::table('tbl_promocion')
            ->orderBy('prm_estado', 'desc') // Activas primero
            ->orderBy('prm_fechaFin', 'asc') // Las que vencen pronto primero
            ->get();

        // Procesar datos para la vista (Calcular estado lógico)
        $promociones->transform(function($p) {
            $hoy = Carbon::now();
            $fin = Carbon::parse($p->prm_fechaFin);
            
            // Lógica de estado visual
            if ($p->prm_estado == 0) {
                $p->estado_visual = 'Inactiva';
                $p->clase_visual = 'badge-inactive';
            } elseif ($fin->isPast()) {
                $p->estado_visual = 'Vencida';
                $p->clase_visual = 'badge-expired'; // Clase nueva para vencidas
            } else {
                $p->estado_visual = 'Activa';
                $p->clase_visual = 'badge-active';
            }
            
            return $p;
        });

        return view('admin.configuracion.lista_promociones', compact('promociones'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prm_nombre' => 'required|string|max:100',
            'prm_tipoDescuento' => 'required|string',
            'prm_valorDescuento' => 'required|numeric|min:0',
            'prm_fechaInicio' => 'required|date',
            'prm_fechaFin' => 'required|date|after_or_equal:prm_fechaInicio',
            'prm_estado' => 'required|in:1,0'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $id = DB::table('tbl_promocion')->insertGetId([
                'prm_nombre' => $request->prm_nombre,
                'prm_descripcion' => $request->prm_descripcion,
                'prm_tipoDescuento' => $request->prm_tipoDescuento,
                'prm_valorDescuento' => $request->prm_valorDescuento,
                'prm_fechaInicio' => $request->prm_fechaInicio,
                'prm_fechaFin' => $request->prm_fechaFin,
                'prm_estado' => $request->prm_estado
            ]);

            return response()->json(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::table('tbl_promocion')->where('prm_id', $id)->update($request->only([
                'prm_nombre', 'prm_descripcion', 'prm_tipoDescuento',
                'prm_valorDescuento', 'prm_fechaInicio', 'prm_fechaFin', 'prm_estado'
            ]));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::table('tbl_promocion')->where('prm_id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Promoción eliminada correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function toggleStatus($id)
    {
        try {
            // Obtener estado actual
            $promo = DB::table('tbl_promocion')->where('prm_id', $id)->first();
            
            if (!$promo) {
                return response()->json(['success' => false, 'message' => 'Promoción no encontrada'], 404);
            }

            // Invertir estado (Si es 1 pasa a 0, si es 0 pasa a 1)
            $nuevoEstado = $promo->prm_estado == 1 ? 0 : 1;

            DB::table('tbl_promocion')
                ->where('prm_id', $id)
                ->update(['prm_estado' => $nuevoEstado]);

            $mensaje = $nuevoEstado == 1 ? 'Promoción activada.' : 'Promoción desactivada.';

            return response()->json(['success' => true, 'message' => $mensaje, 'nuevo_estado' => $nuevoEstado]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}