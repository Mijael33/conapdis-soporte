@extends('layouts.admin')
@section('title', 'Nuevo Vehículo')
@section('page-title', 'Nuevo Vehículo')
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Registrar Vehículo</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.vehiculos.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Código Inventario</label>
                    <input type="text" name="codigo_inventario" class="form-control" value="{{ old('codigo_inventario') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Placa</label>
                    <input type="text" name="placa" class="form-control" value="{{ old('placa') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Categoría</label>
                    <select name="categoria_vehiculo_id" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_vehiculo_id')==$cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Sede</label>
                    <select name="sede_id" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ old('sede_id')==$sede->id ? 'selected' : '' }}>{{ $sede->nombre_sede }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Marca</label>
                    <input type="text" name="marca" class="form-control" value="{{ old('marca') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Modelo</label>
                    <input type="text" name="modelo" class="form-control" value="{{ old('modelo') }}" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Año</label>
                    <input type="number" name="anio" class="form-control" value="{{ old('anio') }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Color</label>
                    <input type="text" name="color" class="form-control" value="{{ old('color') }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Kilometraje</label>
                    <input type="number" name="kilometraje" class="form-control" value="{{ old('kilometraje', 0) }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Serial Motor</label>
                    <input type="text" name="serial_motor" class="form-control" value="{{ old('serial_motor') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Serial Chasis</label>
                    <input type="text" name="serial_chasis" class="form-control" value="{{ old('serial_chasis') }}">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones') }}</textarea>
            </div>
            <button type="submit" class="btn-conapdis">Guardar</button>
            <a href="{{ route('admin.vehiculos.index') }}" class="btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>
@endsection