@extends('layouts.admin')
@section('title', 'Editar Componente')
@section('page-title', 'Editar Componente')
@section('content')
<div class="container-fluid">
    <h2 class="mb-4" style="color: #1a3b5d; font-weight: 700;">Editar: {{ $componente->serial_unico }}</h2>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <form action="{{ route('admin.componentes.update', $componente) }}" method="POST">
            @csrf @method('PUT')

            {{-- DATOS BÁSICOS --}}
            <h5 class="fw-bold mb-3" style="color: #1a3b5d;"><i class="bi bi-info-circle me-2"></i>Datos Básicos</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Sede *</label>
                    <select name="sede_id" class="form-select rounded-3" required>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ old('sede_id', $componente->sede_id)==$sede->id ? 'selected' : '' }}>{{ $sede->nombre_sede }} ({{ $sede->estado->nombre }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Categoría *</label>
                    <select name="categoria_componente_id" class="form-select rounded-3" required>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_componente_id', $componente->categoria_componente_id)==$cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Estatus *</label>
                    <select name="estatus" class="form-select rounded-3" required>
                        @foreach(['Disponible','Instalado','En Revisión','Desincorporado'] as $e)
                            <option value="{{ $e }}" {{ old('estatus', $componente->estatus)==$e ? 'selected' : '' }}>{{ $e }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Serial Único *</label>
                    <input type="text" name="serial_unico" class="form-control rounded-3" value="{{ old('serial_unico', $componente->serial_unico) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Marca *</label>
                    <input type="text" name="marca" class="form-control rounded-3" value="{{ old('marca', $componente->marca) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Modelo *</label>
                    <input type="text" name="modelo" class="form-control rounded-3" value="{{ old('modelo', $componente->modelo) }}" required>
                </div>
            </div>

            {{-- CARACTERÍSTICAS TÉCNICAS DINÁMICAS --}}
            <h5 class="fw-bold mb-2" style="color: #1a3b5d;">
                <i class="bi bi-cpu me-2"></i>Características Técnicas
            </h5>
            <p class="text-muted mb-3" style="font-size: 0.85rem;">Agrega pares clave-valor para las características del componente.</p>

            <div class="card border rounded-3 mb-3" style="background: #f8fafc;">
                <div class="card-body">
                    <div id="caracteristicas-container"></div>

                    <button type="button" class="btn btn-outline-conapdis btn-sm" onclick="agregarCaracteristica()">
                        <i class="bi bi-plus-lg"></i> Agregar Característica
                    </button>
                </div>
            </div>

            <input type="hidden" name="caracteristicas_tecnicas" id="caracteristicas_json" value="">

            {{-- OBSERVACIONES --}}
            <h5 class="fw-bold mb-3 mt-4" style="color: #1a3b5d;"><i class="bi bi-chat-left-text me-2"></i>Observaciones</h5>
            <div class="mb-3">
                <textarea name="observaciones" class="form-control rounded-3" rows="3">{{ old('observaciones', $componente->observaciones) }}</textarea>
            </div>

            {{-- BOTONES --}}
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn-conapdis px-4"><i class="bi bi-check-lg"></i> Actualizar Componente</button>
                <a href="{{ route('admin.componentes.index') }}" class="btn-outline-conapdis px-4">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Características existentes del componente
let caracteristicas = @json($componente->caracteristicas_tecnicas ?? []);

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
    actualizarJSON();
}

function eliminarCaracteristica(id) {
    document.getElementById(id).remove();
    actualizarJSON();
}

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

document.addEventListener('DOMContentLoaded', function() {
    if (caracteristicas && Object.keys(caracteristicas).length > 0) {
        for (const [clave, valor] of Object.entries(caracteristicas)) {
            agregarCaracteristica(clave, valor);
        }
    } else {
        agregarCaracteristica();
        agregarCaracteristica();
    }
});
</script>
@endpush
@endsection