<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Componentes - CONAPDIS</title>
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; }
        body { font-size: 8pt; color: #1e293b; margin: 0; padding: 15px; }

        .titulo-doc {
            text-align: center;
            font-size: 11pt;
            font-weight: 700;
            color: #001e5c;
            text-transform: uppercase;
            padding: 8px;
            background: #f4f6f9;
            border-top: 2px solid #003097;
            border-bottom: 2px solid #003097;
            margin: 12px 0;
        }

        .info-doc {
            text-align: center;
            font-size: 8pt;
            color: #64748b;
            margin-bottom: 10px;
        }

        table { width: 100%; border-collapse: collapse; }
        .componentes-table th {
            background: #001e5c;
            color: white;
            padding: 6px 6px;
            font-size: 7pt;
            text-align: left;
            text-transform: uppercase;
        }
        .componentes-table td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            font-size: 7pt;
        }
        .componentes-table tr:nth-child(even) { background: #f8fafc; }

        .pie {
            margin-top: 20px;
            padding-top: 10px;
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
        Listado de Componentes
    </div>

    <div class="info-doc">
        Total: <strong>{{ $componentes->count() }}</strong> componentes | Fecha: {{ date('d/m/Y H:i') }}
    </div>

    <table class="componentes-table">
        <thead>
            <tr>
                <th>Serial</th>
                <th>Categoría</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Estado</th>
                <th>Sede</th>
                <th>Estatus</th>
                <th>Equipo Actual</th>
            </tr>
        </thead>
        <tbody>
            @forelse($componentes as $c)
            <tr>
                <td>{{ $c->serial_unico }}</td>
                <td>{{ $c->categoria->nombre ?? 'N/A' }}</td>
                <td>{{ $c->marca }}</td>
                <td>{{ $c->modelo }}</td>
                <td>{{ $c->sede->estado->nombre ?? 'N/A' }}</td>
                <td>{{ $c->sede->nombre_sede ?? 'N/A' }}</td>
                <td>{{ $c->estatus }}</td>
                <td>{{ $c->equipoActual->first()->codigo_inventario_institucional ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; color: #94a3b8; padding: 15px;">No hay componentes registrados</td>
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