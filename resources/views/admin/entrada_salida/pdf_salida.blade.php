<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Salida - {{ $registro->numero_comprobante }}</title>
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

        .badge-operativo { color: #4c7f36; font-weight: 700; }
        .badge-danios { color: #ef172f; font-weight: 700; }
        .badge-incompleto { color: #a16207; font-weight: 700; }

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
        Comprobante de Salida de Bien
    </div>

    <div class="section-title">Datos del Movimiento</div>
    <table class="datos-table">
        <tr>
            <td class="label">N° Comprobante</td>
            <td><strong>{{ $registro->numero_comprobante }}</strong></td>
            <td class="label">Tipo</td>
            <td><strong>Salida</strong></td>
        </tr>
        <tr>
            <td class="label">Tipo de Bien</td>
            <td>{{ $registro->bien_tipo }}</td>
            <td class="label">Fecha Registro</td>
            <td>{{ \App\Helpers\FechaHelper::formatear($registro->created_at) }}</td>
        </tr>
    </table>

    <div class="section-title">Datos del Bien</div>
    <table class="datos-table">
        <tr>
            <td class="label">Código Inventario</td>
            <td colspan="3"><strong>{{ $registro->bien_codigo }}</strong></td>
        </tr>
        <tr>
            <td class="label">Descripción</td>
            <td colspan="3">{{ $registro->bien_descripcion }}</td>
        </tr>
        <tr>
            <td class="label">Sede</td>
            <td>{{ $registro->sede->nombre_sede ?? 'N/A' }}</td>
            <td class="label">Estado</td>
            <td>{{ $registro->sede->estado->nombre ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="section-title">Datos de la Salida</div>
    <table class="datos-table">
        <tr>
            <td class="label">Fecha y Hora</td>
            <td colspan="3">
                <strong>{{ \App\Helpers\FechaHelper::formatear($registro->fecha_hora_salida) }}</strong>
            </td>
        </tr>
        <tr>
            <td class="label">Autorizado por</td>
            <td>{{ $registro->salida_autoriza_nombre }}</td>
            <td class="label">Cédula</td>
            <td>{{ $registro->salida_autoriza_cedula }}</td>
        </tr>
        <tr>
            <td class="label">Cargo</td>
            <td colspan="3">{{ $registro->salida_autoriza_cargo }}</td>
        </tr>
        <tr>
            <td class="label">Retirado por</td>
            <td>{{ $registro->salida_retira_nombre }}</td>
            <td class="label">Cédula</td>
            <td>{{ $registro->salida_retira_cedula }}</td>
        </tr>
        <tr>
            <td class="label">Cargo</td>
            <td colspan="3">{{ $registro->salida_retira_cargo }}</td>
        </tr>
        <tr>
            <td class="label">Motivo</td>
            <td colspan="3">{{ $registro->salida_motivo }}</td>
        </tr>
        <tr>
            <td class="label">Destino</td>
            <td colspan="3">{{ $registro->salida_destino }}</td>
        </tr>
        <tr>
            <td class="label">Estado al Salir</td>
            <td colspan="3">
                @if($registro->salida_estado_bien == 'Operativo')<span class="badge-operativo">✓ Operativo</span>
                @elseif($registro->salida_estado_bien == 'Con Daños')<span class="badge-danios">✗ Con Daños</span>
                @elseif($registro->salida_estado_bien == 'Incompleto')<span class="badge-incompleto">⚠ Incompleto</span>
                @else N/A @endif
            </td>
        </tr>
        @if($registro->salida_observaciones)
        <tr>
            <td class="label">Observaciones</td>
            <td colspan="3">{{ $registro->salida_observaciones }}</td>
        </tr>
        @endif
    </table>

    <div class="section-title">Registro de Seguridad - Salida</div>
    <table class="datos-table">
        <tr>
            <td class="label">Nombre del Funcionario</td>
            <td>{{ $registro->salida_seguridad_nombre }}</td>
            <td class="label">Cédula</td>
            <td>{{ $registro->salida_seguridad_cedula }}</td>
        </tr>
    </table>

    <div class="section-title">Información del Registro</div>
    <table class="datos-table">
        <tr>
            <td class="label">Registrado por</td>
            <td colspan="3">{{ $registro->salidaUsuario->name ?? 'N/A' }}</td>
        </tr>
    </table>

    @include('pdf.partials.firmas', [
        'firma1_nombre' => $registro->salida_retira_nombre,
        'firma1_cargo' => 'Persona que Retira',
        'firma1_cedula' => $registro->salida_retira_cedula,
        'firma2_nombre' => $registro->salida_seguridad_nombre,
        'firma2_cargo' => 'Funcionario de Seguridad',
        'firma2_cedula' => $registro->salida_seguridad_cedula,
        'firma3_nombre' => $registro->salida_autoriza_nombre,
        'firma3_cargo' => 'Autorizado por',
        'firma3_cedula' => $registro->salida_autoriza_cedula,
    ])

    <div class="pie">
        Documento generado automáticamente por el Sistema de Gestión de Bienes CONAPDIS<br>
        Comprobante N° {{ $registro->numero_comprobante }} | Emitido: {{ \App\Helpers\FechaHelper::formatear(now()) }}
    </div>

</body>
</html>