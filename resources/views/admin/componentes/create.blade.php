@extends('layouts.admin')
@section('title', 'Nuevo Componente')
@section('page-title', 'Nuevo Componente')
@section('content')
<div class="container-fluid">
    <h2 class="mb-4" style="color: #1a3b5d; font-weight: 700;">Registrar Componente</h2>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <form action="{{ route('admin.componentes.store') }}" method="POST">
            @csrf

            {{-- DATOS BÁSICOS --}}
            <h5 class="fw-bold mb-3" style="color: #1a3b5d;"><i class="bi bi-info-circle me-2"></i>Datos Básicos</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Sede *</label>
                    <select name="sede_id" class="form-select rounded-3" required>
                        <option value="">Seleccione sede</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ old('sede_id')==$sede->id ? 'selected' : '' }}>{{ $sede->nombre_sede }} ({{ $sede->estado->nombre }})</option>
                        @endforeach
                    </select>
                    @error('sede_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Categoría *</label>
                    <select name="categoria_componente_id" class="form-select rounded-3" required>
                        <option value="">Seleccione categoría</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_componente_id')==$cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                    @error('categoria_componente_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Serial Único *</label>
                    <input type="text" name="serial_unico" class="form-control rounded-3" value="{{ old('serial_unico') }}" required>
                    @error('serial_unico') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Marca *</label>
                    <input type="text" name="marca" class="form-control rounded-3" value="{{ old('marca') }}" required>
                    @error('marca') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Modelo *</label>
                    <input type="text" name="modelo" class="form-control rounded-3" value="{{ old('modelo') }}" required>
                    @error('modelo') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- CARACTERÍSTICAS TÉCNICAS DINÁMICAS --}}
            <h5 class="fw-bold mb-2" style="color: #1a3b5d;">
                <i class="bi bi-cpu me-2"></i>Características Técnicas
            </h5>
            <p class="text-muted mb-3" style="font-size: 0.85rem;">Agrega pares de características específicas del componente (ej: capacidad, tipo, frecuencia).</p>

            <div class="card border rounded-3 mb-3" style="background: #f8fafc;">
                <div class="card-body">
                    <div id="caracteristicas-container">
                        {{-- Aquí se agregan dinámicamente --}}
                    </div>

                    <button type="button" class="btn btn-outline-conapdis btn-sm" onclick="agregarCaracteristica()">
                        <i class="bi bi-plus-lg"></i> Agregar Característica
                    </button>
                </div>
            </div>

            {{-- Input hidden que guardará el JSON --}}
            <input type="hidden" name="caracteristicas_tecnicas" id="caracteristicas_json" value="{{ old('caracteristicas_tecnicas') }}">

            {{-- OBSERVACIONES --}}
            <h5 class="fw-bold mb-3 mt-4" style="color: #1a3b5d;"><i class="bi bi-chat-left-text me-2"></i>Observaciones</h5>
            <div class="mb-3">
                <textarea name="observaciones" class="form-control rounded-3" rows="3">{{ old('observaciones') }}</textarea>
            </div>

            {{-- BOTONES --}}
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn-conapdis px-4"><i class="bi bi-check-lg"></i> Guardar Componente</button>
                <a href="{{ route('admin.componentes.index') }}" class="btn-outline-conapdis px-4">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Características iniciales desde el old() o array vacío
let caracteristicas = [];

// Cargar características existentes
try {
    const oldValue = document.getElementById('caracteristicas_json').value;
    if (oldValue) {
        caracteristicas = JSON.parse(oldValue);
    }
} catch(e) {
    caracteristicas = [];
}

// Función para agregar una nueva fila vacía
function agregarCaracteristica(clave = '', valor = '') {
    const container = document.getElementById('caracteristicas-container');
    const id = 'carac_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);

    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 caracteristica-row align-items-center';
    row.id = id;
    row.innerHTML = `
        <div class="col-md-4">
            <input type="text" class="form-control form-control-sm rounded-3 clave-input" 
                   placeholder="Ej: capacidad" value="${clave}" oninput="actualizarJSON()">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form-control-sm rounded-3 valor-input" 
                   placeholder="Ej: 16GB" value="${valor}" oninput="actualizarJSON()">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="eliminarCaracteristica('${id}')">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(row);
}

// Función para eliminar una fila
function eliminarCaracteristica(id) {
    document.getElementById(id).remove();
    actualizarJSON();
}

// Función para actualizar el JSON en el campo hidden
function actualizarJSON() {
    const rows = document.querySelectorAll('.caracteristica-row');
    const resultado = {};

    rows.forEach(row => {
        const clave = row.querySelector('.clave-input').value.trim();
        const valor = row.querySelector('.valor-input').value.trim();
        if (clave && valor) {
            resultado[clave] = valor;
        }
    });

    document.getElementById('caracteristicas_json').value = JSON.stringify(resultado);
}

// Cargar características existentes al iniciar
document.addEventListener('DOMContentLoaded', function() {
    if (caracteristicas && Object.keys(caracteristicas).length > 0) {
        for (const [clave, valor] of Object.entries(caracteristicas)) {
            agregarCaracteristica(clave, valor);
        }
    } else {
        // Por defecto, agregar 2 filas vacías
        agregarCaracteristica();
        agregarCaracteristica();
    }
});
</script>
@endpush
@endsection