@extends('layouts.admin')
@section('title', 'Categorías de Componentes')
@section('page-title', 'Categorías de Componentes')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Catálogo de Categorías</h5>
        <a href="{{ route('admin.categorias-componentes.create') }}" class="btn-conapdis"><i class="bi bi-plus-lg"></i> Nueva Categoría</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @foreach($categorias as $cat)
                    <tr>
                        <td>{{ $cat->id }}</td>
                        <td class="fw-semibold"><i class="bi bi-cpu me-2"></i>{{ $cat->nombre }}</td>
                        <td>{{ $cat->descripcion ?? 'Sin descripción' }}</td>
                        <td>
                            <a href="{{ route('admin.categorias-componentes.edit', $cat) }}" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.categorias-componentes.destroy', $cat) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $categorias->links() }}
    </div>
</div>
@endsection