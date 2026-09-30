@extends('layouts.admin')
@section('title', 'Editar Equipo')
@section('page-title', 'Editar Equipo')
@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <form action="{{ route('admin.equipos.update', $equipo) }}" method="POST" id="formEquipo">
            @csrf @method('PUT')

            <h5 class="fw-bold mb-3" style="color: #1a3b5d;"><i class="bi bi-info-circle me-2"></i>Datos Básicos</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Código Inventario *</label>
                    <input type="text" name="codigo_inventario_institucional" class="form-control rounded-3" value="{{ old('codigo_inventario_institucional', $equipo->codigo_inventario_institucional) }}" required>
                    @error('codigo_inventario_institucional') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Serial Chasis</label>
                    <input type="text" name="serial_chasis" class="form-control rounded-3" value="{{ old('serial_chasis', $equipo->serial_chasis) }}">
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tipo *</label>
                    <select name="tipo_equipo_id" class="form-select rounded-3" required>
                        @foreach($tiposEquipos as $tipo)
                            <option value="{{ $tipo->id }}" {{ old('tipo_equipo_id', $equipo->tipo_equipo_id)==$tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Marca *</label>
                    <input type="text" name="marca" class="form-control rounded-3" value="{{ old('marca', $equipo->marca) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Modelo *</label>
                    <input type="text" name="modelo" class="form-control rounded-3" value="{{ old('modelo', $equipo->modelo) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Estatus *</label>
                    <select name="estatus_general" class="form-select rounded-3" required>
                        @foreach(['Operativo','En Mantenimiento','Inoperativo','Donado/Desincorporado'] as $e)
                            <option value="{{ $e }}" {{ old('estatus_general', $equipo->estatus_general)==$e ? 'selected' : '' }}>{{ $e }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Sede *</label>
                    <select name="sede_id" class="form-select rounded-3" required>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ old('sede_id', $equipo->sede_id)==$sede->id ? 'selected' : '' }}>{{ $sede->nombre_sede }} ({{ $sede->estado->nombre }})</option>
                        @endforeach
                    </select>
                    @error('sede_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- VALORACIÓN --}}
            <h5 class="fw-bold mb-3 mt-4" style="color: #1a3b5d;"><i class="bi bi-cash-coin me-2"></i>Valoración</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Valor Prudencial (Bs.)</label>
                    <input type="number" step="0.01" min="0" name="valor_prudencial" class="form-control rounded-3" value="{{ old('valor_prudencial', $equipo->valor_prudencial) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Valor de Adquisición (Bs.)</label>
                    <input type="number" step="0.01" min="0" name="valor_adquisicion" class="form-control rounded-3" value="{{ old('valor_adquisicion', $equipo->valor_adquisicion) }}">
                </div>
            </div>

            <h5 class="fw-bold mb-3 mt-4" style="color: #1a3b5d;"><i class="bi bi-person-badge me-2"></i>Usuario Asignado</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nombre</label>
                    <input type="text" name="usuario_asignado_nombre" class="form-control rounded-3" value="{{ old('usuario_asignado_nombre', $equipo->usuario_asignado_nombre) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cédula</label>
                    <input type="text" name="usuario_asignado_cedula" class="form-control rounded-3" value="{{ old('usuario_asignado_cedula', $equipo->usuario_asignado_cedula) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cargo</label>
                    <input type="text" name="usuario_asignado_cargo" class="form-control rounded-3" value="{{ old('usuario_asignado_cargo', $equipo->usuario_asignado_cargo) }}">
                </div>
            </div>

            <h5 class="fw-bold mb-3 mt-4" style="color: #1a3b5d;"><i class="bi bi-microsoft me-2"></i>Sistemas Operativos</h5>
            <div id="so-container">
                @if($equipo->sistemasOperativos->count() > 0)
                    @foreach($equipo->sistemasOperativos as $index => $so)
                    <div class="row so-item border rounded p-3 mb-2">
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Nombre del SO</label>
                            <input type="text" name="so_nombre[]" class="form-control" value="{{ $so->nombre }}" placeholder="Ej: Windows 11 Pro">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">Arquitectura</label>
                            <select name="so_arquitectura[]" class="form-select">
                                <option value="">Seleccione</option>
                                <option value="x64" {{ $so->arquitectura == 'x64' ? 'selected' : '' }}>x64</option>
                                <option value="x86" {{ $so->arquitectura == 'x86' ? 'selected' : '' }}>x86</option>
                                <option value="ARM64" {{ $so->arquitectura == 'ARM64' ? 'selected' : '' }}>ARM64</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Contraseña</label>
                            <input type="text" name="so_password[]" class="form-control" placeholder="Dejar vacío para no cambiar" value="">
                            <small class="text-muted">Dejar vacío para mantener la actual</small>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Notas</label>
                            <input type="text" name="so_notas[]" class="form-control" value="{{ $so->notas }}" placeholder="Notas adicionales">
                        </div>
                        <div class="col-md-1 mb-2 d-flex align-items-end">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-so"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                @else
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
                @endif
            </div>
            <button type="button" class="btn btn-sm btn-outline-conapdis mb-3" id="add-so"><i class="bi bi-plus-lg"></i> Agregar SO</button>

            <div class="mt-3">
                <button type="submit" class="btn-conapdis">Actualizar</button>
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