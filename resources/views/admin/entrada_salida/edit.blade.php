@extends('layouts.admin')
@section('title', 'Editar Registro E/S')
@section('page-title', 'Editar Registro de Entrada/Salida')
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Editar Movimiento</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.entrada-salida.update', $registro) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select" required>
                        <option value="Salida" {{ $registro->tipo=='Salida' ? 'selected' : '' }}>Salida</option>
                        <option value="Entrada" {{ $registro->tipo=='Entrada' ? 'selected' : '' }}>Entrada</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tipo de Bien</label>
                    <select name="bien_tipo" class="form-select" required>
                        @foreach(['Tecnologia','BienNacional','Vehiculo','Sonido'] as $t)
                            <option value="{{ $t }}" {{ $registro->bien_tipo==$t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Bien ID</label>
                    <input type="number" name="bien_id" class="form-control" value="{{ $registro->bien_id }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Sede</label>
                    <select name="sede_id" class="form-select" required>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ $registro->sede_id==$sede->id ? 'selected' : '' }}>{{ $sede->nombre_sede }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-conapdis">Actualizar</button>
            <a href="{{ route('admin.entrada-salida.index') }}" class="btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>
@endsection