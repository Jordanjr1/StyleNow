@extends('layouts.app')

@section('title', 'Crear Empleado')

@section('content')
<div class="container">
    <h1>Crear Nuevo Empleado</h1>
    
    <form method="POST" action="{{ route('admin.empleados.store') }}">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" required>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="cedula" class="form-label">Cédula</label>
                <input type="text" class="form-control" id="cedula" name="cedula" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono">
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="sucursal_id" class="form-label">Sucursal</label>
            <select class="form-select" id="sucursal_id" name="sucursal_id" required>
                <option value="">Seleccionar sucursal</option>
                @foreach($sucursales as $sucursal)
                    <option value="{{ $sucursal->suc_id }}">{{ $sucursal->suc_nombre }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="mb-3">
            <label for="especialidad_id" class="form-label">Especialidad</label>
            <select class="form-select" id="especialidad_id" name="especialidad_id" required>
                <option value="">Seleccionar especialidad</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->cats_id }}">{{ $categoria->cats_nombre }}</option>
                @endforeach
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Crear Empleado</button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection