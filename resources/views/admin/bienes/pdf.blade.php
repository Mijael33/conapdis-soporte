<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Bienes - CONAPDIS</title>
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
        .bienes-table th {
            background: #001e5c;
            color: white;
            padding: 6px 6px;
            font-size: 7pt;
            text-align: left;
            text-transform: uppercase;
        }
        .bienes-table td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            font-size: 7pt;
        }
        .bienes-table tr:nth-child(even) { background: #f8fafc; }

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
        Listado de Bienes Nacionales
    </div>

    <div class="info-doc">
        Total: <strong>{{ $bienes->count() }}</strong> bienes | Fecha: {{ date('d/m/Y H:i') }}
    </div>

    <table class="bienes-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Categoría</th>
                <th>Descripción</th>
                <th>Marca</th>
                <th>Estado</th>
                <th>Sede</th>
                <th>Estatus</th>
                <th>Asignado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bienes as $b)
            <tr>
                <td>{{ $b->codigo_inventario }}</td>
                <td>{{ $b->categoria->nombre ?? 'N/A' }}</td>
                <td>{{ Str::limit($b->descripcion, 40) }}</td>
                <td>{{ $b->marca ?? '-' }}</td>
                <td>{{ $b->sede->estado->nombre ?? 'N/A' }}</td>
                <td>{{ $b->sede->nombre_sede ?? 'N/A' }}</td>
                <td>{{ $b->estatus }}</td>
                <td>{{ $b->usuario_asignado_nombre ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; color: #94a3b8; padding: 15px;">No hay bienes registrados</td>
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