@extends('layouts.admin')
@section('title', 'Detalle Bien')
@section('page-title', 'Ficha del Bien')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">{{ $bien->codigo_inventario }}</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.bienes.pdf', $bien) }}" class="btn-conapdis btn-sm" target="_blank">
                    <i class="bi bi-file-pdf"></i> PDF
                </a>
                <a href="{{ route('admin.bienes.pegatina', $bien) }}" class="btn-outline-conapdis btn-sm" target="_blank">
                    <i class="bi bi-tag"></i> Pegatina
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table">
                <tr><th class="w-25">Código</th><td class="fw-bold">{{ $bien->codigo_inventario }}</td></tr>
                <tr><th>Categoría</th><td><span class="badge badge-instalado">{{ $bien->categoria->nombre ?? 'N/A' }}</span></td></tr>
                <tr><th>Descripción</th><td>{{ $bien->descripcion }}</td></tr>
                <tr><th>Marca/Modelo</th><td>{{ $bien->marca ?? 'N/A' }} {{ $bien->modelo ?? '' }}</td></tr>
                <tr><th>Serial</th><td>{{ $bien->serial ?? 'N/A' }}</td></tr>
                <tr><th>Color</th><td>{{ $bien->color ?? 'N/A' }}</td></tr>
                <tr><th>Material</th><td>{{ $bien->material ?? 'N/A' }}</td></tr>
                <tr><th>Sede</th><td>{{ $bien->sede ? $bien->sede->nombre_sede . ' (' . $bien->sede->estado->nombre . ')' : 'N/A' }}</td></tr>
                <tr><th>Estatus</th><td>
                    @if($bien->estatus=='Disponible')<span class="badge badge-disponible">Disponible</span>
                    @elseif($bien->estatus=='Asignado')<span class="badge badge-instalado">Asignado</span>
                    @elseif($bien->estatus=='En Mantenimiento')<span class="badge badge-revision">En Mantenimiento</span>
                    @else<span class="badge badge-inoperativo">Desincorporado</span>@endif
                </td></tr>
                <tr><th>Usuario Asignado</th><td>{{ $bien->usuario_asignado_nombre ?? 'N/A' }}</td></tr>
                <tr><th>Cédula</th><td>{{ $bien->usuario_asignado_cedula ?? 'N/A' }}</td></tr>
                <tr><th>Cargo</th><td>{{ $bien->usuario_asignado_cargo ?? 'N/A' }}</td></tr>
                <tr><th>Valor Adquisición</th><td>{{ $bien->valor_adquisicion ? number_format($bien->valor_adquisicion, 2, ',', '.') . ' Bs.' : 'N/A' }}</td></tr>
                <tr><th>Fecha Adquisición</th><td>{{ $bien->fecha_adquisicion ? $bien->fecha_adquisicion->format('d/m/Y') : 'N/A' }}</td></tr>
                <tr><th>Observaciones</th><td>{{ $bien->observaciones ?? 'N/A' }}</td></tr>
            </table>
        </div>
    </div>
    <a href="{{ route('admin.bienes.index') }}" class="btn-outline-conapdis mt-3"><i class="bi bi-arrow-left"></i> Volver</a>
</div>
@endsection