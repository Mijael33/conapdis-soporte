@extends('layouts.admin')
@section('title', 'Importar Bienes')
@section('page-title', 'Importar Bienes desde Excel')
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-file-excel me-2"></i>Importación Masiva</h5></div>
    <div class="card-body">
        <div class="alert alert-info">
            <h6 class="fw-bold"><i class="bi bi-info-circle me-2"></i>Formato del Excel</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mt-2">
                    <thead class="table-header"><tr><th>Columna</th><th>Descripción</th><th>Obligatorio</th></tr></thead>
                    <tbody>
                        <tr><td><code>codigo_inventario</code></td><td>Código único</td><td>✅</td></tr>
                        <tr><td><code>categoria</code></td><td>Nombre de categoría (debe existir o se crea automáticamente)</td><td>✅</td></tr>
                        <tr><td><code>sede</code></td><td>Nombre de sede (debe existir o se crea automáticamente)</td><td>✅</td></tr>
                        <tr><td><code>descripcion</code></td><td>Descripción del bien</td><td>✅</td></tr>
                        <tr><td><code>marca</code></td><td>Marca del bien</td><td>❌</td></tr>
                        <tr><td><code>modelo</code></td><td>Modelo del bien</td><td>❌</td></tr>
                        <tr><td><code>serial</code></td><td>Número de serial</td><td>❌</td></tr>
                        <tr><td><code>color</code></td><td>Color predominante</td><td>❌</td></tr>
                        <tr><td><code>material</code></td><td>Material de fabricación</td><td>❌</td></tr>
                        <tr><td><code>estatus</code></td><td>Disponible, Asignado, En Mantenimiento, Desincorporado (por defecto: Disponible)</td><td>❌</td></tr>
                        <tr><td><code>usuario_asignado</code></td><td>Nombre del usuario asignado</td><td>❌</td></tr>
                        <tr><td><code>cedula_asignado</code></td><td>Cédula del usuario asignado</td><td>❌</td></tr>
                        <tr><td><code>cargo_asignado</code></td><td>Cargo del usuario asignado</td><td>❌</td></tr>
                        <tr><td><code>valor_prudencial</code></td><td>Valor prudencial del bien (al menos 1 valor obligatorio)</td><td>❌</td></tr>
                        <tr><td><code>valor_adquisicion</code></td><td>Valor de adquisición (al menos 1 valor obligatorio)</td><td>❌</td></tr>
                        <tr><td><code>fecha_adquisicion</code></td><td>Fecha de adquisición (AAAA-MM-DD)</td><td>❌</td></tr>
                        <tr><td><code>observaciones</code></td><td>Notas u observaciones adicionales</td><td>❌</td></tr>
                    </tbody>
                </table>
            </div>
            <a href="{{ route('admin.bienes.plantilla') }}" class="btn btn-sm btn-outline-conapdis mt-2"><i class="bi bi-download"></i> Descargar Plantilla</a>
        </div>
        <form action="{{ route('admin.bienes.procesar-importacion') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Archivo Excel</label>
                <input type="file" name="archivo" class="form-control" accept=".xlsx,.xls,.csv" required>
            </div>
            <button type="submit" class="btn btn-conapdis"><i class="bi bi-upload"></i> Importar</button>
            <a href="{{ route('admin.bienes.index') }}" class="btn btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>
@endsection