@extends('layouts.admin')
@section('title', 'Categorías de Vehículos')
@section('page-title', 'Categorías de Vehículos')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-bold">Catálogo de Categorías</h5>
        <a href="{{ route('admin.vehiculos-categorias.create') }}" class="btn-conapdis btn-sm"><i class="bi bi-plus-lg"></i> Nueva Categoría</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @forelse($categorias as $cat)
                    <tr>
                        <td>{{ $cat->id }}</td>
                        <td class="fw-semibold"><i class="bi bi-truck me-2"></i>{{ $cat->nombre }}</td>
                        <td>{{ $cat->descripcion ?? 'Sin descripción' }}</td>
                        <td>
                            <a href="{{ route('admin.vehiculos-categorias.edit', $cat) }}" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.vehiculos-categorias.destroy', $cat) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">Sin categorías</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $categorias->links() }}
    </div>
</div>
@endsection