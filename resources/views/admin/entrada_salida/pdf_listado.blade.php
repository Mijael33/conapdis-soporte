<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Movimientos - CONAPDIS</title>
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; }
        body { font-size: 7pt; color: #1e293b; margin: 0; padding: 10px; }

        .titulo-doc {
            text-align: center;
            font-size: 10pt;
            font-weight: 700;
            color: #001e5c;
            text-transform: uppercase;
            padding: 6px;
            background: #f4f6f9;
            border-top: 2px solid #003097;
            border-bottom: 2px solid #003097;
            margin: 10px 0;
        }

        .info-doc {
            text-align: center;
            font-size: 7pt;
            color: #64748b;
            margin-bottom: 10px;
        }

        table { width: 100%; border-collapse: collapse; }
        .movimientos-table th {
            background: #001e5c;
            color: white;
            padding: 5px 4px;
            font-size: 6.5pt;
            text-align: left;
            text-transform: uppercase;
        }
        .movimientos-table td {
            padding: 4px 4px;
            border: 1px solid #e2e8f0;
            font-size: 6.5pt;
        }
        .movimientos-table tr:nth-child(even) { background: #f8fafc; }

        .badge-salida { background: #fef2f2; color: #ef172f; padding: 2px 5px; border-radius: 10px; font-weight: 600; }
        .badge-entrada { background: #ecfdf5; color: #4c7f36; padding: 2px 5px; border-radius: 10px; font-weight: 600; }

        .pie {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 6.5pt;
            color: #64748b;
        }
    </style>
</head>
<body>

    @include('pdf.partials.encabezado')

    <div class="titulo-doc">
        Listado de Movimientos de Bienes
    </div>

    <div class="info-doc">
        Total: <strong>{{ $registros->count() }}</strong> movimientos | Fecha: {{ date('d/m/Y H:i') }}
    </div>

    <table class="movimientos-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Bien</th>
                <th>Código</th>
                <th>Descripción</th>
                <th>Sede</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Retira/Entrega</th>
                <th>Seguridad</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registros as $reg)
            <tr>
                <td>{{ $reg->id }}</td>
                <td>
                    @if($reg->tipo == 'Salida')
                        <span class="badge-salida">SALIDA</span>
                    @else
                        <span class="badge-entrada">ENTRADA</span>
                    @endif
                </td>
                <td>{{ $reg->bien_tipo }}</td>
                <td>{{ $reg->codigo_inventario }}</td>
                <td>{{ Str::limit($reg->descripcion_bien, 25) }}</td>
                <td>{{ $reg->sede->nombre_sede ?? 'N/A' }}</td>
                <td>{{ $reg->sede->estado->nombre ?? 'N/A' }}</td>
                <td>
                    @if($reg->tipo == 'Salida')
                        {{ $reg->fecha_hora_salida ? $reg->fecha_hora_salida->format('d/m/Y H:i') : 'N/A' }}
                    @else
                        {{ $reg->fecha_hora_entrada ? $reg->fecha_hora_entrada->format('d/m/Y H:i') : 'N/A' }}
                    @endif
                </td>
                <td>{{ $reg->persona_retira_nombre ?? 'N/A' }}</td>
                <td>
                    @if($reg->tipo == 'Salida')
                        {{ $reg->seguridad_salida_nombre ?? 'N/A' }}
                    @else
                        {{ $reg->seguridad_entrada_nombre ?? 'N/A' }}
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align: center; color: #94a3b8; padding: 15px;">No hay movimientos registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pie">
        Documento generado automáticamente por el Sistema de Gestión de Bienes CONAPDIS<br>
        Fecha de emisión: {{ date('d/m/Y H:i:s') }}
    </div>

</body>
</html>