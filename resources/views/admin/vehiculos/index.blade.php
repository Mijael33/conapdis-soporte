@extends('layouts.admin')
@section('title', 'Vehículos')
@section('page-title', 'Flota Vehicular')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-bold">Inventario de Vehículos</h5>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.vehiculos.importar') }}" class="btn-outline-conapdis btn-sm"><i class="bi bi-upload"></i> Importar Excel</a>
            <a href="{{ route('admin.vehiculos.exportar-excel') }}" class="btn-outline-conapdis btn-sm"><i class="bi bi-file-excel"></i> Exportar Excel</a>
            <a href="{{ route('admin.vehiculos.exportar-pdf') }}" class="btn-outline-conapdis btn-sm"><i class="bi bi-file-pdf"></i> Exportar PDF</a>
            <a href="{{ route('admin.vehiculos.create') }}" class="btn-conapdis btn-sm"><i class="bi bi-plus-lg"></i> Nuevo</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
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
                    <option value="Asignado" {{ request('estatus')=='Asignado' ? 'selected' : '' }}>Asignado</option>
                    <option value="En Mantenimiento" {{ request('estatus')=='En Mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                    <option value="Desincorporado" {{ request('estatus')=='Desincorporado' ? 'selected' : '' }}>Desincorporado</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Buscar placa, marca..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-conapdis"><i class="bi bi-search"></i> Filtrar</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr>
                        <th>Código</th><th>Placa</th><th>Categoría</th><th>Marca/Modelo</th>
                        <th>Estado</th><th>Sede</th><th>Estatus</th>
                        <th class="text-center" style="width: 130px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehiculos as $veh)
                    <tr>
                        <td class="fw-semibold">{{ $veh->codigo_inventario }}</td>
                        <td><span class="badge badge-instalado">{{ $veh->placa }}</span></td>
                        <td>{{ $veh->categoria->nombre ?? 'N/A' }}</td>
                        <td>{{ $veh->marca }} {{ $veh->modelo }} ({{ $veh->anio }})</td>
                        <td>{{ $veh->sede ? $veh->sede->estado->nombre : 'N/A' }}</td>
                        <td>{{ $veh->sede ? $veh->sede->nombre_sede : 'N/A' }}</td>
                        <td>
                            @if($veh->estatus=='Disponible')<span class="badge badge-disponible">Disponible</span>
                            @elseif($veh->estatus=='Asignado')<span class="badge badge-instalado">Asignado</span>
                            @elseif($veh->estatus=='En Mantenimiento')<span class="badge badge-revision">En Mant.</span>
                            @else<span class="badge badge-inoperativo">Desinc.</span>@endif
                        </td>
                        <td class="text-center" style="white-space: nowrap;">
                            <a href="{{ route('admin.vehiculos.show', $veh) }}" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.vehiculos.edit', $veh) }}" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.vehiculos.destroy', $veh) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar el vehículo {{ $veh->placa }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No hay vehículos registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $vehiculos->links() }}
    </div>
</div>

<x-modal-resultado-importacion rutaDescarga="admin.vehiculos.descargar-errores" />
@endsection