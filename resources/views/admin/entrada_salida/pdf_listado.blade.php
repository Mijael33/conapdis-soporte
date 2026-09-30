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

        .badge-pendiente { background: #fef3c7; color: #a16207; padding: 2px 5px; border-radius: 10px; font-weight: 600; }
        .badge-completado { background: #dcfce7; color: #166534; padding: 2px 5px; border-radius: 10px; font-weight: 600; }

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
        Listado de Movimientos de Entrada/Salida
    </div>

    <div class="info-doc">
        Total: <strong>{{ $registros->count() }}</strong> movimientos | Fecha: {{ \App\Helpers\FechaHelper::formatear(now()) }}
    </div>

    <table class="movimientos-table">
        <thead>
            <tr>
                <th>N°</th>
                <th>Bien</th>
                <th>Sede</th>
                <th>Salida</th>
                <th>Retira</th>
                <th>Entrada</th>
                <th>Entrega</th>
                <th>Estatus</th>
                <th>Duración</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registros as $reg)
            <tr>
                <td>{{ $reg->numero_comprobante }}</td>
                <td>
                    <strong>{{ $reg->bien_codigo }}</strong><br>
                    <small>{{ Str::limit($reg->bien_descripcion, 20) }}</small>
                </td>
                <td>{{ Str::limit($reg->sede->nombre_sede ?? 'N/A', 15) }}</td>
                <td>{{ \App\Helpers\FechaHelper::formatear($reg->fecha_hora_salida) }}</td>
                <td>{{ Str::limit($reg->salida_retira_nombre ?? '-', 15) }}</td>
                <td>{{ \App\Helpers\FechaHelper::formatear($reg->fecha_hora_entrada) }}</td>
                <td>{{ Str::limit($reg->entrada_recibe_nombre ?? '-', 15) }}</td>
                <td>
                    @if($reg->estatus == 'Pendiente')
                        <span class="badge-pendiente">PENDIENTE</span>
                    @else
                        <span class="badge-completado">COMPLETADO</span>
                    @endif
                </td>
                <td>{{ $reg->duracion ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; color: #94a3b8; padding: 15px;">
                    No hay movimientos registrados
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pie">
        Documento generado automáticamente por el Sistema de Gestión de Bienes CONAPDIS<br>
        Fecha de emisión: {{ \App\Helpers\FechaHelper::formatear(now()) }}
    </div>

</body>
</html>