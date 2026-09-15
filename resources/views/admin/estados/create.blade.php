@extends('layouts.admin')
@section('title', 'Nuevo Estado')
@section('page-title', 'Nuevo Estado')
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Crear Estado</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.estados.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nombre del Estado</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Región</label>
                <select name="region" class="form-select" required>
                    <option value="">Seleccione</option>
                    @foreach(['Capital','Central','Occidental','Oriental','Los Llanos','Guayana'] as $r)
                        <option value="{{ $r }}" {{ old('region')==$r ? 'selected' : '' }}>{{ $r }}</option>
                    @endforeach
                </select>
                @error('region') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <button type="submit" class="btn-conapdis">Guardar</button>
            <a href="{{ route('admin.estados.index') }}" class="btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>
@endsection