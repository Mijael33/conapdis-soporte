@extends('layouts.admin')
@section('title', 'Detalle Orden')
@section('page-title', 'Orden: ' . $ordene->codigo_ticket)
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">{{ $ordene->codigo_ticket }}</h5>
        <a href="{{ route('admin.reportes.orden-servicio', $ordene) }}" class="btn-conapdis btn-sm" target="_blank"><i class="bi bi-file-pdf"></i> PDF</a>
    </div>
    <div class="card-body">
        <table class="table">
            <tr><th class="w-25">Ticket</th><td class="fw-bold">{{ $ordene->codigo_ticket }}</td></tr>
            <tr><th>Equipo</th><td>{{ $ordene->equipo->codigo_inventario_institucional }} - {{ $ordene->equipo->marca }} {{ $ordene->equipo->modelo }}</td></tr>
            <tr><th>Técnico</th><td>{{ $ordene->tecnico->name }}</td></tr>
            <tr><th>Fecha Inicio</th><td>{{ $ordene->fecha_inicio }}</td></tr>
            <tr><th>Fecha Cierre</th><td>{{ $ordene->fecha_cierre ?? 'Pendiente' }}</td></tr>
            <tr><th>Estatus</th><td>
                @if(!$ordene->estatus_final)<span class="badge badge-revision">Abierta</span>
                @else<span class="badge badge-operativo">{{ $ordene->estatus_final }}</span>@endif
            </td></tr>
            <tr><th>Problema Reportado</th><td>{{ $ordene->problema_reportado_usuario }}</td></tr>
            <tr><th>Diagnóstico</th><td>{{ $ordene->diagnostico_tecnico ?? 'Pendiente' }}</td></tr>
            <tr><th>Acciones Realizadas</th><td>{{ $ordene->acciones_realizadas ?? 'Pendiente' }}</td></tr>
        </table>
    </div>
</div>
<a href="{{ route('admin.ordenes.index') }}" class="btn-outline-conapdis mt-3"><i class="bi bi-arrow-left"></i> Volver</a>
@endsection