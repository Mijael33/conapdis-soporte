@extends('layouts.admin')
@section('title', 'Bitácora')
@section('page-title', 'Bitácora de Auditoría')

@section('content')
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-journal-text me-2"></i>Registros de Trazabilidad
        </h5>
        <small class="text-muted">
            @if(auth()->user()->hasRole('Administrador'))
                Viendo todos los registros del sistema
            @elseif(session('filtro_sede_id'))
                Viendo registros de: {{ \App\Models\Sede::find(session('filtro_sede_id'))->nombre_sede ?? '' }}
            @else
                Viendo registros de su sede
            @endif
        </small>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-2">
                <label class="form-label small">Desde</label>
                <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Hasta</label>
                <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn-conapdis"><i class="bi bi-search"></i> Filtrar</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr>
                        <th>Fecha</th><th>Tipo</th><th>Usuario</th><th>Equipo</th><th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bitacoras as $bit)
                    <tr>
                        <td>
                            @if($bit->tipo == 'equipo')
                                {{ $bit->fecha_registro->format('d/m/Y H:i') }}
                            @else
                                {{ $bit->fecha_acceso->format('d/m/Y H:i') }}
                            @endif
                        </td>
                        <td>
                            @if($bit->tipo == 'contrasena')
                                <span class="badge badge-revision"><i class="bi bi-eye"></i> Acceso Contraseña</span>
                            @else
                                <span class="badge badge-instalado">{{ str_replace('_', ' ', $bit->accion) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $bit->usuario->name }}</span>
                            @if($bit->usuario->sede)
                                <br><small class="text-muted">{{ $bit->usuario->sede->nombre_sede }}</small>
                            @endif
                        </td>
                        <td>
                            @if($bit->tipo == 'contrasena')
                                @if($bit->sistemaOperativo && $bit->sistemaOperativo->equipo)
                                    <a href="{{ route('admin.equipos.show', $bit->sistemaOperativo->equipo) }}" class="text-decoration-none">
                                        {{ $bit->sistemaOperativo->equipo->codigo_inventario_institucional }}
                                    </a>
                                    <br><small class="text-muted">SO: {{ $bit->sistemaOperativo->nombre }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            @else
                                @if($bit->equipo)
                                    <a href="{{ route('admin.equipos.show', $bit->equipo) }}" class="text-decoration-none">
                                        {{ $bit->equipo->codigo_inventario_institucional }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($bit->tipo == 'contrasena')
                                {{ $bit->motivo ?? 'Acceso a contraseña' }}
                                <br><small class="text-muted">IP: {{ $bit->ip_address ?? 'N/A' }}</small>
                            @else
                                {{ Str::limit($bit->descripcion_detallada, 80) }}
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No hay registros en la bitácora</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $bitacoras->links() }}
    </div>
</div>
@endsection