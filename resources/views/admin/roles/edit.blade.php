@extends('layouts.admin')
@section('title', 'Editar Rol')
@section('page-title', 'Editar Rol')
@section('content')
<div class="container-fluid">
    <h2 class="mb-4" style="color: #1a3b5d; font-weight: 700;">Editar Rol: {{ $role->name }}</h2>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <form action="{{ route('admin.roles.update', $role) }}" method="POST" id="formRol">
            @csrf @method('PUT')

            {{-- DATOS BÁSICOS --}}
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nombre del Rol *</label>
                    <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $role->name) }}" required>
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold d-block">Modo de Permisos</label>
                    <div class="form-check form-switch mt-2">
                        <input type="checkbox" id="es_admin" class="form-check-input" onchange="toggleAdmin()" style="width: 50px; height: 25px;" {{ $role->name === 'Administrador' ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold ms-2" for="es_admin">👑 Es Administrador Total (todos los permisos)</label>
                    </div>
                    <small class="text-muted d-block mt-1">Si activas esto, el rol tendrá acceso completo al sistema.</small>
                </div>
            </div>

            {{-- PERMISOS POR MÓDULO --}}
            <div id="permisosSection">
                <hr class="my-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h5 class="mb-1" style="color: #1a3b5d; font-weight: 700;">Permisos por Módulo</h5>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">Selecciona las acciones permitidas para cada módulo.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="seleccionarTodos()">
                            <i class="bi bi-check-all"></i> Todos
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="deseleccionarTodos()">
                            <i class="bi bi-x-circle"></i> Ninguno
                        </button>
                    </div>
                </div>

                @foreach($modulos as $moduloKey => $moduloNombre)
                    @if(isset($permisosPorModulo[$moduloKey]) && $permisosPorModulo[$moduloKey]->count() > 0)
                    <div class="card border-0 shadow-sm rounded-3 mb-3">
                        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-2 px-3">
                            <h6 class="mb-0 fw-bold" style="color: #003097;">
                                <i class="bi bi-folder2-open me-2"></i>{{ $moduloNombre }}
                            </h6>
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="toggle_{{ $moduloKey }}" onchange="toggleModulo('{{ $moduloKey }}')">
                                <label class="form-check-label small text-muted" for="toggle_{{ $moduloKey }}">Seleccionar todo</label>
                            </div>
                        </div>
                        <div class="card-body py-3 px-3">
                            <div class="row g-2">
                                @foreach($permisosPorModulo[$moduloKey] as $permiso)
                                <div class="col-lg-3 col-md-4 col-6">
                                    <div class="form-check p-2 rounded-2 border" style="background: #f8fafc;">
                                        <input type="checkbox" 
                                               name="permissions[]" 
                                               value="{{ $permiso->id }}" 
                                               id="perm_{{ $permiso->id }}"
                                               class="form-check-input modulo-check-{{ $moduloKey }}"
                                               {{ in_array($permiso->id, $rolePermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium" for="perm_{{ $permiso->id }}" style="font-size: 0.85rem;">
                                            @php
                                                $accion = last(explode('.', $permiso->name));
                                                $iconos = [
                                                    'ver' => '<i class="bi bi-eye text-primary me-1"></i>',
                                                    'crear' => '<i class="bi bi-plus-circle text-success me-1"></i>',
                                                    'editar' => '<i class="bi bi-pencil text-warning me-1"></i>',
                                                    'eliminar' => '<i class="bi bi-trash text-danger me-1"></i>',
                                                    'importar' => '<i class="bi bi-upload text-info me-1"></i>',
                                                    'exportar' => '<i class="bi bi-download text-secondary me-1"></i>',
                                                    'generar' => '<i class="bi bi-file-pdf text-danger me-1"></i>',
                                                ];
                                            @endphp
                                            {!! $iconos[$accion] ?? '<i class="bi bi-circle me-1"></i>' !!}
                                            {{ ucfirst($accion) }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>

            {{-- BOTONES --}}
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn-conapdis px-4"><i class="bi bi-check-lg"></i> Actualizar Rol</button>
                <a href="{{ route('admin.roles.index') }}" class="btn-outline-conapdis px-4">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleAdmin() {
    const isAdmin = document.getElementById('es_admin').checked;
    const permisosSection = document.getElementById('permisosSection');
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');

    if (isAdmin) {
        permisosSection.style.display = 'none';
        checkboxes.forEach(cb => cb.checked = true);
    } else {
        permisosSection.style.display = 'block';
    }
}

// Al cargar, si es Administrador, ocultar sección
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('es_admin').checked) {
        document.getElementById('permisosSection').style.display = 'none';
    }

    // Sincronizar toggles de módulo con el estado actual
    document.querySelectorAll('input[type="checkbox"][id^="toggle_"]').forEach(function(toggle) {
        const moduloKey = toggle.id.replace('toggle_', '');
        const checkboxes = document.querySelectorAll('.modulo-check-' + moduloKey);
        if (checkboxes.length > 0) {
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            toggle.checked = allChecked;
        }
    });
});

function toggleModulo(moduloKey) {
    const toggle = document.getElementById('toggle_' + moduloKey);
    const checkboxes = document.querySelectorAll('.modulo-check-' + moduloKey);
    checkboxes.forEach(cb => cb.checked = toggle.checked);
}

function seleccionarTodos() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = true);
    document.querySelectorAll('input[type="checkbox"][id^="toggle_"]').forEach(t => t.checked = true);
}

function deseleccionarTodos() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);
    document.querySelectorAll('input[type="checkbox"][id^="toggle_"]').forEach(t => t.checked = false);
}
</script>
@endpush
@endsection