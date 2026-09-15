@extends('layouts.admin')
@section('title', 'Estados')
@section('page-title', 'Estados')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Listado de Estados</h5>
        <a href="{{ route('admin.estados.create') }}" class="btn-conapdis"><i class="bi bi-plus-lg"></i> Nuevo Estado</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr><th>ID</th><th>Nombre</th><th>Región</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @foreach($estados as $estado)
                    <tr>
                        <td>{{ $estado->id }}</td>
                        <td class="fw-semibold">{{ $estado->nombre }}</td>
                        <td><span class="badge badge-instalado">{{ $estado->region }}</span></td>
                        <td>
                            <a href="{{ route('admin.estados.edit', $estado) }}" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.estados.destroy', $estado) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $estados->links() }}
    </div>
</div>
@endsection