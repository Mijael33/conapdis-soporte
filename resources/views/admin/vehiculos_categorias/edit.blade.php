@extends('layouts.admin')
@section('title', 'Editar Categoría de Vehículo')
@section('page-title', 'Editar Categoría')
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Editar: {{ $categoria->nombre }}</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.vehiculos-categorias.update', $categoria) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $categoria->nombre) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion', $categoria->descripcion) }}</textarea>
            </div>
            <button type="submit" class="btn-conapdis">Actualizar</button>
            <a href="{{ route('admin.vehiculos-categorias.index') }}" class="btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>
@endsection