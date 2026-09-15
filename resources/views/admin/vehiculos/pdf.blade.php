<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Vehículos - CONAPDIS</title>
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
        .vehiculos-table th {
            background: #001e5c;
            color: white;
            padding: 6px 6px;
            font-size: 7pt;
            text-align: left;
            text-transform: uppercase;
        }
        .vehiculos-table td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            font-size: 7pt;
        }
        .vehiculos-table tr:nth-child(even) { background: #f8fafc; }

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
        Listado de Vehículos
    </div>

    <div class="info-doc">
        Total: <strong>{{ $vehiculos->count() }}</strong> vehículos | Fecha: {{ date('d/m/Y H:i') }}
    </div>

    <table class="vehiculos-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Placa</th>
                <th>Categoría</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Estado</th>
                <th>Sede</th>
                <th>Estatus</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vehiculos as $v)
            <tr>
                <td>{{ $v->codigo_inventario }}</td>
                <td>{{ $v->placa }}</td>
                <td>{{ $v->categoria->nombre ?? 'N/A' }}</td>
                <td>{{ $v->marca }}</td>
                <td>{{ $v->modelo }}</td>
                <td>{{ $v->anio ?? '-' }}</td>
                <td>{{ $v->sede->estado->nombre ?? 'N/A' }}</td>
                <td>{{ $v->sede->nombre_sede ?? 'N/A' }}</td>
                <td>{{ $v->estatus }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center; color: #94a3b8; padding: 15px;">No hay vehículos registrados</td>
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