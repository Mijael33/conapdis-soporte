@extends('layouts.admin')
@section('title', 'Nuevo Equipo')
@section('page-title', 'Nuevo Equipo')
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Registrar Equipo</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.equipos.store') }}" method="POST" id="formEquipo">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Código Inventario Institucional</label>
                    <input type="text" name="codigo_inventario_institucional" class="form-control" value="{{ old('codigo_inventario_institucional') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Serial del Chasis</label>
                    <input type="text" name="serial_chasis" class="form-control" value="{{ old('serial_chasis') }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tipo de Equipo</label>
                    <select name="tipo_equipo_id" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach($tiposEquipos as $tipo)
                            <option value="{{ $tipo->id }}" {{ old('tipo_equipo_id')==$tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Marca</label>
                    <input type="text" name="marca" class="form-control" value="{{ old('marca') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Modelo</label>
                    <input type="text" name="modelo" class="form-control" value="{{ old('modelo') }}" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Departamento</label>
                <select name="departamento_id" class="form-select" required>
                    <option value="">Seleccione</option>
                    @foreach($departamentos as $depto)
                        <option value="{{ $depto->id }}" {{ old('departamento_id')==$depto->id ? 'selected' : '' }}>{{ $depto->nombre_departamento }} ({{ $depto->sede->nombre_sede }})</option>
                    @endforeach
                </select>
            </div>
            <h6 class="fw-bold mt-3">Usuario Asignado</h6>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="usuario_asignado_nombre" class="form-control" value="{{ old('usuario_asignado_nombre') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Cédula</label>
                    <input type="text" name="usuario_asignado_cedula" class="form-control" value="{{ old('usuario_asignado_cedula') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Cargo</label>
                    <input type="text" name="usuario_asignado_cargo" class="form-control" value="{{ old('usuario_asignado_cargo') }}">
                </div>
            </div>

            {{-- Sistemas Operativos --}}
            <h6 class="fw-bold mt-3">Sistemas Operativos</h6>
            <div id="so-container">
                <div class="row so-item border rounded p-3 mb-2">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Nombre del SO</label>
                        <input type="text" name="so_nombre[]" class="form-control" placeholder="Ej: Windows 11 Pro">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Arquitectura</label>
                        <select name="so_arquitectura[]" class="form-select">
                            <option value="">Seleccione</option>
                            <option value="x64">x64</option>
                            <option value="x86">x86</option>
                            <option value="ARM64">ARM64</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Contraseña</label>
                        <input type="text" name="so_password[]" class="form-control" placeholder="Contraseña del SO">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Notas</label>
                        <input type="text" name="so_notas[]" class="form-control" placeholder="Notas adicionales">
                    </div>
                    <div class="col-md-1 mb-2 d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-so"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-conapdis mb-3" id="add-so"><i class="bi bi-plus-lg"></i> Agregar SO</button>

            <div class="mt-3">
                <button type="submit" class="btn-conapdis">Guardar</button>
                <a href="{{ route('admin.equipos.index') }}" class="btn-outline-conapdis">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('add-so').addEventListener('click', function() {
    const container = document.getElementById('so-container');
    const newSo = container.querySelector('.so-item').cloneNode(true);
    newSo.querySelectorAll('input').forEach(input => input.value = '');
    newSo.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
    container.appendChild(newSo);
});

document.getElementById('so-container').addEventListener('click', function(e) {
    if (e.target.closest('.remove-so')) {
        const items = document.querySelectorAll('.so-item');
        if (items.length > 1) {
            e.target.closest('.so-item').remove();
        }
    }
});
</script>
@endpush
@endsection