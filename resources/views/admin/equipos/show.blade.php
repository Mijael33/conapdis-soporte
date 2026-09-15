@extends('layouts.admin')
@section('title', 'Detalle Equipo')
@section('page-title', 'Ficha del Equipo')

@section('content')
<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold">{{ $equipo->codigo_inventario_institucional }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.equipos.pdf', $equipo) }}" class="btn-conapdis btn-sm" target="_blank">
                        <i class="bi bi-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('admin.equipos.pegatina', $equipo) }}" class="btn-outline-conapdis btn-sm" target="_blank">
                        <i class="bi bi-tag"></i> Pegatina
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr><th class="w-25">Código Inventario</th><td class="fw-bold">{{ $equipo->codigo_inventario_institucional }}</td></tr>
                    <tr><th>Serial Chasis</th><td>{{ $equipo->serial_chasis ?? 'N/A' }}</td></tr>
                    <tr><th>Tipo</th><td>{{ $equipo->tipoEquipo->nombre ?? 'N/A' }}</td></tr>
                    <tr><th>Marca/Modelo</th><td>{{ $equipo->marca }} {{ $equipo->modelo }}</td></tr>
                    <tr><th>Departamento</th><td>{{ $equipo->departamento->nombre_departamento ?? 'N/A' }} - {{ $equipo->departamento->sede->nombre_sede ?? 'N/A' }} ({{ $equipo->departamento->sede->estado->nombre ?? 'N/A' }})</td></tr>
                    <tr><th>Estatus</th><td>
                        @if($equipo->estatus_general=='Operativo')<span class="badge badge-operativo">Operativo</span>
                        @elseif($equipo->estatus_general=='En Mantenimiento')<span class="badge badge-mantenimiento">En Mantenimiento</span>
                        @else<span class="badge badge-inoperativo">Inoperativo</span>@endif
                    </td></tr>
                    <tr><th>Usuario Asignado</th><td>{{ $equipo->usuario_asignado_nombre ?? 'No asignado' }}</td></tr>
                    <tr><th>Cédula</th><td>{{ $equipo->usuario_asignado_cedula ?? 'N/A' }}</td></tr>
                    <tr><th>Cargo</th><td>{{ $equipo->usuario_asignado_cargo ?? 'N/A' }}</td></tr>
                </table>
            </div>
        </div>

        {{-- Sistemas Operativos --}}
        <div class="card mt-3">
            <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-microsoft me-2"></i>Sistemas Operativos</h5></div>
            <div class="card-body">
                @forelse($equipo->sistemasOperativos as $so)
                    <div class="border rounded p-3 mb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $so->nombre }}</strong>
                                @if($so->arquitectura)<span class="badge badge-instalado ms-2">{{ $so->arquitectura }}</span>@endif
                                @if($so->tiene_contrasena)
                                    <span class="badge badge-revision ms-2"><i class="bi bi-lock"></i> Tiene contraseña</span>
                                @else
                                    <span class="badge badge-disponible ms-2">Sin contraseña</span>
                                @endif
                                @if($so->notas)<br><small class="text-muted">{{ $so->notas }}</small>@endif
                            </div>
                            @if($so->tiene_contrasena)
                                <button class="btn btn-sm btn-outline-conapdis ver-password" data-so-id="{{ $so->id }}" title="Ver contraseña">
                                    <i class="bi bi-eye"></i> Ver contraseña
                                </button>
                            @endif
                        </div>
                        <div class="password-display mt-2" id="password-{{ $so->id }}" style="display:none;">
                            <div class="alert alert-info">
                                <strong><i class="bi bi-key"></i> Contraseña:</strong> <span class="password-text fw-bold"></span>
                                <br><small class="text-muted"><i class="bi bi-shield-check"></i> Este acceso ha sido registrado en la bitácora.</small>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">No hay sistemas operativos registrados</p>
                @endforelse
            </div>
        </div>

        {{-- Historial de Órdenes --}}
        <div class="card mt-3">
            <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-tools me-2"></i>Historial de Reparaciones</h5></div>
            <div class="card-body">
                @forelse($equipo->ordenesServicio as $orden)
                    <div class="border-bottom pb-2 mb-2">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $orden->codigo_ticket }}</strong>
                            <small class="text-muted">{{ $orden->fecha_inicio ? $orden->fecha_inicio->format('d/m/Y') : 'N/A' }}</small>
                        </div>
                        <p class="mb-1"><strong>Problema:</strong> {{ Str::limit($orden->problema_reportado_usuario, 80) }}</p>
                        <p class="mb-1"><strong>Diagnóstico:</strong> {{ $orden->diagnostico_tecnico ?? 'Pendiente' }}</p>
                        <span class="badge {{ $orden->estatus_final == 'Reparado' ? 'badge-operativo' : 'badge-revision' }}">{{ $orden->estatus_final ?? 'Abierta' }}</span>
                        <small class="text-muted ms-2">Técnico: {{ $orden->tecnico->name ?? 'N/A' }}</small>
                    </div>
                @empty
                    <p class="text-muted">Sin órdenes de servicio</p>
                @endforelse
            </div>
        </div>

        {{-- Bitácora --}}
        <div class="card mt-3">
            <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Bitácora de Cambios</h5></div>
            <div class="card-body">
                @forelse($equipo->bitacoras as $bit)
                    <div class="border-bottom pb-2 mb-2">
                        <small class="text-muted">{{ $bit->fecha_registro->format('d/m/Y H:i') }}</small>
                        <span class="badge badge-instalado ms-2">{{ str_replace('_', ' ', $bit->accion) }}</span>
                        <strong class="ms-2">{{ $bit->usuario->name ?? 'N/A' }}</strong>
                        <p class="mb-0 mt-1">{{ $bit->descripcion_detallada }}</p>
                    </div>
                @empty
                    <p class="text-muted">Sin registros en bitácora</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Sidebar: Componentes --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-memory me-2"></i>Componentes</h5>
                <a href="{{ route('admin.equipos.asignar-componente', $equipo) }}" class="btn-conapdis btn-sm"><i class="bi bi-plus-lg"></i> Agregar</a>
            </div>
            <div class="card-body">
                @forelse($equipo->componentes as $comp)
                    <div class="border rounded p-3 mb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $comp->marca }} {{ $comp->modelo }}</strong>
                                <br><small class="text-muted">{{ $comp->categoria->nombre ?? 'N/A' }}</small>
                                <br><small class="text-muted"><i class="bi bi-upc"></i> {{ $comp->serial_unico }}</small>
                                <br><small class="text-muted"><i class="bi bi-calendar3"></i> Instalado: {{ $comp->pivot->fecha_instalacion }}</small>
                            </div>
                            <form action="{{ route('admin.equipos.remover-componente', [$equipo, $comp]) }}" method="POST" class="ms-2">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                    onclick="return confirm('¿Remover el componente {{ $comp->serial_unico }}?\n\nEste componente pasará a estado \"En Revisión\".')"
                                    title="Remover componente">
                                    <i class="bi bi-unlink"></i> Remover
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-cpu" style="font-size: 2rem;"></i>
                        <p class="mt-2">Sin componentes asignados</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
<a href="{{ route('admin.equipos.index') }}" class="btn-outline-conapdis mt-3"><i class="bi bi-arrow-left"></i> Volver</a>

@push('scripts')
<script>
document.querySelectorAll('.ver-password').forEach(btn => {
    btn.addEventListener('click', async function() {
        const soId = this.dataset.soId;
        const display = document.getElementById('password-' + soId);
        
        if (display.style.display === 'none' || !display.style.display) {
            try {
                const response = await fetch(`{{ url('panel') }}/equipos/so/${soId}/password`);
                const data = await response.json();
                
                display.querySelector('.password-text').textContent = data.password || 'Sin contraseña';
                display.style.display = 'block';
                this.innerHTML = '<i class="bi bi-eye-slash"></i> Ocultar';
            } catch (err) {
                alert('Error al obtener la contraseña');
            }
        } else {
            display.style.display = 'none';
            this.innerHTML = '<i class="bi bi-eye"></i> Ver contraseña';
        }
    });
});
</script>
@endpush
@endsection