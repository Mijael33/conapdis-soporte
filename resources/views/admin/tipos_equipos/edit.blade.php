@extends('layouts.admin')
@section('title', 'Editar Tipo de Equipo')
@section('page-title', 'Editar Tipo de Equipo')
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Editar: {{ $tiposEquipo->nombre }}</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.tipos-equipos.update', $tiposEquipo) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $tiposEquipo->nombre) }}" required>
                @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion', $tiposEquipo->descripcion) }}</textarea>
                @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <button type="submit" class="btn-conapdis">Actualizar</button>
            <a href="{{ route('admin.tipos-equipos.index') }}" class="btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>
@endsection