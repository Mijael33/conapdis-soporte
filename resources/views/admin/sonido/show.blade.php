@extends('layouts.admin')
@section('title', 'Detalle Equipo de Sonido')
@section('page-title', 'Ficha del Equipo de Sonido')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">{{ $equipo->serial }}</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.sonido.pdf', $equipo) }}" class="btn-conapdis btn-sm" target="_blank">
                    <i class="bi bi-file-pdf"></i> PDF
                </a>
                <a href="{{ route('admin.sonido.pegatina', $equipo) }}" class="btn-outline-conapdis btn-sm" target="_blank">
                    <i class="bi bi-tag"></i> Pegatina
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table">
                <tr><th class="w-25">Código</th><td>{{ $equipo->codigo_inventario }}</td></tr>
                <tr><th>Serial</th><td class="fw-bold">{{ $equipo->serial }}</td></tr>
                <tr><th>Categoría</th><td><span class="badge badge-instalado">{{ $equipo->categoria->nombre ?? 'N/A' }}</span></td></tr>
                <tr><th>Marca/Modelo</th><td>{{ $equipo->marca }} {{ $equipo->modelo }}</td></tr>
                <tr><th>Potencia</th><td>{{ $equipo->potencia ?? 'N/A' }}</td></tr>
                <tr><th>Sede</th><td>{{ $equipo->sede ? $equipo->sede->nombre_sede . ' (' . $equipo->sede->estado->nombre . ')' : 'N/A' }}</td></tr>
                <tr><th>Estatus</th><td>
                    @if($equipo->estatus=='Disponible')<span class="badge badge-disponible">Disponible</span>
                    @elseif($equipo->estatus=='Asignado')<span class="badge badge-instalado">Asignado</span>
                    @elseif($equipo->estatus=='En Mantenimiento')<span class="badge badge-revision">En Mantenimiento</span>
                    @else<span class="badge badge-inoperativo">Desincorporado</span>@endif
                </td></tr>
                <tr><th>Usuario Asignado</th><td>{{ $equipo->usuario_asignado_nombre ?? 'N/A' }}</td></tr>
                <tr><th>Cédula</th><td>{{ $equipo->usuario_asignado_cedula ?? 'N/A' }}</td></tr>
                <tr><th>Cargo</th><td>{{ $equipo->usuario_asignado_cargo ?? 'N/A' }}</td></tr>
                <tr><th>Observaciones</th><td>{{ $equipo->observaciones ?? 'N/A' }}</td></tr>
            </table>
        </div>
    </div>
    <a href="{{ route('admin.sonido.index') }}" class="btn-outline-conapdis mt-3"><i class="bi bi-arrow-left"></i> Volver</a>
</div>
@endsection