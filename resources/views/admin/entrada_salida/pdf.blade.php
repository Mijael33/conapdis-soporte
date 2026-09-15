<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de {{ $registro->tipo }} - CONAPDIS</title>
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
        Comprobante de {{ $registro->tipo }} de Bien
    </div>

    <div class="section-title">Datos del Movimiento</div>
    <table class="datos-table">
        <tr>
            <td class="label">N° Comprobante</td>
            <td><strong>CONAPDIS-{{ $registro->tipo }}-{{ str_pad($registro->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
            <td class="label">Tipo</td>
            <td><strong>{{ $registro->tipo }}</strong></td>
        </tr>
        <tr>
            <td class="label">Tipo de Bien</td>
            <td>{{ $registro->bien_tipo }}</td>
            <td class="label">Fecha Registro</td>
            <td>{{ $registro->created_at->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <div class="section-title">Datos del Bien</div>
    <table class="datos-table">
        <tr>
            <td class="label">Código Inventario</td>
            <td colspan="3"><strong>{{ $registro->codigo_inventario }}</strong></td>
        </tr>
        <tr>
            <td class="label">Descripción</td>
            <td colspan="3">{{ $registro->descripcion_bien }}</td>
        </tr>
        <tr>
            <td class="label">Sede</td>
            <td>{{ $registro->sede->nombre_sede ?? 'N/A' }}</td>
            <td class="label">Estado</td>
            <td>{{ $registro->sede->estado->nombre ?? 'N/A' }}</td>
        </tr>
    </table>

    @if($registro->tipo == 'Salida')
    <div class="section-title">Datos de Salida</div>
    <table class="datos-table">
        <tr>
            <td class="label">Fecha y Hora</td>
            <td colspan="3">{{ $registro->fecha_hora_salida ? $registro->fecha_hora_salida->format('d/m/Y H:i') : 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Autorizado por</td>
            <td>{{ $registro->autorizado_por_nombre ?? 'N/A' }}</td>
            <td class="label">C.I.</td>
            <td>{{ $registro->autorizado_por_cedula ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Cargo Autorizado</td>
            <td colspan="3">{{ $registro->autorizado_por_cargo ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Retirado por</td>
            <td>{{ $registro->persona_retira_nombre ?? 'N/A' }}</td>
            <td class="label">C.I.</td>
            <td>{{ $registro->persona_retira_cedula ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Cargo Retira</td>
            <td colspan="3">{{ $registro->persona_retira_cargo ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Motivo</td>
            <td colspan="3">{{ $registro->motivo ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Destino</td>
            <td colspan="3">{{ $registro->destino ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Estado al Salir</td>
            <td colspan="3">
                @if($registro->estado_salida == 'Operativo')<span class="badge-operativo">✓ Operativo</span>
                @elseif($registro->estado_salida == 'Con Daños')<span class="badge-danios">✗ Con Daños</span>
                @elseif($registro->estado_salida == 'Incompleto')<span class="badge-incompleto">⚠ Incompleto</span>
                @else N/A @endif
            </td>
        </tr>
        @if($registro->observaciones_salida)
        <tr>
            <td class="label">Observaciones</td>
            <td colspan="3">{{ $registro->observaciones_salida }}</td>
        </tr>
        @endif
    </table>

    <div class="section-title">Registro de Seguridad - Salida</div>
    <table class="datos-table">
        <tr>
            <td class="label">Nombre del Funcionario</td>
            <td>{{ $registro->seguridad_salida_nombre ?? 'N/A' }}</td>
            <td class="label">C.I.</td>
            <td>{{ $registro->seguridad_salida_cedula ?? 'N/A' }}</td>
        </tr>
    </table>
    @else
    <div class="section-title">Datos de Entrada</div>
    <table class="datos-table">
        <tr>
            <td class="label">Fecha y Hora</td>
            <td colspan="3">{{ $registro->fecha_hora_entrada ? $registro->fecha_hora_entrada->format('d/m/Y H:i') : 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Estado al Retornar</td>
            <td colspan="3">
                @if($registro->estado_entrada == 'Operativo')<span class="badge-operativo">✓ Operativo</span>
                @elseif($registro->estado_entrada == 'Con Daños')<span class="badge-danios">✗ Con Daños</span>
                @elseif($registro->estado_entrada == 'Incompleto')<span class="badge-incompleto">⚠ Incompleto</span>
                @elseif($registro->estado_entrada == 'No Retornó')<span class="badge-danios">✗ No Retornó</span>
                @else N/A @endif
            </td>
        </tr>
        @if($registro->observaciones_entrada)
        <tr>
            <td class="label">Observaciones</td>
            <td colspan="3">{{ $registro->observaciones_entrada }}</td>
        </tr>
        @endif
    </table>

    <div class="section-title">Registro de Seguridad - Entrada</div>
    <table class="datos-table">
        <tr>
            <td class="label">Nombre del Funcionario</td>
            <td>{{ $registro->seguridad_entrada_nombre ?? 'N/A' }}</td>
            <td class="label">C.I.</td>
            <td>{{ $registro->seguridad_entrada_cedula ?? 'N/A' }}</td>
        </tr>
    </table>
    @endif

    <div class="section-title">Información del Registro</div>
    <table class="datos-table">
        <tr>
            <td class="label">Registrado por</td>
            <td colspan="3">{{ $registro->usuario->name ?? 'N/A' }}</td>
        </tr>
    </table>

    @include('pdf.partials.firmas', [
        'firma1_nombre' => $registro->persona_retira_nombre ?? '',
        'firma1_cargo' => $registro->tipo == 'Salida' ? 'Persona que Retira' : 'Persona que Entrega',
        'firma1_cedula' => $registro->persona_retira_cedula ?? '',
        'firma2_nombre' => $registro->tipo == 'Salida' ? ($registro->seguridad_salida_nombre ?? '') : ($registro->seguridad_entrada_nombre ?? ''),
        'firma2_cargo' => 'Funcionario de Seguridad',
        'firma2_cedula' => $registro->tipo == 'Salida' ? ($registro->seguridad_salida_cedula ?? '') : ($registro->seguridad_entrada_cedula ?? ''),
        'firma3_nombre' => $registro->autorizado_por_nombre ?? '',
        'firma3_cargo' => 'Autorizado por',
        'firma3_cedula' => $registro->autorizado_por_cedula ?? '',
    ])

    <div class="pie">
        Documento generado automáticamente por el Sistema de Gestión de Bienes CONAPDIS<br>
        Comprobante N° CONAPDIS-{{ $registro->tipo }}-{{ str_pad($registro->id, 6, '0', STR_PAD_LEFT) }} | {{ date('d/m/Y H:i:s') }}
    </div>

</body>
</html>