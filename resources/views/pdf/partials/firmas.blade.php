{{--
|--------------------------------------------------------------------------
| BLOQUE DE FIRMAS HOLÓGRAFAS DINÁMICO
|--------------------------------------------------------------------------
| Detecta cuántas firmas hay y ajusta el ancho automáticamente.
| - 1 firma → ocupa el centro (33% de ancho centrado)
| - 2 firmas → 50% cada una
| - 3 firmas → 33% cada una
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

    // Si hay menos de 3 firmas, calculamos el margen para centrar
    if ($totalFirmas === 1) {
        $paddingIzq = 33;
    } elseif ($totalFirmas === 2) {
        $paddingIzq = 0;
    } else {
        $paddingIzq = 0;
    }
@endphp

@if($totalFirmas > 0)
<div style="page-break-inside: avoid; margin-top: 50px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            {{-- Padding izquierdo para centrar cuando hay 1 firma --}}
            @if($totalFirmas === 1)
                <td style="width: 33%;"></td>
            @endif

            @foreach($firmas as $firma)
            <td style="width: {{ $anchoColumna }}%; text-align: center; padding: 0 15px; vertical-align: bottom;">
                <div style="height: 70px;"></div>
                <div style="border-top: 1px solid #1e293b; padding-top: 6px;">
                    @if(!empty($firma['nombre']))
                        <div style="font-weight: 600; font-size: 9pt; color: #1e293b;">{{ $firma['nombre'] }}</div>
                    @else
                        <div style="font-weight: 600; font-size: 9pt; color: #94a3b8;">_____________________</div>
                    @endif
                    <div style="font-size: 8pt; color: #64748b; text-transform: uppercase; margin-top: 2px;">
                        {{ $firma['cargo'] }}
                    </div>
                    @if(!empty($firma['cedula']))
                        <div style="font-size: 8pt; color: #1e293b; margin-top: 2px;">C.I.: {{ $firma['cedula'] }}</div>
                    @endif
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