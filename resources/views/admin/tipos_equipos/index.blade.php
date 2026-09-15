@extends('layouts.admin')
@section('title', 'Tipos de Equipos')
@section('page-title', 'Tipos de Equipos')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Catálogo de Tipos de Equipos</h5>
        <a href="{{ route('admin.tipos-equipos.create') }}" class="btn-conapdis"><i class="bi bi-plus-lg"></i> Nuevo Tipo</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @foreach($tipos as $tipo)
                    <tr>
                        <td>{{ $tipo->id }}</td>
                        <td class="fw-semibold"><i class="bi bi-pc-display me-2"></i>{{ $tipo->nombre }}</td>
                        <td>{{ $tipo->descripcion ?? 'Sin descripción' }}</td>
                        <td>
                            <a href="{{ route('admin.tipos-equipos.edit', $tipo) }}" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.tipos-equipos.destroy', $tipo) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $tipos->links() }}
    </div>
</div>
@endsection