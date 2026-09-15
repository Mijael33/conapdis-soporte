

<?php
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
?>

<div style="width: 100%; margin-bottom: 15px;">

    
    <?php if($membreteBase64): ?>
    <div style="width: 100%; text-align: center; margin-bottom: 10px;">
        <img src="<?php echo e($membreteBase64); ?>"
             alt="Membrete CONAPDIS"
             style="width: 100%; height: auto; display: block;">
    </div>
    <?php endif; ?>

    
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 90px; vertical-align: middle; padding-right: 12px;">
                <?php if($logoBase64): ?>
                    <img src="<?php echo e($logoBase64); ?>"
                         alt="Logo CONAPDIS"
                         style="width: 80px; height: auto; display: block;">
                <?php endif; ?>
            </td>
            <td style="vertical-align: middle;">
                <div style="font-size: 11pt; font-weight: 700; color: #003097; margin-bottom: 2px;">
                    <?php echo e(config('institucional.nombre')); ?>

                </div>
                <div style="font-size: 8pt; color: #64748b; margin-bottom: 2px;">
                    <?php echo e(config('institucional.descripcion')); ?>

                </div>
                <div style="font-size: 8pt; color: #64748b;">
                    <strong>RIF:</strong> <?php echo e(config('institucional.rif')); ?>

                </div>
                <div style="font-size: 7pt; color: #64748b; margin-top: 2px;">
                    <?php echo e(config('institucional.direccion')); ?>

                </div>
            </td>
        </tr>
    </table>

    
    <div style="border-bottom: 3px solid #003097; margin-top: 10px;"></div>
</div><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/pdf/partials/encabezado.blade.php ENDPATH**/ ?>