<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\CategoriaProducto;
use App\Models\Sucursal; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['proveedor', 'categoria', 'sucursal'])->get();
        $proveedores = Proveedor::where('prv_estado', 'A')->get();
        $categorias = CategoriaProducto::all(); 
        $sucursales = Sucursal::where('suc_estado', '1')->get();

        $stats = [
            'total' => $productos->count(),
            'stock_bajo' => $productos->filter(fn($p) => $p->prd_stockActual <= $p->prd_stockMinimo)->count(),
            'valor_inventario' => $productos->sum(fn($p) => $p->prd_stockActual * $p->prd_precioCompra)
        ];

        return view('admin.inventario.lista_productos', compact('productos', 'stats', 'proveedores', 'categorias', 'sucursales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'prd_nombre'      => 'required|string|max:255',
            'prd_proveedorId' => 'required',
            'prd_categoriaId' => 'required', 
            'prd_sucursalId'  => 'required',
            'prd_stockActual' => 'required|numeric|min:0',
            'prd_stockMinimo' => 'required|numeric|min:1',
            'prd_unidadMedida'=> 'required|string',
            'prd_precioCompra' => 'required|numeric|min:0.01', // No puedes comprar a $0
            'prd_precioVenta' => 'required|numeric|min:0.01',  // No puedes vender a $0
            'prd_tieneIva' => 'nullable|in:0,1',
            'prd_imagen'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048' // Max 2MB
        ]);

        $validated['prd_estado'] = 'A';
        $validated['prd_tieneIva'] = $request->input('prd_tieneIva', 0) == 1 ? 1 : 0;

        // Manejo de la subida de imagen
        if ($request->hasFile('prd_imagen')) {
            $file = $request->file('prd_imagen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/productos'), $filename);
            $validated['prd_imagen'] = 'uploads/productos/' . $filename;
        }

        try {
            Producto::create($validated);
            return redirect()->back()->with('success', 'Producto guardado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        
        $validated = $request->validate([
            'prd_nombre'      => 'required|string|max:255',
            'prd_categoriaId' => 'required',
            'prd_proveedorId' => 'required',
            'prd_sucursalId'  => 'required',
            'prd_stockActual' => 'required|numeric|min:0', // Agregamos min:0
            'prd_stockMinimo' => 'required|numeric|min:1', // El mínimo de alerta debería ser al menos 1
            'prd_unidadMedida'=> 'required|string',
            'prd_precioCompra' => 'required|numeric|min:0.01', // No puedes comprar a $0
            'prd_precioVenta' => 'required|numeric|min:0.01',
            'prd_tieneIva' => 'nullable|in:0,1',
            'prd_imagen'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);
        $validated['prd_tieneIva'] = $request->input('prd_tieneIva', 0) == 1 ? 1 : 0;
        
        // Manejo de la actualización de imagen
        if ($request->hasFile('prd_imagen')) {
            // Eliminar imagen anterior si existe
            if ($producto->prd_imagen && File::exists(public_path($producto->prd_imagen))) {
                File::delete(public_path($producto->prd_imagen));
            }
            
            $file = $request->file('prd_imagen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/productos'), $filename);
            $validated['prd_imagen'] = 'uploads/productos/' . $filename;
        }

        $producto->update($validated);
        return redirect()->back()->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        
        // Opcional: Eliminar la imagen del servidor si se borra el producto
        if ($producto->prd_imagen && File::exists(public_path($producto->prd_imagen))) {
            File::delete(public_path($producto->prd_imagen));
        }

        $producto->delete(); 
        return redirect()->back()->with('success', 'Producto eliminado correctamente.');
    }

    // Carga la bandeja de entrada de solicitudes de los empleados
    public function solicitudes() 
    {
        $solicitudes = DB::table('tbl_reporte_falta')
            ->join('tbl_producto', 'tbl_reporte_falta.rep_productoId', '=', 'tbl_producto.prd_id')
            ->leftJoin('tbl_sucursal', 'tbl_producto.prd_sucursalId', '=', 'tbl_sucursal.suc_id')
            ->join('tbl_empleado', 'tbl_reporte_falta.rep_empleadoId', '=', 'tbl_empleado.emp_id')
            ->join('tbl_usuario', 'tbl_empleado.emp_usuarioId', '=', 'tbl_usuario.usr_id')
            ->select(
                'tbl_reporte_falta.*', 
                'tbl_producto.prd_nombre', 
                'tbl_producto.prd_stockActual', 
                'tbl_sucursal.suc_nombre',
                DB::raw("CONCAT(tbl_usuario.usr_nombre, ' ', tbl_usuario.usr_apellido) as empleado_nombre")
            )
            // Ordena primero los Pendientes, luego los Atendidos, y por fecha
            ->orderByRaw("FIELD(rep_estado, 'Pendiente', 'Atendido')")
            ->orderBy('rep_fecha', 'desc')
            ->get();

        return view('admin.inventario.solicitudes', compact('solicitudes'));
    }

    // Marca un reporte como comprado/atendido
    // Marca un reporte como comprado/atendido Y SUMA EL STOCK
    public function atenderSolicitud(Request $request, $id) 
    {
        // Recibimos la cantidad que el admin ingresó en el popup (por defecto 0)
        $cantidadComprada = (int) $request->input('cantidad', 0);

        try {
            // 1. Buscamos el reporte para saber qué producto pidió el empleado
            $reporte = DB::table('tbl_reporte_falta')->where('rep_id', $id)->first();
            
            // 2. Si el admin indicó que compró 1 o más, le sumamos eso al stock real
            if ($reporte && $cantidadComprada > 0) {
                DB::table('tbl_producto')
                    ->where('prd_id', $reporte->rep_productoId)
                    ->increment('prd_stockActual', $cantidadComprada);
            }

            // 3. Finalmente, marcamos el reporte del empleado como "Atendido"
            DB::table('tbl_reporte_falta')
                ->where('rep_id', $id)
                ->update(['rep_estado' => 'Atendido']);
                
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}