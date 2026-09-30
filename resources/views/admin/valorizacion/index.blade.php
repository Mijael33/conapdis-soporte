@extends('layouts.admin')
@section('title', 'Valorización de Inventario')
@section('page-title', 'Valorización de Inventario')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="mb-1 fw-bold" style="color: #1a3b5d;">
                        <i class="bi bi-cash-coin me-2"></i>Valorización de Inventario
                    </h5>
                    <small class="text-muted">Resumen de valores prudenciales y de adquisición de todo el inventario</small>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @can('valorizacion.exportar')
                    <a href="{{ route('admin.valorizacion.pdf', request()->query()) }}" class="btn-outline-conapdis btn-sm" target="_blank">
                        <i class="bi bi-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('admin.valorizacion.excel', request()->query()) }}" class="btn-outline-conapdis btn-sm">
                        <i class="bi bi-file-excel"></i> Excel
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Desde (fecha)</label>
                    <input type="date" name="fecha_desde" class="form-control rounded-3" value="{{ $filtros['fecha_desde'] }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Hasta (fecha)</label>
                    <input type="date" name="fecha_hasta" class="form-control rounded-3" value="{{ $filtros['fecha_hasta'] }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn-conapdis w-100">
                        <i class="bi bi-search"></i> Aplicar Filtros
                    </button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('admin.valorizacion.index') }}" class="btn-outline-conapdis w-100">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- TOTALES GENERALES --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4" style="border-left: 4px solid #003097;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: #dbeafe;">
                            <i class="bi bi-box-seam" style="font-size: 1.2rem; color: #003097;"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold" style="color: #1f2937;">{{ number_format($totales['cantidad'], 0, ',', '.') }}</h4>
                            <small class="text-muted">Total de Bienes Registrados</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4" style="border-left: 4px solid #16a34a;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: #dcfce7;">
                            <i class="bi bi-cash-coin" style="font-size: 1.2rem; color: #16a34a;"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold" style="color: #166534;">
                                {{ number_format($totales['suma_prudencial'], 2, ',', '.') }} Bs.
                            </h4>
                            <small class="text-muted">Total Valor Prudencial</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4" style="border-left: 4px solid #f59e0b;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: #fef3c7;">
                            <i class="bi bi-cash-stack" style="font-size: 1.2rem; color: #f59e0b;"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold" style="color: #a16207;">
                                {{ number_format($totales['suma_adquisicion'], 2, ',', '.') }} Bs.
                            </h4>
                            <small class="text-muted">Total Valor Adquisición</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLA RESUMEN POR MÓDULO --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold" style="color: #1a3b5d;">
                <i class="bi bi-list-ul me-2"></i>Resumen por Módulo
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-header">
                        <tr>
                            <th>Módulo</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Valor Prudencial (Bs.)</th>
                            <th class="text-end">Valor Adquisición (Bs.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold"><i class="bi bi-hdd-stack me-2" style="color: #2563eb;"></i>Equipos de Tecnología</td>
                            <td class="text-center">{{ number_format($equipos['cantidad'], 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($equipos['suma_prudencial'], 2, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($equipos['suma_adquisicion'], 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold"><i class="bi bi-memory me-2" style="color: #059669;"></i>Componentes</td>
                            <td class="text-center">{{ number_format($componentes['cantidad'], 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($componentes['suma_prudencial'], 2, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($componentes['suma_adquisicion'], 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold"><i class="bi bi-box-seam me-2" style="color: #db2777;"></i>Bienes Nacionales</td>
                            <td class="text-center">{{ number_format($bienes['cantidad'], 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($bienes['suma_prudencial'], 2, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($bienes['suma_adquisicion'], 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold"><i class="bi bi-truck me-2" style="color: #7c3aed;"></i>Vehículos</td>
                            <td class="text-center">{{ number_format($vehiculos['cantidad'], 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($vehiculos['suma_prudencial'], 2, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($vehiculos['suma_adquisicion'], 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold"><i class="bi bi-soundwave me-2" style="color: #0891b2;"></i>Equipos de Sonido</td>
                            <td class="text-center">{{ number_format($sonido['cantidad'], 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($sonido['suma_prudencial'], 2, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($sonido['suma_adquisicion'], 2, ',', '.') }}</td>
                        </tr>
                    </tbody>
                    <tfoot style="background: #f1f5f9; font-weight: 700;">
                        <tr>
                            <td class="fw-bold">TOTAL GENERAL</td>
                            <td class="text-center fw-bold">{{ number_format($totales['cantidad'], 0, ',', '.') }}</td>
                            <td class="text-end fw-bold" style="color: #166534;">{{ number_format($totales['suma_prudencial'], 2, ',', '.') }}</td>
                            <td class="text-end fw-bold" style="color: #a16207;">{{ number_format($totales['suma_adquisicion'], 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- DETALLES DESPLEGABLES POR MÓDULO --}}
    <h6 class="fw-bold mb-3" style="color: #1a3b5d;">
        <i class="bi bi-list-nested me-2"></i>Detalle por Bien
    </h6>

    @php
        $modulos = [
            ['titulo' => 'Equipos de Tecnología', 'key' => 'equipos', 'icon' => 'hdd-stack', 'color' => '#2563eb', 'items' => $equipos['items'], 'codigo_field' => 'codigo_inventario_institucional', 'desc_field' => null],
            ['titulo' => 'Componentes', 'key' => 'componentes', 'icon' => 'memory', 'color' => '#059669', 'items' => $componentes['items'], 'codigo_field' => 'serial_unico', 'desc_field' => null],
            ['titulo' => 'Bienes Nacionales', 'key' => 'bienes', 'icon' => 'box-seam', 'color' => '#db2777', 'items' => $bienes['items'], 'codigo_field' => 'codigo_inventario', 'desc_field' => 'descripcion'],
            ['titulo' => 'Vehículos', 'key' => 'vehiculos', 'icon' => 'truck', 'color' => '#7c3aed', 'items' => $vehiculos['items'], 'codigo_field' => 'placa', 'desc_field' => null],
            ['titulo' => 'Equipos de Sonido', 'key' => 'sonido', 'icon' => 'soundwave', 'color' => '#0891b2', 'items' => $sonido['items'], 'codigo_field' => 'serial', 'desc_field' => null],
        ];
    @endphp

    <div class="accordion mb-4" id="acordeonValorizacion">
        @foreach($modulos as $idx => $mod)
        <div class="accordion-item border-0 shadow-sm rounded-4 mb-3">
            <h2 class="accordion-header">
                <button class="accordion-button {{ $idx > 0 ? 'collapsed' : '' }} rounded-4 fw-bold" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#modulo-{{ $mod['key'] }}">
                    <i class="bi bi-{{ $mod['icon'] }} me-2" style="color: {{ $mod['color'] }};"></i>
                    {{ $mod['titulo'] }}
                    <span class="badge ms-2" style="background: {{ $mod['color'] }}20; color: {{ $mod['color'] }};">
                        {{ $mod['items']->count() }} item(s)
                    </span>
                </button>
            </h2>
            <div id="modulo-{{ $mod['key'] }}" class="accordion-collapse collapse {{ $idx == 0 ? 'show' : '' }}" data-bs-parent="#acordeonValorizacion">
                <div class="accordion-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Descripción / Marca</th>
                                    <th>Sede</th>
                                    <th class="text-end">Valor Prudencial</th>
                                    <th class="text-end">Valor Adquisición</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mod['items'] as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item->{$mod['codigo_field']} }}</td>
                                    <td>
                                        {{ $item->marca }} {{ $item->modelo }}
                                        @if($mod['desc_field'] && $item->{$mod['desc_field']})
                                            <br><small class="text-muted">{{ Str::limit($item->{$mod['desc_field']}, 60) }}</small>
                                        @endif
                                    </td>
                                    <td><small>{{ $item->sede->nombre_sede ?? 'N/A' }}</small></td>
                                    <td class="text-end">
                                        {{ $item->valor_prudencial ? number_format($item->valor_prudencial, 2, ',', '.') : '-' }}
                                    </td>
                                    <td class="text-end">
                                        {{ $item->valor_adquisicion ? number_format($item->valor_adquisicion, 2, ',', '.') : '-' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No hay registros</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection