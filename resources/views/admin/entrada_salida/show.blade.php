@extends('layouts.admin')
@section('title', 'Detalle Movimiento')
@section('page-title', 'Detalle de Movimiento')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">
            Comprobante {{ $registro->tipo }} - CONAPDIS-{{ $registro->tipo }}-{{ str_pad($registro->id, 6, '0', STR_PAD_LEFT) }}
        </h5>
        <a href="{{ route('admin.entrada-salida.pdf', $registro) }}" class="btn-conapdis btn-sm" target="_blank">
            <i class="bi bi-file-pdf"></i> Descargar PDF
        </a>
    </div>
    <div class="card-body">
        <h6 class="fw-bold text-primary">Datos del Movimiento</h6>
        <table class="table">
            <tr><th class="w-25">N° Comprobante</th><td><strong>CONAPDIS-{{ $registro->tipo }}-{{ str_pad($registro->id, 6, '0', STR_PAD_LEFT) }}</strong></td></tr>
            <tr><th>Tipo</th><td>
                @if($registro->tipo == 'Salida')<span class="badge badge-revision">Salida</span>
                @else<span class="badge badge-disponible">Entrada</span>@endif
            </td></tr>
            <tr><th>Tipo de Bien</th><td>{{ $registro->bien_tipo }}</td></tr>
        </table>

        <h6 class="fw-bold text-primary mt-4">Datos del Bien</h6>
        <table class="table">
            <tr><th class="w-25">Código Inventario</th><td>{{ $registro->codigo_inventario }}</td></tr>
            <tr><th>Descripción</th><td>{{ $registro->descripcion_bien }}</td></tr>
            <tr><th>Sede</th><td>{{ $registro->sede ? $registro->sede->nombre_sede : 'N/A' }}</td></tr>
            <tr><th>Estado</th><td>{{ $registro->sede ? $registro->sede->estado->nombre : 'N/A' }}</td></tr>
        </table>

        @if($registro->tipo == 'Salida')
        <h6 class="fw-bold text-primary mt-4">Datos de Salida</h6>
        <table class="table">
            <tr><th class="w-25">Fecha Salida</th><td>{{ $registro->fecha_hora_salida ? $registro->fecha_hora_salida->format('d/m/Y H:i') : 'N/A' }}</td></tr>
            <tr><th>Autorizado por</th><td>{{ $registro->autorizado_por_nombre }} ({{ $registro->autorizado_por_cargo }})</td></tr>
            <tr><th>C.I. Autorizado</th><td>{{ $registro->autorizado_por_cedula }}</td></tr>
            <tr><th>Retirado por</th><td>{{ $registro->persona_retira_nombre }} ({{ $registro->persona_retira_cargo }})</td></tr>
            <tr><th>C.I. Retira</th><td>{{ $registro->persona_retira_cedula }}</td></tr>
            <tr><th>Motivo</th><td>{{ $registro->motivo }}</td></tr>
            <tr><th>Destino</th><td>{{ $registro->destino }}</td></tr>
            <tr><th>Estado Salida</th><td>{{ $registro->estado_salida ?? 'N/A' }}</td></tr>
            @if($registro->observaciones_salida)
            <tr><th>Observaciones</th><td>{{ $registro->observaciones_salida }}</td></tr>
            @endif
        </table>

        <h6 class="fw-bold text-primary mt-4">Registro de Seguridad - Salida</h6>
        <table class="table">
            <tr><th class="w-25">Seguridad</th><td>{{ $registro->seguridad_salida_nombre }}</td></tr>
            <tr><th>C.I.</th><td>{{ $registro->seguridad_salida_cedula }}</td></tr>
        </table>
        @else
        <h6 class="fw-bold text-primary mt-4">Datos de Entrada</h6>
        <table class="table">
            <tr><th class="w-25">Fecha Entrada</th><td>{{ $registro->fecha_hora_entrada ? $registro->fecha_hora_entrada->format('d/m/Y H:i') : 'N/A' }}</td></tr>
            <tr><th>Estado Entrada</th><td>{{ $registro->estado_entrada ?? 'N/A' }}</td></tr>
            @if($registro->observaciones_entrada)
            <tr><th>Observaciones</th><td>{{ $registro->observaciones_entrada }}</td></tr>
            @endif
        </table>

        <h6 class="fw-bold text-primary mt-4">Registro de Seguridad - Entrada</h6>
        <table class="table">
            <tr><th class="w-25">Seguridad</th><td>{{ $registro->seguridad_entrada_nombre }}</td></tr>
            <tr><th>C.I.</th><td>{{ $registro->seguridad_entrada_cedula }}</td></tr>
        </table>
        @endif

        <h6 class="fw-bold text-primary mt-4">Información del Registro</h6>
        <table class="table">
            <tr><th class="w-25">Registrado por</th><td>{{ $registro->usuario->name }}</td></tr>
            <tr><th>Fecha Registro</th><td>{{ $registro->created_at->format('d/m/Y H:i') }}</td></tr>
        </table>
    </div>
</div>
<a href="{{ route('admin.entrada-salida.index') }}" class="btn-outline-conapdis mt-3"><i class="bi bi-arrow-left"></i> Volver</a>
@endsection