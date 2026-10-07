<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        // 1. Obtenemos los proveedores de la BD
        $proveedores = Proveedor::all();

        // 2. Calculamos estadísticas
        $stats = [
            'total' => $proveedores->count(),
            'activos' => $proveedores->where('prv_estado', 'A')->count(),
        ];

        // 3. Listas para los filtros (opcional, si los usas en la vista)
        $ciudades = ['Guayaquil', 'Quito', 'Cuenca', 'Manta', 'Ambato'];
        $tiposProductos = ['Cuidado Capilar', 'Herramientas', 'Maquillaje', 'Equipos'];

        return view('admin.inventario.lista_proveedores', compact('proveedores', 'stats', 'ciudades', 'tiposProductos'));
    }

    public function store(Request $request)
    {
        // Validación para crear
        $validated = $request->validate([
            'prv_nombre'    => 'required|max:255',
            'prv_email'     => 'required|email|unique:tbl_proveedor,prv_email',
            'prv_telefono'  => 'required|max:20',
            'prv_direccion' => 'nullable|max:255',
            'prv_estado'    => 'required|in:A,I'
        ]);

        Proveedor::create($validated);

        return redirect()->back()->with('success', 'Proveedor registrado correctamente.');
    }

    // NUEVO: Método para actualizar (Editar)
    public function update(Request $request, $id)
    {
        // Buscamos el proveedor por su ID (prv_id)
        $proveedor = Proveedor::findOrFail($id);

        $validated = $request->validate([
            'prv_nombre'    => 'required|max:255',
            // Nota: Ignoramos el email del proveedor actual para que no de error si no lo cambia
            'prv_email'     => 'required|email|unique:tbl_proveedor,prv_email,' . $id . ',prv_id',
            'prv_telefono'  => 'required|max:20',
            'prv_direccion' => 'nullable|max:255',
            'prv_estado'    => 'required|in:A,I'
        ]);

        $proveedor->update($validated);

        return redirect()->back()->with('success', 'Proveedor actualizado correctamente.');
    }

    // NUEVO: Método para eliminar
    public function destroy($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->delete();

        return redirect()->back()->with('success', 'Proveedor eliminado correctamente.');
    }
}