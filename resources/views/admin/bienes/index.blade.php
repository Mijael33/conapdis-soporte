@extends('layouts.admin')
@section('title', 'Bienes Nacionales')
@section('page-title', 'Bienes Nacionales')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-bold">Inventario de Bienes</h5>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.bienes.importar') }}" class="btn-outline-conapdis btn-sm"><i class="bi bi-upload"></i> Importar Excel</a>
            <a href="{{ route('admin.bienes.exportar-excel') }}" class="btn-outline-conapdis btn-sm"><i class="bi bi-file-excel"></i> Exportar Excel</a>
            <a href="{{ route('admin.bienes.exportar-pdf') }}" class="btn-outline-conapdis btn-sm"><i class="bi bi-file-pdf"></i> Exportar PDF</a>
            <a href="{{ route('admin.bienes.create') }}" class="btn-conapdis btn-sm"><i class="bi bi-plus-lg"></i> Nuevo</a>
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
                <input type="text" name="search" class="form-control" placeholder="Buscar código, descripción..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-conapdis"><i class="bi bi-search"></i> Filtrar</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr>
                        <th>Código</th><th>Categoría</th><th>Descripción</th>
                        <th>Estado</th><th>Sede</th><th>Estatus</th>
                        <th class="text-center" style="width: 130px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bienes as $bien)
                    <tr>
                        <td class="fw-semibold">{{ $bien->codigo_inventario }}</td>
                        <td><span class="badge badge-instalado">{{ $bien->categoria->nombre ?? 'N/A' }}</span></td>
                        <td>{{ Str::limit($bien->descripcion, 40) }}</td>
                        <td>{{ $bien->sede ? $bien->sede->estado->nombre : 'N/A' }}</td>
                        <td>{{ $bien->sede ? $bien->sede->nombre_sede : 'N/A' }}</td>
                        <td>
                            @if($bien->estatus=='Disponible')<span class="badge badge-disponible">Disponible</span>
                            @elseif($bien->estatus=='Asignado')<span class="badge badge-instalado">Asignado</span>
                            @elseif($bien->estatus=='En Mantenimiento')<span class="badge badge-revision">En Mant.</span>
                            @else<span class="badge badge-inoperativo">Desinc.</span>@endif
                        </td>
                        <td class="text-center" style="white-space: nowrap;">
                            <a href="{{ route('admin.bienes.show', $bien) }}" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.bienes.edit', $bien) }}" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.bienes.destroy', $bien) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar el bien {{ $bien->codigo_inventario }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay bienes registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $bienes->links() }}
    </div>
</div>

<x-modal-resultado-importacion rutaDescarga="admin.bienes.descargar-errores" />
@endsection