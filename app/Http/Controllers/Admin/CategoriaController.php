<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoriaProducto;
use App\Models\CategoriaServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    /**
     * Mostrar listado de categorías (productos y servicios)
     */
    public function index()
    {
        try {
            $categoriasProductos = CategoriaProducto::orderBy('catp_id', 'desc')->get();
            $categoriasServicios = CategoriaServicio::orderBy('cats_id', 'desc')->get();
            
            // 1. CONTAR PRODUCTOS ASOCIADOS A CADA CATEGORÍA
            foreach($categoriasProductos as $cat) {
                $cat->items_asociados = DB::table('tbl_producto')->where('prd_categoriaId', $cat->catp_id)->count();
            }

            // 2. CONTAR SERVICIOS ASOCIADOS A CADA CATEGORÍA
            foreach($categoriasServicios as $cats) {
                $cats->items_asociados = DB::table('tbl_servicio')->where('srv_categoriaId', $cats->cats_id)->count();
            }
            
            // Estadísticas para las cards superiores
            $stats = [
                'total_p' => $categoriasProductos->count(),
                'total_s' => $categoriasServicios->count(),
                'activas_p' => $categoriasProductos->where('catp_estado', '1')->count(),
                'activas_s' => $categoriasServicios->where('cats_estado', '1')->count(),
            ];

            return view('admin.configuracion.lista_categorias', compact(
                'categoriasProductos', 
                'categoriasServicios', 
                'stats'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar las categorías: ' . $e->getMessage());
        }
    }

    /**
     * Guardar o actualizar categoría (producto o servicio)
     */
    public function store(Request $request)
    {
        try {
            // Validación
            $request->validate([
                'tipo_categoria_db' => 'required|in:producto,servicio',
                'catp_nombre' => 'required|string|max:255',
                'catp_descripcion' => 'nullable|string|max:500',
                'catp_estado' => 'required|in:0,1',
                // Validamos la comisión solo si envían algo
                'cats_porcentajeComision' => 'nullable|integer|min:0|max:100'
            ], [
                'tipo_categoria_db.required' => 'Debe seleccionar un tipo de categoría',
                'catp_nombre.required' => 'El nombre es obligatorio',
                'catp_nombre.max' => 'El nombre no puede exceder 255 caracteres',
                'catp_descripcion.max' => 'La descripción no puede exceder 500 caracteres'
            ]);

            $tipo = $request->input('tipo_categoria_db');
            $nombre = $request->input('catp_nombre');
            $descripcion = $request->input('catp_descripcion');
            $estado = $request->input('catp_estado', '1');
            $comision = $request->input('cats_porcentajeComision', 20); // 20% por defecto si no mandan nada

            DB::beginTransaction();

            if ($tipo === 'producto') {
                // Verificar duplicados solo si es creación nueva
                if (!$request->catp_id) {
                    $existe = CategoriaProducto::where('catp_nombre', $nombre)->first();
                    if ($existe) {
                        return back()->with('error', 'Ya existe una categoría de producto con ese nombre');
                    }
                }

                CategoriaProducto::updateOrCreate(
                    ['catp_id' => $request->catp_id],
                    [
                        'catp_nombre' => $nombre,
                        'catp_descripcion' => $descripcion,
                        'catp_estado' => $estado
                    ]
                );

                $mensaje = $request->catp_id ? 'Categoría de producto actualizada exitosamente' : 'Categoría de producto creada exitosamente';
            } else {
                // Verificar duplicados solo si es creación nueva
                if (!$request->cats_id) {
                    $existe = CategoriaServicio::where('cats_nombre', $nombre)->first();
                    if ($existe) {
                        return back()->with('error', 'Ya existe una categoría de servicio con ese nombre');
                    }
                }

                CategoriaServicio::updateOrCreate(
                    ['cats_id' => $request->cats_id],
                    [
                        'cats_nombre' => $nombre,
                        'cats_descripcion' => $descripcion,
                        'cats_estado' => $estado,
                        'cats_porcentajeComision' => $comision // GUARDAMOS LA COMISIÓN AQUÍ
                    ]
                );

                $mensaje = $request->cats_id ? 'Categoría de servicio actualizada exitosamente' : 'Categoría de servicio creada exitosamente';
            }

            DB::commit();
            return redirect()->route('categorias.index')->with('success', $mensaje);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al guardar la categoría: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Eliminar categoría (producto o servicio)
     */
    public function destroy($tipo, $id)
    {
        try {
            DB::beginTransaction();

            if ($tipo === 'producto') {
                // VERIFICACIÓN DE SEGURIDAD ANTES DE ELIMINAR
                $asociados = DB::table('tbl_producto')->where('prd_categoriaId', $id)->count();
                if ($asociados > 0) {
                    return back()->with('error', "No se puede eliminar la categoría porque tiene $asociados producto(s) asignado(s).");
                }

                $categoria = CategoriaProducto::findOrFail($id);
                $categoria->delete();
                $mensaje = 'Categoría de producto eliminada exitosamente';
            } else {
                // VERIFICACIÓN DE SEGURIDAD ANTES DE ELIMINAR
                $asociados = DB::table('tbl_servicio')->where('srv_categoriaId', $id)->count();
                if ($asociados > 0) {
                    return back()->with('error', "No se puede eliminar la categoría porque tiene $asociados servicio(s) asignado(s).");
                }

                $categoria = CategoriaServicio::findOrFail($id);
                $categoria->delete();
                $mensaje = 'Categoría de servicio eliminada exitosamente';
            }

            DB::commit();
            return redirect()->route('categorias.index')->with('success', $mensaje);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return back()->with('error', 'Categoría no encontrada');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar la categoría: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado de categoría (activar/desactivar)
     * Método adicional opcional
     */
    public function toggleEstado($tipo, $id)
    {
        try {
            if ($tipo === 'producto') {
                $categoria = CategoriaProducto::findOrFail($id);
                $categoria->catp_estado = $categoria->catp_estado == '1' ? '0' : '1';
                $categoria->save();
            } else {
                $categoria = CategoriaServicio::findOrFail($id);
                $categoria->cats_estado = $categoria->cats_estado == '1' ? '0' : '1';
                $categoria->save();
            }

            return back()->with('success', 'Estado actualizado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }
}