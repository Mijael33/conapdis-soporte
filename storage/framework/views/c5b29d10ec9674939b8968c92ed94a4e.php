<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Pegatina - <?php echo e($componente->serial_unico); ?>

    </title>

    
    <style>
        <?php echo file_get_contents(public_path('css/pegatina.css')); ?>

    </style>

</head>

<body>

<?php

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
    | ESTADO DEL COMPONENTE
    |--------------------------------------------------------------------------
    */

    $estatusClass = match (
        $componente->estatus
    ) {

        'Disponible',
        'Instalado' =>
            'operativo',

        'En Revisión' =>
            'mantenimiento',

        default =>
            'inoperativo',

    };

?>


<div class="pegatina">


    

    <div class="marco-externo"></div>

    <div class="marco-medio"></div>

    <div class="marco-interno"></div>


    

    <div class="esquina esquina-tl"></div>

    <div class="esquina esquina-tr"></div>

    <div class="esquina esquina-bl"></div>

    <div class="esquina esquina-br"></div>


    <div class="contenido">


        

        <table class="cabecera">

            <tr>


                

                <td class="logo-col">

                    <?php if($logoBase64): ?>

                        <div class="logo-box">

                            <img
                                src="<?php echo e($logoBase64); ?>"
                                class="logo"
                                alt="CONAPDIS"
                            >

                        </div>

                    <?php endif; ?>

                </td>


                

                <td class="identidad">

                    <div class="nombre">
                        CONAPDIS
                    </div>

                    <div class="institucion">
                        CONSEJO NACIONAL PARA LAS PERSONAS CON DISCAPACIDAD
                    </div>

                    <div class="rif">
                        RIF: <?php echo e(config('institucional.rif')); ?>

                    </div>

                </td>


                

                <td class="estado-col">

                    <div class="estado <?php echo e($estatusClass); ?>">

                        <?php echo e($componente->estatus); ?>


                    </div>

                </td>

            </tr>

        </table>


        

        <div class="ornamento">

            <span class="ornamento-linea"></span>

            ◆

            <span class="ornamento-linea"></span>

        </div>


        

        <div class="titulo">

            COMPONENTE

            <div class="titulo-sub">
                IDENTIFICACIÓN INSTITUCIONAL
            </div>

        </div>


        

        <div class="codigo-area">

            <div class="codigo-titulo">
                SERIAL ÚNICO
            </div>

            <div class="codigo-box">

                <div class="codigo">

                    <?php echo e($componente->serial_unico); ?>


                </div>

            </div>

        </div>


        

        <table class="tabla-datos">


            

            <tr>

                

                <td>

                    <div class="campo">

                        <div class="campo-label">
                            CATEGORÍA
                        </div>

                        <div class="campo-valor">

                            <?php echo e($componente->categoria->nombre ?? 'N/A'); ?>


                        </div>

                    </div>

                </td>


                

                <td>

                    <div class="campo">

                        <div class="campo-label">
                            MARCA / MODELO
                        </div>

                        <div class="campo-valor">

                            <?php echo e($componente->marca); ?>

                            <?php echo e($componente->modelo); ?>


                        </div>

                    </div>

                </td>

            </tr>


            

            <tr>

                <td colspan="2">

                    <div class="campo">

                        <div class="campo-label">
                            SEDE / UBICACIÓN
                        </div>

                        <div class="campo-valor sede">

                            <?php echo e($componente->sede->nombre_sede ?? 'N/A'); ?>


                        </div>

                    </div>

                </td>

            </tr>


        </table>


    </div>


    

    <div class="footer">

        <?php echo e(config('institucional.rif')); ?>


        &nbsp;&nbsp;·&nbsp;&nbsp;

        SISTEMA DE GESTIÓN DE BIENES CONAPDIS

    </div>


</div>


</body>

</html><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/admin/componentes/pdf_pegatina.blade.php ENDPATH**/ ?>