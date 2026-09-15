@extends('layouts.admin')
@section('title', 'Detalle Vehículo')
@section('page-title', 'Ficha del Vehículo')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">{{ $vehiculo->placa }}</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.vehiculos.pdf', $vehiculo) }}" class="btn-conapdis btn-sm" target="_blank">
                    <i class="bi bi-file-pdf"></i> PDF
                </a>
                <a href="{{ route('admin.vehiculos.pegatina', $vehiculo) }}" class="btn-outline-conapdis btn-sm" target="_blank">
                    <i class="bi bi-tag"></i> Pegatina
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table">
                <tr><th class="w-25">Código</th><td>{{ $vehiculo->codigo_inventario }}</td></tr>
                <tr><th>Placa</th><td class="fw-bold">{{ $vehiculo->placa }}</td></tr>
                <tr><th>Categoría</th><td>{{ $vehiculo->categoria->nombre ?? 'N/A' }}</td></tr>
                <tr><th>Marca/Modelo</th><td>{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</td></tr>
                <tr><th>Año</th><td>{{ $vehiculo->anio ?? 'N/A' }}</td></tr>
                <tr><th>Color</th><td>{{ $vehiculo->color ?? 'N/A' }}</td></tr>
                <tr><th>Serial Motor</th><td>{{ $vehiculo->serial_motor ?? 'N/A' }}</td></tr>
                <tr><th>Serial Chasis</th><td>{{ $vehiculo->serial_chasis ?? 'N/A' }}</td></tr>
                <tr><th>Kilometraje</th><td>{{ number_format($vehiculo->kilometraje, 0, ',', '.') }} km</td></tr>
                <tr><th>Sede</th><td>{{ $vehiculo->sede ? $vehiculo->sede->nombre_sede . ' (' . $vehiculo->sede->estado->nombre . ')' : 'N/A' }}</td></tr>
                <tr><th>Estatus</th><td>
                    @if($vehiculo->estatus=='Disponible')<span class="badge badge-disponible">Disponible</span>
                    @elseif($vehiculo->estatus=='Asignado')<span class="badge badge-instalado">Asignado</span>
                    @elseif($vehiculo->estatus=='En Mantenimiento')<span class="badge badge-revision">En Mantenimiento</span>
                    @else<span class="badge badge-inoperativo">Desincorporado</span>@endif
                </td></tr>
                <tr><th>Usuario Asignado</th><td>{{ $vehiculo->usuario_asignado_nombre ?? 'N/A' }}</td></tr>
                <tr><th>Cédula</th><td>{{ $vehiculo->usuario_asignado_cedula ?? 'N/A' }}</td></tr>
                <tr><th>Cargo</th><td>{{ $vehiculo->usuario_asignado_cargo ?? 'N/A' }}</td></tr>
                <tr><th>Observaciones</th><td>{{ $vehiculo->observaciones ?? 'N/A' }}</td></tr>
            </table>
        </div>
    </div>
    <a href="{{ route('admin.vehiculos.index') }}" class="btn-outline-conapdis mt-3"><i class="bi bi-arrow-left"></i> Volver</a>
</div>
@endsection