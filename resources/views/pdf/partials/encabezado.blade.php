{{--
|--------------------------------------------------------------------------
| ENCABEZADO INSTITUCIONAL PARA PDFs
|--------------------------------------------------------------------------
| Membrete + Logo + Datos institucionales.
| Usa base64 para garantizar compatibilidad con Dompdf.
--}}

@php
    // Cargar imágenes como base64 para garantizar compatibilidad con Dompdf
    $membretePath = public_path(config('institucional.membrete'));
    $logoPath = public_path(config('institucional.logo'));

    $membreteBase64 = null;
    $logoBase64 = null;

    if (file_exists($membretePath)) {
        $membreteData = file_get_contents($membretePath);
        $membreteBase64 = 'data:image/' . pathinfo($membretePath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($membreteData);
    }

    if (file_exists($logoPath)) {
        $logoData = file_get_contents($logoPath);
        $logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($logoData);
    }
@endphp

<div style="width: 100%; margin-bottom: 15px;">

    {{-- MEMBRETE --}}
    @if($membreteBase64)
    <div style="width: 100%; text-align: center; margin-bottom: 10px;">
        <img src="{{ $membreteBase64 }}"
             alt="Membrete CONAPDIS"
             style="width: 100%; height: auto; display: block;">
    </div>
    @endif

    {{-- LOGO + DATOS INSTITUCIONALES --}}
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 90px; vertical-align: middle; padding-right: 12px;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}"
                         alt="Logo CONAPDIS"
                         style="width: 80px; height: auto; display: block;">
                @endif
            </td>
            <td style="vertical-align: middle;">
                <div style="font-size: 11pt; font-weight: 700; color: #003097; margin-bottom: 2px;">
                    {{ config('institucional.nombre') }}
                </div>
                <div style="font-size: 8pt; color: #64748b; margin-bottom: 2px;">
                    {{ config('institucional.descripcion') }}
                </div>
                <div style="font-size: 8pt; color: #64748b;">
                    <strong>RIF:</strong> {{ config('institucional.rif') }}
                </div>
                <div style="font-size: 7pt; color: #64748b; margin-top: 2px;">
                    {{ config('institucional.direccion') }}
                </div>
            </td>
        </tr>
    </table>

    {{-- LÍNEA DIVISORIA --}}
    <div style="border-bottom: 3px solid #003097; margin-top: 10px;"></div>
</div>