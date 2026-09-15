@extends('layouts.admin')
@section('title', 'Componentes')
@section('page-title', 'Componentes')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-bold">Inventario de Componentes</h5>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.componentes.importar') }}" class="btn-outline-conapdis btn-sm"><i class="bi bi-upload"></i> Importar</a>
            <a href="{{ route('admin.componentes.exportar-excel') }}" class="btn-outline-conapdis btn-sm"><i class="bi bi-file-excel"></i> Excel</a>
            <a href="{{ route('admin.componentes.exportar-pdf') }}" class="btn-outline-conapdis btn-sm"><i class="bi bi-file-pdf"></i> PDF</a>
            <a href="{{ route('admin.componentes.create') }}" class="btn-conapdis btn-sm"><i class="bi bi-plus-lg"></i> Nuevo</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-2">
                <select name="categoria_id" class="form-select">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ request('categoria_id')==$cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="estatus" class="form-select">
                    <option value="">Todos los estatus</option>
                    <option value="Disponible" {{ request('estatus')=='Disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="Instalado" {{ request('estatus')=='Instalado' ? 'selected' : '' }}>Instalado</option>
                    <option value="En Revisión" {{ request('estatus')=='En Revisión' ? 'selected' : '' }}>En Revisión</option>
                    <option value="Desincorporado" {{ request('estatus')=='Desincorporado' ? 'selected' : '' }}>Desincorporado</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Buscar serial, marca..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-conapdis"><i class="bi bi-search"></i> Filtrar</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr>
                        <th>Serial</th><th>Categoría</th><th>Marca/Modelo</th>
                        <th>Estado</th><th>Sede</th><th>Estatus</th><th>Equipo Actual</th>
                        <th class="text-center" style="width: 130px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($componentes as $comp)
                    <tr>
                        <td class="fw-semibold">{{ $comp->serial_unico }}</td>
                        <td><span class="badge badge-instalado">{{ $comp->categoria->nombre ?? 'N/A' }}</span></td>
                        <td>{{ $comp->marca }} {{ $comp->modelo }}</td>
                        <td>{{ $comp->sede ? $comp->sede->estado->nombre : 'N/A' }}</td>
                        <td>{{ $comp->sede ? $comp->sede->nombre_sede : 'N/A' }}</td>
                        <td>
                            @if($comp->estatus=='Disponible')<span class="badge badge-disponible">Disponible</span>
                            @elseif($comp->estatus=='Instalado')<span class="badge badge-instalado">Instalado</span>
                            @elseif($comp->estatus=='En Revisión')<span class="badge badge-revision">En Revisión</span>
                            @else<span class="badge badge-inoperativo">Desinc.</span>@endif
                        </td>
                        <td>
                            @if($comp->estatus == 'Instalado' && $comp->equipoActual->isNotEmpty())
                                @foreach($comp->equipoActual as $equipo)
                                    <a href="{{ route('admin.equipos.show', $equipo) }}" class="text-decoration-none fw-semibold">
                                        <i class="bi bi-pc-display"></i> {{ $equipo->codigo_inventario_institucional }}
                                    </a>
                                @endforeach
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center" style="white-space: nowrap;">
                            <a href="{{ route('admin.componentes.show', $comp) }}" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.componentes.edit', $comp) }}" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.componentes.destroy', $comp) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar el componente {{ $comp->serial_unico }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No hay componentes registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $componentes->links() }}
    </div>
</div>

<x-modal-resultado-importacion rutaDescarga="admin.componentes.descargar-errores" />
@endsection