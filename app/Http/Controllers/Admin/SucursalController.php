<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index()
    {
        $sucursales = Sucursal::all();
        
        // Datos para las tarjetas de estadísticas
        $stats = [
            'total' => $sucursales->count(),
            'activas' => $sucursales->where('suc_estado', '1')->count(),
            'inactivas' => $sucursales->where('suc_estado', '0')->count(),
        ];

        return view('admin.configuracion.lista_sucursales', compact('sucursales', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'suc_nombre'    => 'required|string|max:100',
            'suc_direccion' => 'required|string',
            'suc_telefono'  => 'nullable|string',
            'suc_email'     => 'nullable|email',
        ]);

        try {
            Sucursal::create([
                'suc_nombre'    => $validated['suc_nombre'],
                'suc_direccion' => $validated['suc_direccion'],
                'suc_telefono'  => $validated['suc_telefono'],
                'suc_email'     => $validated['suc_email'],
                'suc_estado'    => '1', // Por defecto activa al crear
            ]);
            return redirect()->back()->with('success', 'Sucursal creada con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    // MÉTODO NUEVO: Actualizar (Editar)
    public function update(Request $request, $id)
    {
        // Validamos también el estado, ya que en editar sí se puede cambiar
        $validated = $request->validate([
            'suc_nombre'    => 'required|string|max:100',
            'suc_direccion' => 'required|string',
            'suc_telefono'  => 'nullable|string',
            'suc_email'     => 'nullable|email',
            'suc_estado'    => 'required|in:1,0', 
        ]);

        try {
            $sucursal = Sucursal::findOrFail($id);
            $sucursal->update($validated);

            return redirect()->back()->with('success', 'Sucursal actualizada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    // MÉTODO NUEVO: Eliminar
    public function destroy($id)
    {
        try {
            $sucursal = Sucursal::findOrFail($id);
            $sucursal->delete();

            return redirect()->back()->with('success', 'Sucursal eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}