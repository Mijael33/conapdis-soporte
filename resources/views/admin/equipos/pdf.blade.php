<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Equipos - CONAPDIS</title>
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; box-sizing: border-box; }
        body { font-size: 8pt; color: #1e293b; margin: 0; padding: 10px; }

        .titulo-doc {
            text-align: center;
            font-size: 11pt;
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
            font-size: 8pt;
            color: #64748b;
            margin-bottom: 8px;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; 
        }

        .equipos-table th {
            background: #001e5c;
            color: white;
            padding: 6px 4px;
            font-size: 6.5pt;
            text-align: left;
            text-transform: uppercase;
            word-wrap: break-word;
        }

        .equipos-table td {
            padding: 5px 4px;
            border: 1px solid #e2e8f0;
            font-size: 6.5pt;
            word-wrap: break-word;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .col-codigo { width: 13%; }
        .col-tipo { width: 11%; }
        .col-marca { width: 16%; }
        .col-estado { width: 12%; }
        .col-estatus { width: 12%; }
        .col-usuario { width: 16%; }
        .col-valor-p { width: 10%; }
        .col-valor-a { width: 10%; }

        .equipos-table tr:nth-child(even) { background: #f8fafc; }

        tr { page-break-inside: avoid; }

        .pie {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 7pt;
            color: #64748b;
        }
    </style>
</head>
<body>

    @include('pdf.partials.encabezado')

    <div class="titulo-doc">
        Listado de Equipos
    </div>

    <div class="info-doc">
        Total: <strong>{{ $equipos->count() }}</strong> equipos | Fecha: {{ date('d/m/Y H:i') }}
    </div>

    <table class="equipos-table">
        <thead>
            <tr>
                <th class="col-codigo">Código</th>
                <th class="col-tipo">Tipo</th>
                <th class="col-marca">Marca/Modelo</th>
                <th class="col-estado">Estado</th>
                <th class="col-estatus">Estatus</th>
                <th class="col-usuario">Usuario</th>
                <th class="col-valor-p">Val. Prud.</th>
                <th class="col-valor-a">Val. Adq.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($equipos as $e)
            <tr>
                <td class="col-codigo">{{ $e->codigo_inventario_institucional }}</td>
                <td class="col-tipo">{{ $e->tipoEquipo->nombre ?? 'N/A' }}</td>
                <td class="col-marca">{{ $e->marca }} {{ $e->modelo }}</td>
                <td class="col-estado">{{ $e->sede->estado->nombre ?? 'N/A' }}</td>
                <td class="col-estatus">{{ $e->estatus_general }}</td>
                <td class="col-usuario">{{ $e->usuario_asignado_nombre ?? '-' }}</td>
                <td class="col-valor-p">{{ $e->valor_prudencial ? number_format($e->valor_prudencial, 2, ',', '.') : '-' }}</td>
                <td class="col-valor-a">{{ $e->valor_adquisicion ? number_format($e->valor_adquisicion, 2, ',', '.') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; color: #94a3b8; padding: 15px;">No hay equipos registrados</td>
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