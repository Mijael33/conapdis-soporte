<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Servicio - {{ $ordene->codigo_ticket }}</title>
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

    {{-- ENCABEZADO INSTITUCIONAL --}}
    @include('pdf.partials.encabezado')

    {{-- TÍTULO --}}
    <div style="text-align: center; font-size: 12pt; font-weight: 700; color: #001e5c; text-transform: uppercase; padding: 10px; background: #f4f6f9; border-top: 2px solid #003097; border-bottom: 2px solid #003097; margin: 15px 0;">
        Orden de Servicio Técnico
    </div>

    {{-- DATOS DE LA ORDEN --}}
    <div class="section-title">Datos de la Orden</div>
    <table class="datos-table">
        <tr>
            <td class="label">Ticket</td>
            <td><strong>{{ $ordene->codigo_ticket }}</strong></td>
            <td class="label">Estatus</td>
            <td>{{ $ordene->estatus_final ?? 'Abierta' }}</td>
        </tr>
        <tr>
            <td class="label">Fecha Inicio</td>
            <td>{{ $ordene->fecha_inicio ? $ordene->fecha_inicio->format('d/m/Y H:i') : 'N/A' }}</td>
            <td class="label">Fecha Cierre</td>
            <td>{{ $ordene->fecha_cierre ? $ordene->fecha_cierre->format('d/m/Y H:i') : 'Pendiente' }}</td>
        </tr>
    </table>

    {{-- DATOS DEL EQUIPO --}}
    <div class="section-title">Datos del Equipo</div>
    <table class="datos-table">
        <tr>
            <td class="label">Código</td>
            <td>{{ $ordene->equipo->codigo_inventario_institucional }}</td>
            <td class="label">Marca / Modelo</td>
            <td>{{ $ordene->equipo->marca }} {{ $ordene->equipo->modelo }}</td>
        </tr>
        <tr>
            <td class="label">Estado</td>
            <td>{{ $ordene->equipo->departamento->sede->estado->nombre }}</td>
            <td class="label">Sede</td>
            <td>{{ $ordene->equipo->departamento->sede->nombre_sede }}</td>
        </tr>
        <tr>
            <td class="label">Departamento</td>
            <td colspan="3">{{ $ordene->equipo->departamento->nombre_departamento }}</td>
        </tr>
    </table>

    {{-- TÉCNICO --}}
    <div class="section-title">Técnico Responsable</div>
    <table class="datos-table">
        <tr>
            <td class="label">Nombre</td>
            <td colspan="3">{{ $ordene->tecnico->name ?? 'N/A' }}</td>
        </tr>
    </table>

    {{-- PROBLEMA Y DIAGNÓSTICO --}}
    <div class="section-title">Reporte y Diagnóstico</div>
    <table class="datos-table">
        <tr>
            <td class="label">Problema Reportado</td>
            <td colspan="3">{{ $ordene->problema_reportado_usuario }}</td>
        </tr>
        <tr>
            <td class="label">Diagnóstico Técnico</td>
            <td colspan="3">{{ $ordene->diagnostico_tecnico ?? 'Pendiente' }}</td>
        </tr>
        <tr>
            <td class="label">Acciones Realizadas</td>
            <td colspan="3">{{ $ordene->acciones_realizadas ?? 'Pendiente' }}</td>
        </tr>
    </table>

    {{-- FIRMAS --}}
    @include('pdf.partials.firmas', [
        'firma1_nombre' => $ordene->tecnico->name ?? 'Técnico',
        'firma1_cargo' => 'Técnico Responsable',
        'firma2_nombre' => '',
        'firma2_cargo' => 'Coordinador de Soporte',
        'firma3_nombre' => $ordene->equipo->usuario_asignado_nombre ?? '',
        'firma3_cargo' => 'Usuario Receptor',
    ])

    {{-- PIE --}}
    <div class="pie">
        Documento generado automáticamente por el Sistema de Gestión de Bienes CONAPDIS<br>
        Fecha de emisión: {{ date('d/m/Y H:i:s') }}
    </div>

</body>
</html>