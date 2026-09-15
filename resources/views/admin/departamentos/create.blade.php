@extends('layouts.admin')
@section('title', 'Nuevo Departamento')
@section('page-title', 'Nuevo Departamento')
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Crear Departamento</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.departamentos.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Sede</label>
                <select name="sede_id" class="form-select" required>
                    <option value="">Seleccione una sede</option>
                    @foreach($sedes as $sede)
                        <option value="{{ $sede->id }}" {{ old('sede_id')==$sede->id ? 'selected' : '' }}>{{ $sede->nombre_sede }} ({{ $sede->estado->nombre }})</option>
                    @endforeach
                </select>
                @error('sede_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre del Departamento</label>
                <input type="text" name="nombre_departamento" class="form-control" value="{{ old('nombre_departamento') }}" required>
                @error('nombre_departamento') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Piso</label>
                    <input type="number" name="piso" class="form-control" value="{{ old('piso') }}">
                    @error('piso') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Extensión Telefónica</label>
                    <input type="text" name="extension_telefonica" class="form-control" value="{{ old('extension_telefonica') }}">
                    @error('extension_telefonica') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>
            <button type="submit" class="btn-conapdis">Guardar</button>
            <a href="{{ route('admin.departamentos.index') }}" class="btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>
@endsection