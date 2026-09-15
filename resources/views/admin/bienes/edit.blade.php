@extends('layouts.admin')
@section('title', 'Editar Bien')
@section('page-title', 'Editar Bien Nacional')
@section('content')
<div class="container-fluid">
    <h2 class="mb-4" style="color: #1a3b5d; font-weight: 700;">Editar: {{ $bien->codigo_inventario }}</h2>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <form action="{{ route('admin.bienes.update', $bien) }}" method="POST">
            @csrf @method('PUT')

            {{-- DATOS BÁSICOS --}}
            <h5 class="fw-bold mb-3" style="color: #1a3b5d;"><i class="bi bi-info-circle me-2"></i>Datos Básicos</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Código Inventario *</label>
                    <input type="text" name="codigo_inventario" class="form-control rounded-3" value="{{ old('codigo_inventario', $bien->codigo_inventario) }}" required>
                    @error('codigo_inventario') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Categoría *</label>
                    <select name="categoria_bien_id" class="form-select rounded-3" required>
                        <option value="">Seleccione</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_bien_id', $bien->categoria_bien_id)==$cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                    @error('categoria_bien_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Sede *</label>
                    <select name="sede_id" class="form-select rounded-3" required>
                        <option value="">Seleccione</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ old('sede_id', $bien->sede_id)==$sede->id ? 'selected' : '' }}>{{ $sede->nombre_sede }} ({{ $sede->estado->nombre }})</option>
                        @endforeach
                    </select>
                    @error('sede_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Descripción *</label>
                    <textarea name="descripcion" class="form-control rounded-3" rows="2" required>{{ old('descripcion', $bien->descripcion) }}</textarea>
                    @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- CARACTERÍSTICAS FÍSICAS --}}
            <h5 class="fw-bold mb-3" style="color: #1a3b5d;"><i class="bi bi-box me-2"></i>Características del Bien</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Marca</label>
                    <input type="text" name="marca" class="form-control rounded-3" value="{{ old('marca', $bien->marca) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Modelo</label>
                    <input type="text" name="modelo" class="form-control rounded-3" value="{{ old('modelo', $bien->modelo) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Serial</label>
                    <input type="text" name="serial" class="form-control rounded-3" value="{{ old('serial', $bien->serial) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Estatus *</label>
                    <select name="estatus" class="form-select rounded-3" required>
                        @foreach(['Disponible','Asignado','En Mantenimiento','Desincorporado'] as $e)
                            <option value="{{ $e }}" {{ old('estatus', $bien->estatus)==$e ? 'selected' : '' }}>{{ $e }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Color</label>
                    <input type="text" name="color" class="form-control rounded-3" value="{{ old('color', $bien->color) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Material</label>
                    <input type="text" name="material" class="form-control rounded-3" value="{{ old('material', $bien->material) }}">
                </div>
            </div>

            {{-- DATOS DE ADQUISICIÓN --}}
            <h5 class="fw-bold mb-3" style="color: #1a3b5d;"><i class="bi bi-cash-coin me-2"></i>Datos de Adquisición</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Valor de Adquisición (Bs.)</label>
                    <input type="number" step="0.01" name="valor_adquisicion" class="form-control rounded-3" value="{{ old('valor_adquisicion', $bien->valor_adquisicion) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Fecha de Adquisición</label>
                    <input type="date" name="fecha_adquisicion" class="form-control rounded-3" value="{{ old('fecha_adquisicion', $bien->fecha_adquisicion ? $bien->fecha_adquisicion->format('Y-m-d') : '') }}">
                </div>
            </div>

            {{-- USUARIO ASIGNADO --}}
            <h5 class="fw-bold mb-3" style="color: #1a3b5d;"><i class="bi bi-person-badge me-2"></i>Usuario Asignado</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nombre</label>
                    <input type="text" name="usuario_asignado_nombre" class="form-control rounded-3" value="{{ old('usuario_asignado_nombre', $bien->usuario_asignado_nombre) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cédula</label>
                    <input type="text" name="usuario_asignado_cedula" class="form-control rounded-3" value="{{ old('usuario_asignado_cedula', $bien->usuario_asignado_cedula) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cargo</label>
                    <input type="text" name="usuario_asignado_cargo" class="form-control rounded-3" value="{{ old('usuario_asignado_cargo', $bien->usuario_asignado_cargo) }}">
                </div>
            </div>

            {{-- OBSERVACIONES --}}
            <h5 class="fw-bold mb-3" style="color: #1a3b5d;"><i class="bi bi-chat-left-text me-2"></i>Observaciones</h5>
            <div class="mb-3">
                <textarea name="observaciones" class="form-control rounded-3" rows="3">{{ old('observaciones', $bien->observaciones) }}</textarea>
            </div>

            {{-- BOTONES --}}
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn-conapdis px-4"><i class="bi bi-check-lg"></i> Actualizar Bien</button>
                <a href="{{ route('admin.bienes.index') }}" class="btn-outline-conapdis px-4">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection