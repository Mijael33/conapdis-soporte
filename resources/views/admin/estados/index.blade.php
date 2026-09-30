@extends('layouts.admin')
@section('title', 'Estados')
@section('page-title', 'Estados y Sedes')

@section('content')
<div class="container-fluid">

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
            <div>
                <h5 class="mb-0 fw-bold" style="color: #1a3b5d;">
                    <i class="bi bi-geo-alt me-2"></i>Estados y Sedes
                </h5>
                <small class="text-muted">Catálogo base de estados de Venezuela y sus sedes</small>
            </div>
            @can('estados.crear')
            <a href="{{ route('admin.estados.create') }}" class="btn-conapdis btn-sm">
                <i class="bi bi-plus-lg"></i> Nuevo Estado
            </a>
            @endcan
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-header">
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Estado</th>
                            <th style="width: 140px;">Región</th>
                            <th style="width: 120px;" class="text-center">Sedes</th>
                            <th style="width: 200px;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estados as $estado)
                        <tr>
                            <td class="text-muted">#{{ $estado->id }}</td>
                            <td>
                                <div class="fw-bold" style="color: #001e5c;">{{ $estado->nombre }}</div>
                            </td>
                            <td>
                                <span class="badge rounded-pill" style="background: #dbeafe; color: #003097;">
                                    {{ $estado->region }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill" style="background: #e0e7ff; color: #4f46e5; font-size: 0.85rem;">
                                    <i class="bi bi-building me-1"></i>{{ $estado->sedes_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    @can('estados.ver')
                                    <a href="{{ route('admin.estados.show', $estado) }}" class="btn btn-sm btn-outline-conapdis" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @endcan
                                    @can('estados.editar')
                                    <a href="{{ route('admin.estados.edit', $estado) }}" class="btn btn-sm btn-outline-conapdis" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('estados.eliminar')
                                    <form action="{{ route('admin.estados.destroy', $estado) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar el estado {{ $estado->nombre }}?\n\nSolo se puede eliminar si no tiene sedes asociadas.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar" {{ $estado->sedes_count > 0 ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="mt-2 mb-0">No hay estados registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $estados->links() }}
        </div>
    </div>
</div>
@endsection