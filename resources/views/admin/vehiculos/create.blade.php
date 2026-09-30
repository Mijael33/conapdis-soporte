@extends('layouts.admin')
@section('title', 'Nuevo Vehículo')
@section('page-title', 'Nuevo Vehículo')
@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <form action="{{ route('admin.vehiculos.store') }}" method="POST">
            @csrf

            <h5 class="fw-bold mb-3" style="color: #1a3b5d;"><i class="bi bi-info-circle me-2"></i>Datos Básicos</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Código Inventario *</label>
                    <input type="text" name="codigo_inventario" class="form-control rounded-3" value="{{ old('codigo_inventario') }}" required>
                    @error('codigo_inventario') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Placa *</label>
                    <input type="text" name="placa" class="form-control rounded-3" value="{{ old('placa') }}" required>
                    @error('placa') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Categoría *</label>
                    <select name="categoria_vehiculo_id" class="form-select rounded-3" required>
                        <option value="">Seleccione</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_vehiculo_id')==$cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Sede *</label>
                    <select name="sede_id" class="form-select rounded-3" required>
                        <option value="">Seleccione</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ old('sede_id')==$sede->id ? 'selected' : '' }}>{{ $sede->nombre_sede }} ({{ $sede->estado->nombre }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h5 class="fw-bold mb-3" style="color: #1a3b5d;"><i class="bi bi-truck me-2"></i>Características del Vehículo</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Marca *</label>
                    <input type="text" name="marca" class="form-control rounded-3" value="{{ old('marca') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Modelo *</label>
                    <input type="text" name="modelo" class="form-control rounded-3" value="{{ old('modelo') }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Año</label>
                    <input type="number" name="anio" class="form-control rounded-3" value="{{ old('anio') }}" min="1900" max="{{ date('Y') + 1 }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Color</label>
                    <input type="text" name="color" class="form-control rounded-3" value="{{ old('color') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Kilometraje</label>
                    <input type="number" name="kilometraje" class="form-control rounded-3" value="{{ old('kilometraje', 0) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Serial Motor</label>
                    <input type="text" name="serial_motor" class="form-control rounded-3" value="{{ old('serial_motor') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Serial Carrocería</label>
                    <input type="text" name="serial_carroceria" class="form-control rounded-3" value="{{ old('serial_carroceria') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Estatus *</label>
                    <select name="estatus" class="form-select rounded-3" required>
                        @foreach(['Disponible','Asignado','En Mantenimiento','Desincorporado'] as $e)
                            <option value="{{ $e }}" {{ old('estatus', 'Disponible')==$e ? 'selected' : '' }}>{{ $e }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h5 class="fw-bold mb-3" style="color: #1a3b5d;"><i class="bi bi-cash-coin me-2"></i>Valoración</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Valor Prudencial (Bs.)</label>
                    <input type="number" step="0.01" min="0" name="valor_prudencial" class="form-control rounded-3" value="{{ old('valor_prudencial') }}" placeholder="Ej: 250000.00">
                    <small class="text-muted">Al menos uno de los dos valores es obligatorio</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Valor de Adquisición (Bs.)</label>
                    <input type="number" step="0.01" min="0" name="valor_adquisicion" class="form-control rounded-3" value="{{ old('valor_adquisicion') }}" placeholder="Ej: 230000.00">
                </div>
            </div>

            <h5 class="fw-bold mb-3" style="color: #1a3b5d;"><i class="bi bi-person-badge me-2"></i>Usuario Asignado</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nombre</label>
                    <input type="text" name="usuario_asignado_nombre" class="form-control rounded-3" value="{{ old('usuario_asignado_nombre') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cédula</label>
                    <input type="text" name="usuario_asignado_cedula" class="form-control rounded-3" value="{{ old('usuario_asignado_cedula') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cargo</label>
                    <input type="text" name="usuario_asignado_cargo" class="form-control rounded-3" value="{{ old('usuario_asignado_cargo') }}">
                </div>
            </div>

            <h5 class="fw-bold mb-3" style="color: #1a3b5d;"><i class="bi bi-chat-left-text me-2"></i>Observaciones</h5>
            <div class="mb-3">
                <textarea name="observaciones" class="form-control rounded-3" rows="3">{{ old('observaciones') }}</textarea>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn-conapdis px-4"><i class="bi bi-check-lg"></i> Guardar Vehículo</button>
                <a href="{{ route('admin.vehiculos.index') }}" class="btn-outline-conapdis px-4">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection