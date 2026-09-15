<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha del Componente - {{ $componente->serial_unico }}</title>
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; }
        body { font-size: 9pt; color: #1e293b; margin: 0; padding: 15px; }

        table { width: 100%; border-collapse: collapse; }
        .datos-table { margin-bottom: 12px; }
        .datos-table td { padding: 5px 8px; border: 1px solid #e2e8f0; font-size: 9pt; vertical-align: top; }
        .datos-table td.label { background: #f8fafc; font-weight: 600; color: #001e5c; width: 25%; }

        .section-title {
            background: #001e5c;
            color: white;
            padding: 6px 10px;
            font-size: 9pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            margin-top: 12px;
        }

        .titulo-doc {
            text-align: center;
            font-size: 12pt;
            font-weight: 700;
            color: #001e5c;
            text-transform: uppercase;
            padding: 10px;
            background: #f4f6f9;
            border-top: 2px solid #003097;
            border-bottom: 2px solid #003097;
            margin: 15px 0;
        }

        .badge-disponible { color: #166534; font-weight: 700; }
        .badge-instalado { color: #1e40af; font-weight: 700; }
        .badge-revision { color: #a16207; font-weight: 700; }
        .badge-inoperativo { color: #ef172f; font-weight: 700; }

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
        Ficha Técnica de Componente
    </div>

    <div class="section-title">Datos del Componente</div>
    <table class="datos-table">
        <tr>
            <td class="label">Serial Único</td>
            <td><strong>{{ $componente->serial_unico }}</strong></td>
            <td class="label">Categoría</td>
            <td>{{ $componente->categoria->nombre ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Marca</td>
            <td>{{ $componente->marca }}</td>
            <td class="label">Modelo</td>
            <td>{{ $componente->modelo }}</td>
        </tr>
        <tr>
            <td class="label">Estado</td>
            <td>{{ $componente->sede->estado->nombre ?? 'N/A' }}</td>
            <td class="label">Sede</td>
            <td>{{ $componente->sede->nombre_sede ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Estatus</td>
            <td colspan="3">
                @if($componente->estatus == 'Disponible')<span class="badge-disponible">✓ Disponible</span>
                @elseif($componente->estatus == 'Instalado')<span class="badge-instalado">⚙ Instalado</span>
                @elseif($componente->estatus == 'En Revisión')<span class="badge-revision">⚠ En Revisión</span>
                @else<span class="badge-inoperativo">✗ {{ $componente->estatus }}</span>@endif
            </td>
        </tr>
    </table>

    @if($componente->caracteristicas_tecnicas && is_array($componente->caracteristicas_tecnicas))
    <div class="section-title">Características Técnicas</div>
    <table class="datos-table">
        @foreach($componente->caracteristicas_tecnicas as $clave => $valor)
        <tr>
            <td class="label" style="width: 30%;">{{ ucfirst($clave) }}</td>
            <td colspan="3">{{ $valor }}</td>
        </tr>
        @endforeach
    </table>
    @endif

    @if($componente->estatus == 'Instalado' && $componente->equipoActual->isNotEmpty())
    <div class="section-title">Instalado En</div>
    <table class="datos-table">
        @foreach($componente->equipoActual as $equipo)
        <tr>
            <td class="label">Código Equipo</td>
            <td>{{ $equipo->codigo_inventario_institucional }}</td>
            <td class="label">Fecha Instalación</td>
            <td>{{ $equipo->pivot->fecha_instalacion ? \Carbon\Carbon::parse($equipo->pivot->fecha_instalacion)->format('d/m/Y') : 'N/A' }}</td>
        </tr>
        @endforeach
    </table>
    @endif

    @if($componente->observaciones)
    <div class="section-title">Observaciones</div>
    <table class="datos-table">
        <tr><td colspan="4">{{ $componente->observaciones }}</td></tr>
    </table>
    @endif

    @include('pdf.partials.firmas', [
        'firma1_nombre' => '',
        'firma1_cargo' => 'Técnico Evaluador',
        'firma1_cedula' => '',
        'firma2_nombre' => '',
        'firma2_cargo' => 'Funcionario Receptor',
        'firma2_cedula' => '',
    ])

    <div class="pie">
        Documento generado automáticamente por el Sistema de Gestión de Bienes CONAPDIS<br>
        Fecha de emisión: {{ date('d/m/Y H:i:s') }}
    </div>

</body>
</html>