@extends('layouts.admin')
@section('title', 'Detalle Bitácora')
@section('page-title', 'Detalle de Registro')
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Registro #{{ $bitacora->id }}</h5></div>
    <div class="card-body">
        <table class="table">
            <tr><th class="w-25">Fecha</th><td>{{ $bitacora->fecha_registro }}</td></tr>
            <tr><th>Usuario</th><td>{{ $bitacora->usuario->name }}</td></tr>
            <tr><th>Equipo</th><td>{{ $bitacora->equipo->codigo_inventario_institucional }}</td></tr>
            <tr><th>Acción</th><td><span class="badge badge-instalado">{{ $bitacora->accion }}</span></td></tr>
            <tr><th>Descripción</th><td>{{ $bitacora->descripcion_detallada }}</td></tr>
        </table>
        @if($bitacora->datos_anteriores)
        <h6 class="fw-bold mt-3">Datos Anteriores</h6>
        <pre class="bg-light p-3 rounded">{{ json_encode($bitacora->datos_anteriores, JSON_PRETTY_PRINT) }}</pre>
        @endif
        @if($bitacora->datos_nuevos)
        <h6 class="fw-bold mt-3">Datos Nuevos</h6>
        <pre class="bg-light p-3 rounded">{{ json_encode($bitacora->datos_nuevos, JSON_PRETTY_PRINT) }}</pre>
        @endif
    </div>
</div>
<a href="{{ route('admin.bitacora.index') }}" class="btn-outline-conapdis mt-3"><i class="bi bi-arrow-left"></i> Volver</a>
@endsection