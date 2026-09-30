{{--
|--------------------------------------------------------------------------
| BLOQUE DE FIRMAS HOLÓGRAFAS DINÁMICO
|--------------------------------------------------------------------------
| Detecta cuántas firmas hay y ajusta el ancho automáticamente.
| Reserva el mismo espacio para la cédula aunque no exista, para
| que todas las firmas queden perfectamente alineadas.
--}}

@php
    // Detectar cuántas firmas hay (con nombre o cargo)
    $firmas = [];

    if (!empty($firma1_cargo)) {
        $firmas[] = [
            'nombre' => $firma1_nombre ?? '',
            'cargo' => $firma1_cargo,
            'cedula' => $firma1_cedula ?? '',
        ];
    }

    if (!empty($firma2_cargo)) {
        $firmas[] = [
            'nombre' => $firma2_nombre ?? '',
            'cargo' => $firma2_cargo,
            'cedula' => $firma2_cedula ?? '',
        ];
    }

    if (!empty($firma3_cargo)) {
        $firmas[] = [
            'nombre' => $firma3_nombre ?? '',
            'cargo' => $firma3_cargo,
            'cedula' => $firma3_cedula ?? '',
        ];
    }

    $totalFirmas = count($firmas);
    $anchoColumna = $totalFirmas > 0 ? floor(100 / $totalFirmas) : 100;
@endphp

@if($totalFirmas > 0)
<div style="page-break-inside: avoid; margin-top: 50px;">
    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
        <tr>
            {{-- Padding izquierdo para centrar cuando hay 1 firma --}}
            @if($totalFirmas === 1)
                <td style="width: 33%;"></td>
            @endif

            @foreach($firmas as $firma)
            <td style="width: {{ $anchoColumna }}%; text-align: center; padding: 0 10px; vertical-align: top;">
                {{-- Espacio en blanco para la firma --}}
                <div style="height: 60px;"></div>

                {{-- Línea de firma --}}
                <div style="border-top: 1px solid #1e293b; padding-top: 6px; margin-top: 0;">

                    {{-- NOMBRE (siempre ocupa 1 línea) --}}
                    <div style="font-weight: 600; font-size: 8.5pt; color: #1e293b; line-height: 1.2; height: 12px;">
                        @if(!empty($firma['nombre']))
                            {{ $firma['nombre'] }}
                        @else
                            &nbsp;
                        @endif
                    </div>

                    {{-- CARGO (siempre ocupa 1 línea) --}}
                    <div style="font-size: 7pt; color: #64748b; text-transform: uppercase; line-height: 1.2; height: 10px; margin-top: 3px;">
                        {{ $firma['cargo'] }}
                    </div>

                    {{-- CÉDULA (siempre reserva espacio aunque esté vacía) --}}
                    <div style="font-size: 7.5pt; color: #1e293b; line-height: 1.2; height: 10px; margin-top: 3px;">
                        @if(!empty($firma['cedula']))
                            C.I.: {{ $firma['cedula'] }}
                        @else
                            &nbsp;
                        @endif
                    </div>

                </div>
            </td>
            @endforeach

            {{-- Padding derecho para centrar cuando hay 1 firma --}}
            @if($totalFirmas === 1)
                <td style="width: 33%;"></td>
            @endif
        </tr>
    </table>
</div>
@endif