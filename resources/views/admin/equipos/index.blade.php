@extends('layouts.admin')
@section('title', 'Equipos')
@section('page-title', 'Equipos')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-bold">Inventario de Equipos</h5>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.equipos.importar') }}" class="btn-outline-conapdis btn-sm"><i class="bi bi-upload"></i> Importar</a>
            <a href="{{ route('admin.equipos.exportar-excel') }}" class="btn-outline-conapdis btn-sm"><i class="bi bi-file-excel"></i> Excel</a>
            <a href="{{ route('admin.equipos.exportar-pdf') }}" class="btn-outline-conapdis btn-sm"><i class="bi bi-file-pdf"></i> PDF</a>
            <a href="{{ route('admin.equipos.create') }}" class="btn-conapdis btn-sm"><i class="bi bi-plus-lg"></i> Nuevo</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-2">
                <select name="estatus" class="form-select">
                    <option value="">Todos los estatus</option>
                    <option value="Operativo" {{ request('estatus')=='Operativo' ? 'selected' : '' }}>Operativo</option>
                    <option value="En Mantenimiento" {{ request('estatus')=='En Mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                    <option value="Inoperativo" {{ request('estatus')=='Inoperativo' ? 'selected' : '' }}>Inoperativo</option>
                    <option value="Donado/Desincorporado" {{ request('estatus')=='Donado/Desincorporado' ? 'selected' : '' }}>Desincorporado</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="departamento_id" class="form-select">
                    <option value="">Todos los departamentos</option>
                    @foreach($departamentos as $depto)
                        <option value="{{ $depto->id }}" {{ request('departamento_id')==$depto->id ? 'selected' : '' }}>
                            {{ $depto->nombre_departamento }} ({{ $depto->sede->nombre_sede }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Buscar código, serial, marca..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-conapdis"><i class="bi bi-search"></i> Filtrar</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr>
                        <th>Código</th><th>Tipo</th><th>Marca/Modelo</th>
                        <th>Estado</th><th>Sede</th><th>Departamento</th>
                        <th>Estatus</th><th class="text-center" style="width: 130px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equipos as $equipo)
                    <tr>
                        <td class="fw-semibold">{{ $equipo->codigo_inventario_institucional }}</td>
                        <td><span class="badge badge-instalado">{{ $equipo->tipoEquipo->nombre }}</span></td>
                        <td>{{ $equipo->marca }} {{ $equipo->modelo }}</td>
                        <td>{{ $equipo->departamento->sede->estado->nombre }}</td>
                        <td>{{ $equipo->departamento->sede->nombre_sede }}</td>
                        <td>{{ $equipo->departamento->nombre_departamento }}</td>
                        <td>
                            @if($equipo->estatus_general=='Operativo')<span class="badge badge-operativo">Operativo</span>
                            @elseif($equipo->estatus_general=='En Mantenimiento')<span class="badge badge-mantenimiento">En Mant.</span>
                            @elseif($equipo->estatus_general=='Inoperativo')<span class="badge badge-inoperativo">Inoperativo</span>
                            @else<span class="badge badge-revision">Desinc.</span>@endif
                        </td>
                        <td class="text-center" style="white-space: nowrap;">
                            <a href="{{ route('admin.equipos.show', $equipo) }}" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.equipos.edit', $equipo) }}" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.equipos.destroy', $equipo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar el equipo {{ $equipo->codigo_inventario_institucional }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No hay equipos registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $equipos->links() }}
    </div>
</div>

<x-modal-resultado-importacion rutaDescarga="admin.equipos.descargar-errores" />
@endsection