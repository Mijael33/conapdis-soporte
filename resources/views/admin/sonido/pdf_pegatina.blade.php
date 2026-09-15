<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Pegatina - {{ $equipo->serial }}
    </title>

    {{-- CSS específico de las pegatinas --}}
    <style>
        {!! file_get_contents(public_path('css/pegatina.css')) !!}
    </style>

</head>

<body>

@php

    /*
    |--------------------------------------------------------------------------
    | LOGO INSTITUCIONAL
    |--------------------------------------------------------------------------
    */

    $logoPath = public_path(
        config('institucional.logo')
    );

    $logoBase64 = null;

    if (file_exists($logoPath)) {

        $extension = strtolower(
            pathinfo(
                $logoPath,
                PATHINFO_EXTENSION
            )
        );

        $mime = $extension === 'svg'
            ? 'image/svg+xml'
            : 'image/' . $extension;

        $logoBase64 =
            'data:' . $mime . ';base64,' .
            base64_encode(
                file_get_contents($logoPath)
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ESTADO DEL EQUIPO DE SONIDO
    |--------------------------------------------------------------------------
    */

    $estatusClass = match (
        $equipo->estatus
    ) {

        'Disponible',
        'Asignado' =>
            'operativo',

        'En Mantenimiento' =>
            'mantenimiento',

        default =>
            'inoperativo',

    };

@endphp


<div class="pegatina">


    {{-- =====================================================
         MARCOS DECORATIVOS
    ====================================================== --}}

    <div class="marco-externo"></div>

    <div class="marco-medio"></div>

    <div class="marco-interno"></div>


    {{-- =====================================================
         ESQUINAS DECORATIVAS
    ====================================================== --}}

    <div class="esquina esquina-tl"></div>

    <div class="esquina esquina-tr"></div>

    <div class="esquina esquina-bl"></div>

    <div class="esquina esquina-br"></div>


    <div class="contenido">


        {{-- =================================================
             CABECERA INSTITUCIONAL
        ================================================== --}}

        <table class="cabecera">

            <tr>


                {{-- -----------------------------------------
                     LOGO
                ------------------------------------------ --}}

                <td class="logo-col">

                    @if($logoBase64)

                        <div class="logo-box">

                            <img
                                src="{{ $logoBase64 }}"
                                class="logo"
                                alt="CONAPDIS"
                            >

                        </div>

                    @endif

                </td>


                {{-- -----------------------------------------
                     IDENTIDAD INSTITUCIONAL
                ------------------------------------------ --}}

                <td class="identidad">

                    <div class="nombre">
                        CONAPDIS
                    </div>

                    <div class="institucion">
                        CONSEJO NACIONAL PARA LAS PERSONAS CON DISCAPACIDAD
                    </div>

                    <div class="rif">
                        RIF: {{ config('institucional.rif') }}
                    </div>

                </td>


                {{-- -----------------------------------------
                     ESTADO DEL EQUIPO
                ------------------------------------------ --}}

                <td class="estado-col">

                    <div class="estado {{ $estatusClass }}">

                        {{ $equipo->estatus }}

                    </div>

                </td>

            </tr>

        </table>


        {{-- =================================================
             ORNAMENTO SUPERIOR
        ================================================== --}}

        <div class="ornamento">

            <span class="ornamento-linea"></span>

            ◆

            <span class="ornamento-linea"></span>

        </div>


        {{-- =================================================
             TÍTULO PRINCIPAL
        ================================================== --}}

        <div class="titulo">

            EQUIPO DE SONIDO

            <div class="titulo-sub">
                IDENTIFICACIÓN INSTITUCIONAL
            </div>

        </div>


        {{-- =================================================
             SERIAL (destacado como código principal)
        ================================================== --}}

        <div class="codigo-area">

            <div class="codigo-titulo">
                SERIAL
            </div>

            <div class="codigo-box">

                <div class="codigo">

                    {{ $equipo->serial }}

                </div>

            </div>

        </div>


        {{-- =================================================
             TABLA DE DATOS
        ================================================== --}}

        <table class="tabla-datos">


            {{-- ---------------------------------------------
                 FILA 1
            ---------------------------------------------- --}}

            <tr>

                {{-- CATEGORÍA --}}

                <td>

                    <div class="campo">

                        <div class="campo-label">
                            CATEGORÍA
                        </div>

                        <div class="campo-valor">

                            {{ $equipo->categoria->nombre ?? 'N/A' }}

                        </div>

                    </div>

                </td>


                {{-- MARCA / MODELO --}}

                <td>

                    <div class="campo">

                        <div class="campo-label">
                            MARCA / MODELO
                        </div>

                        <div class="campo-valor">

                            {{ $equipo->marca }}
                            {{ $equipo->modelo }}

                        </div>

                    </div>

                </td>

            </tr>


            {{-- ---------------------------------------------
                 FILA 2 (SEDE a ancho completo)
            ---------------------------------------------- --}}

            <tr>

                <td colspan="2">

                    <div class="campo">

                        <div class="campo-label">
                            SEDE / UBICACIÓN
                        </div>

                        <div class="campo-valor sede">

                            {{ $equipo->sede->nombre_sede ?? 'N/A' }}

                        </div>

                    </div>

                </td>

            </tr>


        </table>


    </div>


    {{-- =====================================================
         PIE INSTITUCIONAL
    ====================================================== --}}

    <div class="footer">

        {{ config('institucional.rif') }}

        &nbsp;&nbsp;·&nbsp;&nbsp;

        SISTEMA DE GESTIÓN DE BIENES CONAPDIS

    </div>


</div>


</body>

</html>