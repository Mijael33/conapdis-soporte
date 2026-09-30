<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Valorización de Inventario - CONAPDIS</title>
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; }
        body { font-size: 9pt; color: #1e293b; margin: 0; padding: 15px; }
        .titulo-doc { text-align: center; font-size: 12pt; font-weight: 700; color: #001e5c; text-transform: uppercase; padding: 10px; background: #f4f6f9; border-top: 2px solid #003097; border-bottom: 2px solid #003097; margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .resumen th { background: #001e5c; color: white; padding: 6px 8px; font-size: 8pt; text-align: left; }
        .resumen td { padding: 5px 8px; border: 1px solid #e2e8f0; font-size: 8pt; }
        .resumen .text-end { text-align: right; }
        .resumen .text-center { text-align: center; }
        .total-row { background: #eff6ff; font-weight: 700; }
        .section-title { background: #001e5c; color: white; padding: 6px 10px; font-size: 9pt; font-weight: 700; text-transform: uppercase; margin-top: 15px; margin-bottom: 8px; }
        .pie { margin-top: 20px; padding-top: 10px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 7pt; color: #64748b; }
        .detail-table th { background: #64748b; color: white; padding: 4px 6px; font-size: 7pt; text-align: left; }
        .detail-table td { padding: 4px 6px; border: 1px solid #e2e8f0; font-size: 7pt; }
    </style>
</head>
<body>

    @include('pdf.partials.encabezado')

    <div class="titulo-doc">
        Valorización de Inventario
    </div>

    @if($filtros['fecha_desde'] || $filtros['fecha_hasta'])
    <div style="text-align: center; font-size: 8pt; color: #64748b; margin-bottom: 10px;">
        @if($filtros['fecha_desde']) Desde: {{ \Carbon\Carbon::parse($filtros['fecha_desde'])->format('d/m/Y') }} @endif
        @if($filtros['fecha_hasta']) | Hasta: {{ \Carbon\Carbon::parse($filtros['fecha_hasta'])->format('d/m/Y') }} @endif
    </div>
    @endif

    {{-- RESUMEN --}}
    <div class="section-title">Resumen por Módulo</div>
    <table class="resumen">
        <thead>
            <tr>
                <th>Módulo</th>
                <th class="text-center">Cantidad</th>
                <th class="text-end">Valor Prudencial (Bs.)</th>
                <th class="text-end">Valor Adquisición (Bs.)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Equipos de Tecnología</td>
                <td class="text-center">{{ number_format($equipos['cantidad'], 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($equipos['suma_prudencial'], 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($equipos['suma_adquisicion'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Componentes</td>
                <td class="text-center">{{ number_format($componentes['cantidad'], 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($componentes['suma_prudencial'], 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($componentes['suma_adquisicion'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Bienes Nacionales</td>
                <td class="text-center">{{ number_format($bienes['cantidad'], 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($bienes['suma_prudencial'], 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($bienes['suma_adquisicion'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Vehículos</td>
                <td class="text-center">{{ number_format($vehiculos['cantidad'], 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($vehiculos['suma_prudencial'], 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($vehiculos['suma_adquisicion'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Equipos de Sonido</td>
                <td class="text-center">{{ number_format($sonido['cantidad'], 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($sonido['suma_prudencial'], 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($sonido['suma_adquisicion'], 2, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>TOTAL GENERAL</td>
                <td class="text-center">{{ number_format($totales['cantidad'], 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($totales['suma_prudencial'], 2, ',', '.') }}</td>
                <td class="text-end">{{ number_format($totales['suma_adquisicion'], 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- DETALLES --}}
    @php
        $modulosPdf = [
            ['titulo' => 'Equipos de Tecnología', 'items' => $equipos['items'], 'codigo_field' => 'codigo_inventario_institucional'],
            ['titulo' => 'Componentes', 'items' => $componentes['items'], 'codigo_field' => 'serial_unico'],
            ['titulo' => 'Bienes Nacionales', 'items' => $bienes['items'], 'codigo_field' => 'codigo_inventario'],
            ['titulo' => 'Vehículos', 'items' => $vehiculos['items'], 'codigo_field' => 'placa'],
            ['titulo' => 'Equipos de Sonido', 'items' => $sonido['items'], 'codigo_field' => 'serial'],
        ];
    @endphp

    @foreach($modulosPdf as $mod)
        @if($mod['items']->count() > 0)
        <div class="section-title">{{ $mod['titulo'] }} ({{ $mod['items']->count() }})</div>
        <table class="detail-table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Sede</th>
                    <th class="text-end">Val. Prud.</th>
                    <th class="text-end">Val. Adq.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mod['items'] as $item)
                <tr>
                    <td>{{ $item->{$mod['codigo_field']} }}</td>
                    <td>{{ $item->marca }} {{ $item->modelo }}</td>
                    <td>{{ $item->sede->nombre_sede ?? 'N/A' }}</td>
                    <td class="text-end">{{ $item->valor_prudencial ? number_format($item->valor_prudencial, 2, ',', '.') : '-' }}</td>
                    <td class="text-end">{{ $item->valor_adquisicion ? number_format($item->valor_adquisicion, 2, ',', '.') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    @endforeach

    <div class="pie">
        Documento generado automáticamente por el Sistema de Gestión de Bienes CONAPDIS<br>
        Fecha de emisión: {{ date('d/m/Y H:i:s') }}
    </div>

</body>
</html>