@extends('layouts.admin')
@section('title', 'Departamentos')
@section('page-title', 'Departamentos')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Listado de Departamentos</h5>
        <a href="{{ route('admin.departamentos.create') }}" class="btn-conapdis"><i class="bi bi-plus-lg"></i> Nuevo Departamento</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr><th>ID</th><th>Nombre</th><th>Sede</th><th>Estado</th><th>Piso</th><th>Extensión</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @foreach($departamentos as $depto)
                    <tr>
                        <td>{{ $depto->id }}</td>
                        <td class="fw-semibold">{{ $depto->nombre_departamento }}</td>
                        <td>{{ $depto->sede->nombre_sede }}</td>
                        <td><span class="badge badge-instalado">{{ $depto->sede->estado->nombre }}</span></td>
                        <td>{{ $depto->piso ?? 'N/A' }}</td>
                        <td>{{ $depto->extension_telefonica ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('admin.departamentos.edit', $depto) }}" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.departamentos.destroy', $depto) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $departamentos->links() }}
    </div>
</div>
@endsection