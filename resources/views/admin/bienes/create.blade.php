@extends('layouts.admin')
@section('title', 'Nuevo Bien')
@section('page-title', 'Nuevo Bien Nacional')
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Registrar Bien</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.bienes.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Código Inventario</label>
                    <input type="text" name="codigo_inventario" class="form-control" value="{{ old('codigo_inventario') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Categoría</label>
                    <select name="categoria_bien_id" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_bien_id')==$cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Sede</label>
                    <select name="sede_id" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ old('sede_id')==$sede->id ? 'selected' : '' }}>{{ $sede->nombre_sede }} ({{ $sede->estado->nombre }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="2" required>{{ old('descripcion') }}</textarea>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Marca</label>
                    <input type="text" name="marca" class="form-control" value="{{ old('marca') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Modelo</label>
                    <input type="text" name="modelo" class="form-control" value="{{ old('modelo') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Serial</label>
                    <input type="text" name="serial" class="form-control" value="{{ old('serial') }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Color</label>
                    <input type="text" name="color" class="form-control" value="{{ old('color') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Material</label>
                    <input type="text" name="material" class="form-control" value="{{ old('material') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Valor Adquisición (Bs.)</label>
                    <input type="number" step="0.01" name="valor_adquisicion" class="form-control" value="{{ old('valor_adquisicion') }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Fecha Adquisición</label>
                    <input type="date" name="fecha_adquisicion" class="form-control" value="{{ old('fecha_adquisicion') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Usuario Asignado</label>
                    <input type="text" name="usuario_asignado_nombre" class="form-control" value="{{ old('usuario_asignado_nombre') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Cédula</label>
                    <input type="text" name="usuario_asignado_cedula" class="form-control" value="{{ old('usuario_asignado_cedula') }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones') }}</textarea>
            </div>
            <button type="submit" class="btn-conapdis">Guardar</button>
            <a href="{{ route('admin.bienes.index') }}" class="btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>
@endsection