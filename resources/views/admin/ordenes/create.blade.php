@extends('layouts.admin')
@section('title', 'Nueva Orden')
@section('page-title', 'Nueva Orden de Servicio')
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Abrir Ticket de Soporte</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.ordenes.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Equipo</label>
                <select name="equipo_id" class="form-select" required>
                    <option value="">Seleccione un equipo</option>
                    @foreach($equipos as $equipo)
                        <option value="{{ $equipo->id }}" {{ old('equipo_id')==$equipo->id ? 'selected' : '' }}>{{ $equipo->codigo_inventario_institucional }} - {{ $equipo->marca }} {{ $equipo->modelo }}</option>
                    @endforeach
                </select>
                @error('equipo_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Técnico Asignado</label>
                <select name="tecnico_id" class="form-select" required>
                    <option value="">Seleccione un técnico</option>
                    @foreach($tecnicos as $tecnico)
                        <option value="{{ $tecnico->id }}" {{ old('tecnico_id')==$tecnico->id ? 'selected' : '' }}>{{ $tecnico->name }}</option>
                    @endforeach
                </select>
                @error('tecnico_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Problema Reportado por el Usuario</label>
                <textarea name="problema_reportado_usuario" class="form-control" rows="3" required>{{ old('problema_reportado_usuario') }}</textarea>
                @error('problema_reportado_usuario') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <button type="submit" class="btn-conapdis">Crear Orden</button>
            <a href="{{ route('admin.ordenes.index') }}" class="btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>
@endsection