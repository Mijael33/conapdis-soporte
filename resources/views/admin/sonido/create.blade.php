@extends('layouts.admin')
@section('title', 'Nuevo Equipo de Sonido')
@section('page-title', 'Nuevo Equipo de Sonido')
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Registrar Equipo de Sonido</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.sonido.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Código Inventario</label>
                    <input type="text" name="codigo_inventario" class="form-control" value="{{ old('codigo_inventario') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Serial</label>
                    <input type="text" name="serial" class="form-control" value="{{ old('serial') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Categoría</label>
                    <select name="categoria_sonido_id" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_sonido_id')==$cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
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
                <div class="col-md-4 mb-3">
                    <label class="form-label">Marca</label>
                    <input type="text" name="marca" class="form-control" value="{{ old('marca') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Modelo</label>
                    <input type="text" name="modelo" class="form-control" value="{{ old('modelo') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Potencia</label>
                    <input type="text" name="potencia" class="form-control" value="{{ old('potencia') }}" placeholder="Ej: 2000W PMPO">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones') }}</textarea>
            </div>
            <button type="submit" class="btn-conapdis">Guardar</button>
            <a href="{{ route('admin.sonido.index') }}" class="btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>
@endsection