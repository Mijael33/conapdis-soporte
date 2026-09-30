@extends('layouts.admin')
@section('title', 'Nuevo Estado')
@section('page-title', 'Nuevo Estado')
@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <form action="{{ route('admin.estados.store') }}" method="POST" id="formEstado">
            @csrf

            {{-- DATOS DEL ESTADO --}}
            <h5 class="fw-bold mb-3" style="color: #1a3b5d;">
                <i class="bi bi-geo-alt me-2"></i>Datos del Estado
            </h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre del Estado *</label>
                    <input type="text" name="nombre" class="form-control rounded-3" value="{{ old('nombre') }}" required placeholder="Ej: Miranda">
                    @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Región *</label>
                    <select name="region" class="form-select rounded-3" required>
                        <option value="">Seleccione</option>
                        @foreach(['Capital','Central','Occidental','Oriental','Los Llanos','Guayana'] as $r)
                            <option value="{{ $r }}" {{ old('region')==$r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                    @error('region') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- SEDES DINÁMICAS --}}
            <h5 class="fw-bold mb-2 mt-4" style="color: #1a3b5d;">
                <i class="bi bi-building me-2"></i>Sedes de este Estado
            </h5>
            <p class="text-muted mb-3" style="font-size: 0.85rem;">
                Agrega las sedes que pertenecen a este estado. Puedes agregar tantas como necesites.
            </p>

            <div class="card border rounded-3 mb-3" style="background: #f8fafc;">
                <div class="card-body">
                    <div id="sedes-container">
                        {{-- Aquí se agregan dinámicamente --}}
                    </div>

                    <button type="button" class="btn btn-outline-conapdis btn-sm" onclick="agregarSede()">
                        <i class="bi bi-plus-lg"></i> Agregar Sede
                    </button>
                </div>
            </div>

            {{-- BOTONES --}}
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn-conapdis px-4">
                    <i class="bi bi-check-lg"></i> Guardar Estado
                </button>
                <a href="{{ route('admin.estados.index') }}" class="btn-outline-conapdis px-4">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let sedeCounter = 0;

function agregarSede(nombre = '', direccion = '', codigoPostal = '') {
    const container = document.getElementById('sedes-container');
    const id = 'sede_' + (++sedeCounter);

    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 sede-row align-items-start';
    row.id = id;
    row.innerHTML = `
        <div class="col-md-3">
            <input type="text" name="sedes[${sedeCounter}][nombre_sede]" class="form-control form-control-sm rounded-3" 
                   placeholder="Nombre de la sede" value="${nombre}" required>
        </div>
        <div class="col-md-5">
            <textarea name="sedes[${sedeCounter}][direccion]" class="form-control form-control-sm rounded-3" 
                      rows="1" placeholder="Dirección" required>${direccion}</textarea>
        </div>
        <div class="col-md-2">
            <input type="text" name="sedes[${sedeCounter}][codigo_postal]" class="form-control form-control-sm rounded-3" 
                   placeholder="C.P." value="${codigoPostal}">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="eliminarSede('${id}')">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(row);
}

function eliminarSede(id) {
    document.getElementById(id).remove();
}

// Al cargar, agregar 1 sede vacía por defecto
document.addEventListener('DOMContentLoaded', function() {
    agregarSede();
});
</script>
@endpush
@endsection