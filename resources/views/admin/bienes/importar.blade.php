@extends('layouts.admin')
@section('title', 'Importar Bienes')
@section('page-title', 'Importar Bienes desde Excel')
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-file-excel me-2"></i>Importación Masiva</h5></div>
    <div class="card-body">
        <div class="alert alert-info">
            <h6 class="fw-bold"><i class="bi bi-info-circle me-2"></i>Formato del Excel</h6>
            <table class="table table-sm table-bordered mt-2">
                <thead class="table-header"><tr><th>Columna</th><th>Descripción</th><th>Obligatorio</th></tr></thead>
                <tbody>
                    <tr><td><code>codigo_inventario</code></td><td>Código único</td><td>✅</td></tr>
                    <tr><td><code>categoria</code></td><td>Nombre de categoría existente</td><td>✅</td></tr>
                    <tr><td><code>sede</code></td><td>Nombre de sede existente</td><td>✅</td></tr>
                    <tr><td><code>descripcion</code></td><td>Descripción del bien</td><td>✅</td></tr>
                    <tr><td><code>marca</code>, <code>modelo</code>, <code>serial</code></td><td>Datos opcionales</td><td>❌</td></tr>
                    <tr><td><code>color</code>, <code>material</code></td><td>Características</td><td>❌</td></tr>
                    <tr><td><code>estatus</code></td><td>Disponible/Asignado/etc</td><td>❌</td></tr>
                    <tr><td><code>usuario_asignado</code>, <code>cedula_asignado</code>, <code>cargo_asignado</code></td><td>Datos asignación</td><td>❌</td></tr>
                    <tr><td><code>valor_adquisicion</code>, <code>fecha_adquisicion</code></td><td>Datos económicos</td><td>❌</td></tr>
                </tbody>
            </table>
            <a href="{{ route('admin.bienes.plantilla') }}" class="btn btn-sm btn-outline-conapdis"><i class="bi bi-download"></i> Descargar Plantilla</a>
        </div>
        <form action="{{ route('admin.bienes.procesar-importacion') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Archivo Excel</label>
                <input type="file" name="archivo" class="form-control" accept=".xlsx,.xls,.csv" required>
            </div>
            <button type="submit" class="btn-conapdis"><i class="bi bi-upload"></i> Importar</button>
            <a href="{{ route('admin.bienes.index') }}" class="btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>
@endsection