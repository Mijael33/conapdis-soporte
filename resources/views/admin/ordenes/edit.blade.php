@extends('layouts.admin')
@section('title', 'Editar Orden')
@section('page-title', 'Editar: ' . $ordene->codigo_ticket)
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Diagnóstico y Reparación</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.ordenes.update', $ordene) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Diagnóstico Técnico</label>
                <textarea name="diagnostico_tecnico" class="form-control" rows="3">{{ old('diagnostico_tecnico', $ordene->diagnostico_tecnico) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Acciones Realizadas</label>
                <textarea name="acciones_realizadas" class="form-control" rows="3">{{ old('acciones_realizadas', $ordene->acciones_realizadas) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Estatus Final</label>
                <select name="estatus_final" class="form-select">
                    <option value="">Sin cerrar (Abierta)</option>
                    <option value="Reparado" {{ old('estatus_final', $ordene->estatus_final)=='Reparado' ? 'selected' : '' }}>Reparado</option>
                    <option value="En Espera de Repuesto" {{ old('estatus_final', $ordene->estatus_final)=='En Espera de Repuesto' ? 'selected' : '' }}>En Espera de Repuesto</option>
                    <option value="Remitido a Sede Central" {{ old('estatus_final', $ordene->estatus_final)=='Remitido a Sede Central' ? 'selected' : '' }}>Remitido a Sede Central</option>
                    <option value="Irrecuperable" {{ old('estatus_final', $ordene->estatus_final)=='Irrecuperable' ? 'selected' : '' }}>Irrecuperable</option>
                </select>
            </div>
            <button type="submit" class="btn-conapdis">Actualizar</button>
            <a href="{{ route('admin.ordenes.index') }}" class="btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>
@endsection