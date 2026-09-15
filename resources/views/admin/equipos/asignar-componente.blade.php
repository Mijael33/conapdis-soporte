@extends('layouts.admin')
@section('title', 'Asignar Componente')
@section('page-title', 'Asignar Componente a: ' . $equipo->codigo_inventario_institucional)
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Componentes Disponibles</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr><th>Serial</th><th>Categoría</th><th>Marca/Modelo</th><th>Acción</th></tr>
                </thead>
                <tbody>
                    @foreach($componentesDisponibles as $comp)
                    <tr>
                        <td class="fw-semibold">{{ $comp->serial_unico }}</td>
                        <td><span class="badge badge-instalado">{{ $comp->categoria->nombre }}</span></td>
                        <td>{{ $comp->marca }} {{ $comp->modelo }}</td>
                        <td>
                            <form action="{{ route('admin.equipos.store-componente', $equipo) }}" method="POST">
                                @csrf
                                <input type="hidden" name="componente_id" value="{{ $comp->id }}">
                                <button type="submit" class="btn btn-sm btn-conapdis"><i class="bi bi-plus-circle"></i> Asignar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<a href="{{ route('admin.equipos.show', $equipo) }}" class="btn-outline-conapdis mt-3"><i class="bi bi-arrow-left"></i> Volver</a>
@endsection