@extends('layouts.admin')
@section('title', 'Importar Vehículos')
@section('page-title', 'Importar Vehículos desde Excel')
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-file-excel me-2"></i>Importación Masiva</h5></div>
    <div class="card-body">
        <div class="alert alert-info">
            <h6 class="fw-bold"><i class="bi bi-info-circle me-2"></i>Formato del Excel</h6>
            <p class="mb-1">El archivo debe tener las siguientes columnas en la PRIMERA fila (encabezados):</p>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mt-2">
                    <thead class="table-header">
                        <tr><th>Columna</th><th>Descripción</th><th>Obligatorio</th></tr>
                    </thead>
                    <tbody>
                        <tr><td><code>codigo_inventario</code></td><td>Código de inventario único</td><td>✅</td></tr>
                        <tr><td><code>categoria</code></td><td>Nombre de categoría (Sedán, Camioneta, etc.)</td><td>✅</td></tr>
                        <tr><td><code>sede</code></td><td>Nombre de sede (debe existir o se crea automáticamente)</td><td>✅</td></tr>
                        <tr><td><code>placa</code></td><td>Placa única del vehículo</td><td>✅</td></tr>
                        <tr><td><code>marca</code></td><td>Marca del vehículo</td><td>✅</td></tr>
                        <tr><td><code>modelo</code></td><td>Modelo del vehículo</td><td>✅</td></tr>
                        <tr><td><code>anio</code></td><td>Año del vehículo</td><td>❌</td></tr>
                        <tr><td><code>color</code></td><td>Color</td><td>❌</td></tr>
                        <tr><td><code>serial_motor</code></td><td>Serial del motor</td><td>❌</td></tr>
                        <tr><td><code>serial_carroceria</code></td><td>Serial de la carrocería</td><td>❌</td></tr>
                        <tr><td><code>kilometraje</code></td><td>Kilometraje actual</td><td>❌</td></tr>
                        <tr><td><code>estatus</code></td><td>Disponible, Asignado, En Mantenimiento, Desincorporado (por defecto: Disponible)</td><td>❌</td></tr>
                        <tr><td><code>usuario_asignado_nombre</code></td><td>Nombre del usuario asignado</td><td>❌</td></tr>
                        <tr><td><code>usuario_asignado_cedula</code></td><td>Cédula del usuario asignado</td><td>❌</td></tr>
                        <tr><td><code>usuario_asignado_cargo</code></td><td>Cargo del usuario asignado</td><td>❌</td></tr>
                        <tr><td><code>valor_prudencial</code></td><td>Valor prudencial en Bs. (al menos 1 valor obligatorio)</td><td>❌</td></tr>
                        <tr><td><code>valor_adquisicion</code></td><td>Valor de adquisición en Bs. (al menos 1 valor obligatorio)</td><td>❌</td></tr>
                        <tr><td><code>observaciones</code></td><td>Notas adicionales</td><td>❌</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-2">
                <a href="{{ route('admin.vehiculos.plantilla') }}" class="btn btn-sm btn-outline-conapdis">
                    <i class="bi bi-download"></i> Descargar Plantilla de Ejemplo
                </a>
            </div>
        </div>
        <form action="{{ route('admin.vehiculos.procesar-importacion') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Archivo Excel</label>
                <input type="file" name="archivo" class="form-control" accept=".xlsx,.xls,.csv" required>
                @error('archivo') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <button type="submit" class="btn-conapdis"><i class="bi bi-upload"></i> Importar</button>
            <a href="{{ route('admin.vehiculos.index') }}" class="btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>
@endsection