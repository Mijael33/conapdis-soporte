@extends('layouts.admin')
@section('title', 'Importar Componentes')
@section('page-title', 'Importar Componentes desde Excel')

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
                    <tr><td><code>categoria</code></td><td>Nombre de la categoría (debe existir en el sistema)</td><td>✅</td></tr>
                    <tr><td><code>marca</code></td><td>Marca del componente</td><td>✅</td></tr>
                    <tr><td><code>modelo</code></td><td>Modelo del componente</td><td>✅</td></tr>
                    <tr><td><code>serial_unico</code></td><td>Serial único (no puede repetirse)</td><td>✅</td></tr>
                    <tr><td><code>sede</code></td><td>Nombre de la sede (debe existir)</td><td>✅</td></tr>
                    <tr><td><code>estatus</code></td><td>Disponible, Instalado, En Revisión, Desincorporado</td><td>❌ (default: Disponible)</td></tr>
                    <tr><td><code>observaciones</code></td><td>Notas adicionales</td><td>❌</td></tr>
                </tbody>
            </table>
            <div class="mt-2">
                <a href="{{ route('admin.componentes.plantilla') }}" class="btn btn-sm btn-outline-conapdis">
                    <i class="bi bi-download"></i> Descargar Plantilla de Ejemplo
                </a>
            </div>
        </div>

        <form action="{{ route('admin.componentes.procesar-importacion') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Archivo Excel</label>
                <input type="file" name="archivo" class="form-control" accept=".xlsx,.xls,.csv" required>
                @error('archivo') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <button type="submit" class="btn-conapdis"><i class="bi bi-upload"></i> Importar</button>
            <a href="{{ route('admin.componentes.index') }}" class="btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>
@endsection