

<?php
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
?>

<?php if($totalFirmas > 0): ?>
<div style="page-break-inside: avoid; margin-top: 50px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            
            <?php if($totalFirmas === 1): ?>
                <td style="width: 33%;"></td>
            <?php endif; ?>

            <?php $__currentLoopData = $firmas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $firma): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <td style="width: <?php echo e($anchoColumna); ?>%; text-align: center; padding: 0 15px; vertical-align: bottom;">
                <div style="height: 70px;"></div>
                <div style="border-top: 1px solid #1e293b; padding-top: 6px;">
                    <?php if(!empty($firma['nombre'])): ?>
                        <div style="font-weight: 600; font-size: 9pt; color: #1e293b;"><?php echo e($firma['nombre']); ?></div>
                    <?php else: ?>
                        <div style="font-weight: 600; font-size: 9pt; color: #94a3b8;">_____________________</div>
                    <?php endif; ?>
                    <div style="font-size: 8pt; color: #64748b; text-transform: uppercase; margin-top: 2px;">
                        <?php echo e($firma['cargo']); ?>

                    </div>
                    <?php if(!empty($firma['cedula'])): ?>
                        <div style="font-size: 8pt; color: #1e293b; margin-top: 2px;">C.I.: <?php echo e($firma['cedula']); ?></div>
                    <?php endif; ?>
                </div>
            </td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($totalFirmas === 1): ?>
                <td style="width: 33%;"></td>
            <?php endif; ?>
        </tr>
    </table>
</div>
<?php endif; ?><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/pdf/partials/firmas.blade.php ENDPATH**/ ?>