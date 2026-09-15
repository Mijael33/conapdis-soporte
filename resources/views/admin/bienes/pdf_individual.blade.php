<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha del Bien - {{ $bien->codigo_inventario }}</title>
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; }
        body { font-size: 9pt; color: #1e293b; margin: 0; padding: 15px; }
        table { width: 100%; border-collapse: collapse; }
        .datos-table { margin-bottom: 12px; }
        .datos-table td { padding: 5px 8px; border: 1px solid #e2e8f0; font-size: 9pt; vertical-align: top; }
        .datos-table td.label { background: #f8fafc; font-weight: 600; color: #001e5c; width: 25%; }
        .section-title { background: #001e5c; color: white; padding: 6px 10px; font-size: 9pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; margin-top: 12px; }
        .titulo-doc { text-align: center; font-size: 12pt; font-weight: 700; color: #001e5c; text-transform: uppercase; padding: 10px; background: #f4f6f9; border-top: 2px solid #003097; border-bottom: 2px solid #003097; margin: 15px 0; }
        .badge-disponible { color: #166534; font-weight: 700; }
        .badge-instalado { color: #1e40af; font-weight: 700; }
        .badge-revision { color: #a16207; font-weight: 700; }
        .badge-inoperativo { color: #ef172f; font-weight: 700; }
        .pie { margin-top: 20px; padding-top: 10px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 7pt; color: #64748b; }
    </style>
</head>
<body>

    @include('pdf.partials.encabezado')

    <div class="titulo-doc">
        Ficha Técnica de Bien Nacional
    </div>

    <div class="section-title">Datos del Bien</div>
    <table class="datos-table">
        <tr>
            <td class="label">Código Inventario</td>
            <td><strong>{{ $bien->codigo_inventario }}</strong></td>
            <td class="label">Categoría</td>
            <td>{{ $bien->categoria->nombre ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Descripción</td>
            <td colspan="3">{{ $bien->descripcion }}</td>
        </tr>
        <tr>
            <td class="label">Marca</td>
            <td>{{ $bien->marca ?? 'N/A' }}</td>
            <td class="label">Modelo</td>
            <td>{{ $bien->modelo ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Serial</td>
            <td>{{ $bien->serial ?? 'N/A' }}</td>
            <td class="label">Color</td>
            <td>{{ $bien->color ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Material</td>
            <td colspan="3">{{ $bien->material ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="section-title">Ubicación</div>
    <table class="datos-table">
        <tr>
            <td class="label">Estado</td>
            <td>{{ $bien->sede->estado->nombre ?? 'N/A' }}</td>
            <td class="label">Sede</td>
            <td>{{ $bien->sede->nombre_sede ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Estatus</td>
            <td colspan="3">
                @if($bien->estatus == 'Disponible')<span class="badge-disponible">✓ Disponible</span>
                @elseif($bien->estatus == 'Asignado')<span class="badge-instalado">⚙ Asignado</span>
                @elseif($bien->estatus == 'En Mantenimiento')<span class="badge-revision">⚠ En Mantenimiento</span>
                @else<span class="badge-inoperativo">✗ {{ $bien->estatus }}</span>@endif
            </td>
        </tr>
    </table>

    <div class="section-title">Datos de Adquisición</div>
    <table class="datos-table">
        <tr>
            <td class="label">Valor (Bs.)</td>
            <td>{{ $bien->valor_adquisicion ? number_format($bien->valor_adquisicion, 2, ',', '.') : 'N/A' }}</td>
            <td class="label">Fecha Adquisición</td>
            <td>{{ $bien->fecha_adquisicion ? $bien->fecha_adquisicion->format('d/m/Y') : 'N/A' }}</td>
        </tr>
    </table>

    @if($bien->usuario_asignado_nombre)
    <div class="section-title">Usuario Asignado</div>
    <table class="datos-table">
        <tr>
            <td class="label">Nombre</td>
            <td>{{ $bien->usuario_asignado_nombre }}</td>
            <td class="label">Cédula</td>
            <td>{{ $bien->usuario_asignado_cedula ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Cargo</td>
            <td colspan="3">{{ $bien->usuario_asignado_cargo ?? 'N/A' }}</td>
        </tr>
    </table>
    @endif

    @if($bien->observaciones)
    <div class="section-title">Observaciones</div>
    <table class="datos-table">
        <tr><td colspan="4">{{ $bien->observaciones }}</td></tr>
    </table>
    @endif

    @include('pdf.partials.firmas', [
        'firma1_nombre' => '',
        'firma1_cargo' => 'Técnico Evaluador',
        'firma1_cedula' => '',
        'firma2_nombre' => $bien->usuario_asignado_nombre ?? '',
        'firma2_cargo' => 'Funcionario Receptor',
        'firma2_cedula' => $bien->usuario_asignado_cedula ?? '',
    ])

    <div class="pie">
        Documento generado automáticamente por el Sistema de Gestión de Bienes CONAPDIS<br>
        Fecha de emisión: {{ date('d/m/Y H:i:s') }}
    </div>

</body>
</html>