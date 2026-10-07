@extends('layouts.app')

@section('title', 'Lista de Empleados')

@section('content')
<div class="container">
    <h1>Lista de Empleados</h1>
    
    <div class="mb-3">
        <a href="{{ route('admin.empleados.create') }}" class="btn btn-primary">Nuevo Empleado</a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Sucursal</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($empleados as $empleado)
                    <tr>
                        <td>{{ $empleado->emp_id }}</td>
                        <td>{{ $empleado->usuario->usr_nombre }} {{ $empleado->usuario->usr_apellido }}</td>
                        <td>{{ $empleado->usuario->usr_email }}</td>
                        <td>{{ $empleado->sucursal->suc_nombre ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-{{ $empleado->emp_estado == 'A' ? 'success' : 'danger' }}">
                                {{ $empleado->emp_estado == 'A' ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info">Ver</a>
                            <a href="#" class="btn btn-sm btn-warning">Editar</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay empleados registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection