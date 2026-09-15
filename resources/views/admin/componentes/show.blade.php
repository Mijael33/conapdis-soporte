@extends('layouts.admin')
@section('title', 'Detalle Componente')
@section('page-title', 'Historial del Componente')

@section('content')
<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">{{ $componente->marca }} {{ $componente->modelo }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.componentes.pdf', $componente) }}" class="btn-conapdis btn-sm" target="_blank">
                        <i class="bi bi-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('admin.componentes.pegatina', $componente) }}" class="btn-outline-conapdis btn-sm" target="_blank">
                        <i class="bi bi-tag"></i> Pegatina
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr><th class="w-25">Serial Único</th><td class="fw-bold">{{ $componente->serial_unico }}</td></tr>
                    <tr><th>Categoría</th><td><span class="badge badge-instalado">{{ $componente->categoria->nombre ?? 'N/A' }}</span></td></tr>
                    <tr><th>Sede</th><td>{{ $componente->sede ? $componente->sede->nombre_sede . ' (' . $componente->sede->estado->nombre . ')' : 'Sin sede' }}</td></tr>
                    <tr><th>Estatus</th><td>
                        @if($componente->estatus=='Disponible')<span class="badge badge-disponible">Disponible</span>
                        @elseif($componente->estatus=='Instalado')<span class="badge badge-instalado">Instalado</span>
                        @elseif($componente->estatus=='En Revisión')<span class="badge badge-revision">En Revisión</span>
                        @else<span class="badge badge-inoperativo">Desincorporado</span>@endif
                    </td></tr>
                    <tr><th>Observaciones</th><td>{{ $componente->observaciones ?? 'N/A' }}</td></tr>
                    @if($componente->estatus == 'Instalado' && $componente->equipoActual->isNotEmpty())
                    <tr>
                        <th>Instalado en</th>
                        <td>
                            @foreach($componente->equipoActual as $equipo)
                                <a href="{{ route('admin.equipos.show', $equipo) }}" class="btn btn-sm btn-outline-conapdis">
                                    <i class="bi bi-pc-display"></i> {{ $equipo->codigo_inventario_institucional }}
                                </a>
                                <br><small class="text-muted">{{ $equipo->departamento->sede->nombre_sede }} | Instalado: {{ $equipo->pivot->fecha_instalacion }}</small>
                            @endforeach
                        </td>
                    </tr>
                    @endif
                </table>

                @if($componente->caracteristicas_tecnicas)
                <h6 class="fw-bold mt-3">Características Técnicas</h6>
                <table class="table table-bordered">
                    @foreach($componente->caracteristicas_tecnicas as $key => $value)
                    <tr><th class="w-25">{{ ucfirst($key) }}</th><td>{{ $value }}</td></tr>
                    @endforeach
                </table>
                @endif
            </div>
        </div>

        {{-- BITÁCORA DE CAMBIOS DEL COMPONENTE --}}
        <div class="card mt-3">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Bitácora de Cambios del Componente</h5>
            </div>
            <div class="card-body">
                @forelse($componente->bitacoras as $bit)
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

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Historial en Equipos</h5></div>
            <div class="card-body">
                @forelse($componente->equipos as $equipo)
                    <div class="border-bottom pb-2 mb-2">
                        <a href="{{ route('admin.equipos.show', $equipo) }}" class="fw-semibold text-decoration-none">
                            {{ $equipo->codigo_inventario_institucional }}
                        </a>
                        <br><small>{{ $equipo->departamento->sede->nombre_sede ?? 'N/A' }}</small>
                        <br><small class="text-muted">Instalado: {{ $equipo->pivot->fecha_instalacion }}</small>
                        @if($equipo->pivot->fecha_desinstalacion)
                            <br><small class="text-danger">Removido: {{ $equipo->pivot->fecha_desinstalacion }}</small>
                        @endif
                        @if($equipo->pivot->activo)
                            <span class="badge badge-operativo ms-2">Activo</span>
                        @else
                            <span class="badge badge-inoperativo ms-2">Inactivo</span>
                        @endif
                    </div>
                @empty
                    <p class="text-muted">Sin historial</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
<a href="{{ route('admin.componentes.index') }}" class="btn-outline-conapdis mt-3"><i class="bi bi-arrow-left"></i> Volver</a>
@endsection