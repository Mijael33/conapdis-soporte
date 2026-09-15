@extends('layouts.admin')
@section('title', 'Importar Equipos')
@section('page-title', 'Importar Equipos desde Excel')

@section('content')
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold"><i class="bi bi-file-excel me-2"></i>Importación Masiva</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <h6 class="fw-bold"><i class="bi bi-info-circle me-2"></i>Formato del Excel</h6>
            <p class="mb-1">El archivo debe tener las siguientes columnas en la PRIMERA fila (encabezados):</p>
            <table class="table table-sm table-bordered mt-2">
                <thead class="table-header">
                    <tr><th>Columna</th><th>Descripción</th><th>Obligatorio</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>codigo_inventario</code></td><td>Código de inventario institucional</td><td>✅</td></tr>
                    <tr><td><code>serial_chasis</code></td><td>Serial del chasis</td><td>❌</td></tr>
                    <tr><td><code>tipo_equipo</code></td><td>Nombre del tipo (debe existir)</td><td>✅</td></tr>
                    <tr><td><code>departamento</code></td><td>Nombre del departamento (debe existir)</td><td>✅</td></tr>
                    <tr><td><code>sede</code></td><td>Nombre de la sede (debe existir)</td><td>✅</td></tr>
                    <tr><td><code>marca</code></td><td>Marca del equipo</td><td>✅</td></tr>
                    <tr><td><code>modelo</code></td><td>Modelo del equipo</td><td>✅</td></tr>
                    <tr><td><code>usuario_nombre</code></td><td>Nombre del usuario asignado</td><td>❌</td></tr>
                    <tr><td><code>usuario_cedula</code></td><td>Cédula del usuario</td><td>❌</td></tr>
                    <tr><td><code>usuario_cargo</code></td><td>Cargo del usuario</td><td>❌</td></tr>
                </tbody>
            </table>
            <div class="mt-2">
                <a href="{{ route('admin.equipos.plantilla') }}" class="btn btn-sm btn-outline-conapdis">
                    <i class="bi bi-download"></i> Descargar Plantilla de Ejemplo
                </a>
            </div>
        </div>

        <form action="{{ route('admin.equipos.procesar-importacion') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Archivo Excel</label>
                <input type="file" name="archivo" class="form-control" accept=".xlsx,.xls,.csv" required>
                @error('archivo') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <button type="submit" class="btn-conapdis"><i class="bi bi-upload"></i> Importar</button>
            <a href="{{ route('admin.equipos.index') }}" class="btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>
@endsection