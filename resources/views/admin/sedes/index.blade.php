@extends('layouts.admin')
@section('title', 'Sedes')
@section('page-title', 'Sedes')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Listado de Sedes</h5>
        <a href="{{ route('admin.sedes.create') }}" class="btn-conapdis"><i class="bi bi-plus-lg"></i> Nueva Sede</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr><th>ID</th><th>Nombre</th><th>Estado</th><th>Dirección</th><th>Código Postal</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @foreach($sedes as $sede)
                    <tr>
                        <td>{{ $sede->id }}</td>
                        <td class="fw-semibold">{{ $sede->nombre_sede }}</td>
                        <td><span class="badge badge-instalado">{{ $sede->estado->nombre }}</span></td>
                        <td>{{ Str::limit($sede->direccion, 40) }}</td>
                        <td>{{ $sede->codigo_postal ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('admin.sedes.edit', $sede) }}" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.sedes.destroy', $sede) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta sede?')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $sedes->links() }}
    </div>
</div>
@endsection