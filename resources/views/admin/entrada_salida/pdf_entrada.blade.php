<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Entrada - {{ $registro->numero_comprobante }}</title>
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
        .badge-no-retorno { color: #ef172f; font-weight: 700; }

        .comparacion-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .comparacion-table th {
            background: #001e5c;
            color: white;
            padding: 6px;
            font-size: 8pt;
            text-align: center;
        }

        .comparacion-table td {
            padding: 6px;
            border: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9pt;
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

    @include('pdf.partials.encabezado')

    <div class="titulo-doc">
        Comprobante de Entrada de Bien
    </div>

    <div class="section-title">Datos del Movimiento</div>
    <table class="datos-table">
        <tr>
            <td class="label">N° Comprobante</td>
            <td><strong>{{ $registro->numero_comprobante }}</strong></td>
            <td class="label">Tipo</td>
            <td><strong>Entrada</strong></td>
        </tr>
        <tr>
            <td class="label">Tipo de Bien</td>
            <td>{{ $registro->bien_tipo }}</td>
            <td class="label">Estatus</td>
            <td><strong>Completado</strong></td>
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

    <div class="section-title">Resumen del Movimiento</div>
    <table class="datos-table">
        <tr>
            <td class="label">Fecha de Salida</td>
            <td>{{ \App\Helpers\FechaHelper::formatear($registro->fecha_hora_salida) }}</td>
            <td class="label">Fecha de Entrada</td>
            <td>{{ \App\Helpers\FechaHelper::formatear($registro->fecha_hora_entrada) }}</td>
        </tr>
        <tr>
            <td class="label">Duración Total</td>
            <td colspan="3"><strong>{{ $registro->duracion ?? 'N/A' }}</strong></td>
        </tr>
    </table>

    <div class="section-title">Comparación de Estados del Bien</div>
    <table class="comparacion-table">
        <thead>
            <tr>
                <th style="width: 25%;">Momento</th>
                <th style="width: 25%;">Estado</th>
                <th style="width: 50%;">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Al Salir</strong></td>
                <td>
                    @if($registro->salida_estado_bien == 'Operativo')<span class="badge-operativo">✓ Operativo</span>
                    @elseif($registro->salida_estado_bien == 'Con Daños')<span class="badge-danios">✗ Con Daños</span>
                    @elseif($registro->salida_estado_bien == 'Incompleto')<span class="badge-incompleto">⚠ Incompleto</span>
                    @else N/A @endif
                </td>
                <td>{{ $registro->salida_observaciones ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Al Regresar</strong></td>
                <td>
                    @if($registro->entrada_estado_bien == 'Operativo')<span class="badge-operativo">✓ Operativo</span>
                    @elseif($registro->entrada_estado_bien == 'Con Daños')<span class="badge-danios">✗ Con Daños</span>
                    @elseif($registro->entrada_estado_bien == 'Incompleto')<span class="badge-incompleto">⚠ Incompleto</span>
                    @elseif($registro->entrada_estado_bien == 'No Retornó')<span class="badge-no-retorno">✗ No Retornó</span>
                    @else N/A @endif
                </td>
                <td>{{ $registro->entrada_observaciones ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Datos de la Salida Original</div>
    <table class="datos-table">
        <tr>
            <td class="label">Retirado por</td>
            <td>{{ $registro->salida_retira_nombre }}</td>
            <td class="label">Cédula</td>
            <td>{{ $registro->salida_retira_cedula }}</td>
        </tr>
        <tr>
            <td class="label">Seguridad Salida</td>
            <td>{{ $registro->salida_seguridad_nombre }}</td>
            <td class="label">Cédula</td>
            <td>{{ $registro->salida_seguridad_cedula }}</td>
        </tr>
        <tr>
            <td class="label">Motivo</td>
            <td colspan="3">{{ $registro->salida_motivo }}</td>
        </tr>
        <tr>
            <td class="label">Destino</td>
            <td colspan="3">{{ $registro->salida_destino }}</td>
        </tr>
    </table>

    <div class="section-title">Datos de la Entrada</div>
    <table class="datos-table">
        <tr>
            <td class="label">Fecha y Hora</td>
            <td colspan="3"><strong>{{ \App\Helpers\FechaHelper::formatear($registro->fecha_hora_entrada) }}</strong></td>
        </tr>
        <tr>
            <td class="label">Entregado por</td>
            <td>{{ $registro->entrada_recibe_nombre }}</td>
            <td class="label">Cédula</td>
            <td>{{ $registro->entrada_recibe_cedula }}</td>
        </tr>
    </table>

    <div class="section-title">Registro de Seguridad - Entrada</div>
    <table class="datos-table">
        <tr>
            <td class="label">Nombre del Funcionario</td>
            <td>{{ $registro->entrada_seguridad_nombre }}</td>
            <td class="label">Cédula</td>
            <td>{{ $registro->entrada_seguridad_cedula }}</td>
        </tr>
    </table>

    <div class="section-title">Información del Registro</div>
    <table class="datos-table">
        <tr>
            <td class="label">Salida registrada por</td>
            <td>{{ $registro->salidaUsuario->name ?? 'N/A' }}</td>
            <td class="label">Entrada registrada por</td>
            <td>{{ $registro->entradaUsuario->name ?? 'N/A' }}</td>
        </tr>
    </table>

    @include('pdf.partials.firmas', [
        'firma1_nombre' => $registro->entrada_recibe_nombre,
        'firma1_cargo' => 'Persona que Entrega',
        'firma1_cedula' => $registro->entrada_recibe_cedula,
        'firma2_nombre' => $registro->entrada_seguridad_nombre,
        'firma2_cargo' => 'Funcionario de Seguridad',
        'firma2_cedula' => $registro->entrada_seguridad_cedula,
        'firma3_nombre' => $registro->entradaUsuario->name ?? '',
        'firma3_cargo' => 'Funcionario que Registra',
        'firma3_cedula' => '',
    ])

    <div class="pie">
        Documento generado automáticamente por el Sistema de Gestión de Bienes CONAPDIS<br>
        Comprobante N° {{ $registro->numero_comprobante }} | Emitido: {{ \App\Helpers\FechaHelper::formatear(now()) }}
    </div>

</body>
</html>