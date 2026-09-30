@extends('layouts.admin')
@section('title', 'Editar Estado')
@section('page-title', 'Editar Estado')
@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <form action="{{ route('admin.estados.update', $estado) }}" method="POST" id="formEstado">
            @csrf @method('PUT')

            {{-- DATOS DEL ESTADO --}}
            <h5 class="fw-bold mb-3" style="color: #1a3b5d;">
                <i class="bi bi-geo-alt me-2"></i>Datos del Estado
            </h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nombre del Estado *</label>
                    <input type="text" name="nombre" class="form-control rounded-3" value="{{ old('nombre', $estado->nombre) }}" required>
                    @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Región *</label>
                    <select name="region" class="form-select rounded-3" required>
                        @foreach(['Capital','Central','Occidental','Oriental','Los Llanos','Guayana'] as $r)
                            <option value="{{ $r }}" {{ old('region', $estado->region)==$r ? 'selected' : '' }}>{{ $r }}</option>
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
                Administra las sedes que pertenecen a este estado. Puedes agregar, editar o eliminar las que necesites.
            </p>

            <div class="card border rounded-3 mb-3" style="background: #f8fafc;">
                <div class="card-body">
                    <div id="sedes-container">
                        @forelse($estado->sedes as $index => $sede)
                        <div class="row g-2 mb-2 sede-row align-items-start" id="sede_existente_{{ $sede->id }}">
                            <input type="hidden" name="sedes[{{ $index }}][id]" value="{{ $sede->id }}">
                            <div class="col-md-3">
                                <input type="text" name="sedes[{{ $index }}][nombre_sede]" class="form-control form-control-sm rounded-3" 
                                       placeholder="Nombre de la sede" value="{{ $sede->nombre_sede }}" required>
                            </div>
                            <div class="col-md-5">
                                <textarea name="sedes[{{ $index }}][direccion]" class="form-control form-control-sm rounded-3" 
                                          rows="1" placeholder="Dirección" required>{{ $sede->direccion }}</textarea>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="sedes[{{ $index }}][codigo_postal]" class="form-control form-control-sm rounded-3" 
                                       placeholder="C.P." value="{{ $sede->codigo_postal }}">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="eliminarSede('sede_existente_{{ $sede->id }}')" title="Eliminar sede">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted mb-2 small">Este estado no tiene sedes registradas.</p>
                        @endforelse
                    </div>

                    <button type="button" class="btn btn-outline-conapdis btn-sm mt-2" onclick="agregarSede()">
                        <i class="bi bi-plus-lg"></i> Agregar Sede
                    </button>
                </div>
            </div>

            <div class="alert" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; border-radius: 8px;">
                <small>
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Nota:</strong> Al eliminar una sede con equipos, usuarios o bienes asociados, el sistema bloqueará la operación para proteger la integridad de los datos.
                </small>
            </div>

            {{-- BOTONES --}}
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn-conapdis px-4">
                    <i class="bi bi-check-lg"></i> Actualizar Estado
                </button>
                <a href="{{ route('admin.estados.index') }}" class="btn-outline-conapdis px-4">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let sedeCounter = {{ $estado->sedes->count() }};

function agregarSede() {
    sedeCounter++;
    const container = document.getElementById('sedes-container');
    const id = 'sede_nueva_' + sedeCounter;

    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 sede-row align-items-start';
    row.id = id;
    row.innerHTML = `
        <div class="col-md-3">
            <input type="text" name="sedes[${sedeCounter}][nombre_sede]" class="form-control form-control-sm rounded-3" 
                   placeholder="Nombre de la sede" required>
        </div>
        <div class="col-md-5">
            <textarea name="sedes[${sedeCounter}][direccion]" class="form-control form-control-sm rounded-3" 
                      rows="1" placeholder="Dirección" required></textarea>
        </div>
        <div class="col-md-2">
            <input type="text" name="sedes[${sedeCounter}][codigo_postal]" class="form-control form-control-sm rounded-3" 
                   placeholder="C.P.">
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
    const row = document.getElementById(id);
    if (row) {
        if (confirm('¿Eliminar esta sede? Si tenía dependencias asociadas, se bloqueará el guardado.')) {
            row.remove();
        }
    }
}
</script>
@endpush
@endsection