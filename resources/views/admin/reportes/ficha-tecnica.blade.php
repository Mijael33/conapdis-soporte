<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha Técnica - {{ $equipo->codigo_inventario_institucional }}</title>
    <style>
        * { font-family: 'DejaVu Sans', sans-serif; }
        body { font-size: 9pt; color: #1e293b; margin: 0; padding: 15px; }

        /* TABLAS */
        table { width: 100%; border-collapse: collapse; }
        .datos-table { margin-bottom: 12px; }
        .datos-table td { padding: 5px 8px; border: 1px solid #e2e8f0; font-size: 9pt; vertical-align: top; }
        .datos-table td.label { background: #f8fafc; font-weight: 600; color: #001e5c; width: 25%; }

        /* SECCIONES */
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

        /* TABLA DE COMPONENTES */
        .componentes-table th {
            background: #003097;
            color: white;
            padding: 6px 8px;
            font-size: 8pt;
            text-align: left;
            text-transform: uppercase;
        }
        .componentes-table td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
            font-size: 8pt;
        }
        .componentes-table tr:nth-child(even) { background: #f8fafc; }

        /* BADGES */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 50px;
            font-size: 8pt;
            font-weight: 600;
        }
        .badge-operativo { background: #ecfdf5; color: #4c7f36; }
        .badge-mantenimiento { background: #fefce8; color: #a16207; }
        .badge-inoperativo { background: #fef2f2; color: #ef172f; }

        /* PIE */
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

    {{-- TÍTULO DEL DOCUMENTO --}}
    <div style="text-align: center; font-size: 12pt; font-weight: 700; color: #001e5c; text-transform: uppercase; padding: 10px; background: #f4f6f9; border-top: 2px solid #003097; border-bottom: 2px solid #003097; margin: 15px 0;">
        Ficha Técnica de Equipo
    </div>

    {{-- DATOS DEL EQUIPO --}}
    <div class="section-title">Datos del Equipo</div>
    <table class="datos-table">
        <tr>
            <td class="label">Código Inventario</td>
            <td><strong>{{ $equipo->codigo_inventario_institucional }}</strong></td>
            <td class="label">Serial Chasis</td>
            <td>{{ $equipo->serial_chasis ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Tipo</td>
            <td>{{ $equipo->tipoEquipo->nombre }}</td>
            <td class="label">Marca / Modelo</td>
            <td>{{ $equipo->marca }} {{ $equipo->modelo }}</td>
        </tr>
        <tr>
            <td class="label">Estado</td>
            <td>{{ $equipo->departamento->sede->estado->nombre }}</td>
            <td class="label">Sede</td>
            <td>{{ $equipo->departamento->sede->nombre_sede }}</td>
        </tr>
        <tr>
            <td class="label">Departamento</td>
            <td colspan="3">{{ $equipo->departamento->nombre_departamento }}</td>
        </tr>
        <tr>
            <td class="label">Estatus General</td>
            <td colspan="3">
                @if($equipo->estatus_general == 'Operativo')
                    <span class="badge badge-operativo">✓ Operativo</span>
                @elseif($equipo->estatus_general == 'En Mantenimiento')
                    <span class="badge badge-mantenimiento">⚠ En Mantenimiento</span>
                @else
                    <span class="badge badge-inoperativo">✗ {{ $equipo->estatus_general }}</span>
                @endif
            </td>
        </tr>
    </table>

    {{-- USUARIO ASIGNADO --}}
    <div class="section-title">Usuario Asignado</div>
    <table class="datos-table">
        <tr>
            <td class="label">Nombre</td>
            <td>{{ $equipo->usuario_asignado_nombre ?? 'No asignado' }}</td>
            <td class="label">Cédula</td>
            <td>{{ $equipo->usuario_asignado_cedula ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Cargo</td>
            <td colspan="3">{{ $equipo->usuario_asignado_cargo ?? 'N/A' }}</td>
        </tr>
    </table>

    {{-- COMPONENTES INSTALADOS --}}
    <div class="section-title">Componentes Instalados</div>
    @if($equipo->componentes->count() > 0)
    <table class="componentes-table">
        <thead>
            <tr>
                <th>Categoría</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Serial</th>
                <th>Fecha Instalación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equipo->componentes as $comp)
            <tr>
                <td>{{ $comp->categoria->nombre ?? 'N/A' }}</td>
                <td>{{ $comp->marca }}</td>
                <td>{{ $comp->modelo }}</td>
                <td>{{ $comp->serial_unico }}</td>
                <td>{{ $comp->pivot->fecha_instalacion ? \Carbon\Carbon::parse($comp->pivot->fecha_instalacion)->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align: center; color: #94a3b8; font-size: 9pt; padding: 10px;">Sin componentes asignados</p>
    @endif

    {{-- SISTEMAS OPERATIVOS --}}
    @if($equipo->sistemasOperativos->count() > 0)
    <div class="section-title">Sistemas Operativos</div>
    <table class="componentes-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Arquitectura</th>
                <th>Contraseña</th>
                <th>Notas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equipo->sistemasOperativos as $so)
            <tr>
                <td>{{ $so->nombre }}</td>
                <td>{{ $so->arquitectura ?? 'N/A' }}</td>
                <td>{{ $so->tiene_contrasena ? '••••••••' : 'Sin contraseña' }}</td>
                <td>{{ $so->notas ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- HISTORIAL DE REPARACIONES --}}
    @if($equipo->ordenesServicio->count() > 0)
    <div class="section-title">Historial de Reparaciones</div>
    <table class="componentes-table">
        <thead>
            <tr>
                <th>Ticket</th>
                <th>Fecha</th>
                <th>Problema</th>
                <th>Estatus</th>
                <th>Técnico</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equipo->ordenesServicio as $orden)
            <tr>
                <td>{{ $orden->codigo_ticket }}</td>
                <td>{{ $orden->fecha_inicio ? $orden->fecha_inicio->format('d/m/Y') : 'N/A' }}</td>
                <td>{{ Str::limit($orden->problema_reportado_usuario, 40) }}</td>
                <td>{{ $orden->estatus_final ?? 'Abierta' }}</td>
                <td>{{ $orden->tecnico->name ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- FIRMAS --}}
    @include('pdf.partials.firmas', [
        'firma1_nombre' => 'Técnico Evaluador',
        'firma1_cargo' => 'Firma del Técnico',
        'firma1_cedula' => '',
        'firma2_nombre' => 'Funcionario Receptor',
        'firma2_cargo' => 'Firma del Receptor',
        'firma2_cedula' => '',
        'firma3_nombre' => '',
        'firma3_cargo' => '',
    ])

    {{-- PIE DE PÁGINA --}}
    <div class="pie">
        Documento generado automáticamente por el Sistema de Gestión de Bienes CONAPDIS<br>
        Fecha de emisión: {{ date('d/m/Y H:i:s') }}
    </div>

</body>
</html>