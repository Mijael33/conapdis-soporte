@extends('layouts.admin')
@section('title', 'Nueva Sede')
@section('page-title', 'Nueva Sede')
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Crear Sede</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.sedes.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Estado</label>
                <select name="estado_id" class="form-select" required>
                    <option value="">Seleccione un estado</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->id }}" {{ old('estado_id')==$estado->id ? 'selected' : '' }}>{{ $estado->nombre }}</option>
                    @endforeach
                </select>
                @error('estado_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre de la Sede</label>
                <input type="text" name="nombre_sede" class="form-control" value="{{ old('nombre_sede') }}" required>
                @error('nombre_sede') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Dirección</label>
                <textarea name="direccion" class="form-control" rows="2" required>{{ old('direccion') }}</textarea>
                @error('direccion') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Código Postal</label>
                <input type="text" name="codigo_postal" class="form-control" value="{{ old('codigo_postal') }}">
                @error('codigo_postal') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <button type="submit" class="btn-conapdis">Guardar</button>
            <a href="{{ route('admin.sedes.index') }}" class="btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>
@endsection