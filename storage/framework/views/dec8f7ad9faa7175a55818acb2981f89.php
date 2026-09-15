<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha del Componente - <?php echo e($componente->serial_unico); ?></title>
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

        .badge-disponible { color: #166534; font-weight: 700; }
        .badge-instalado { color: #1e40af; font-weight: 700; }
        .badge-revision { color: #a16207; font-weight: 700; }
        .badge-inoperativo { color: #ef172f; font-weight: 700; }

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

    <?php echo $__env->make('pdf.partials.encabezado', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="titulo-doc">
        Ficha Técnica de Componente
    </div>

    <div class="section-title">Datos del Componente</div>
    <table class="datos-table">
        <tr>
            <td class="label">Serial Único</td>
            <td><strong><?php echo e($componente->serial_unico); ?></strong></td>
            <td class="label">Categoría</td>
            <td><?php echo e($componente->categoria->nombre ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <td class="label">Marca</td>
            <td><?php echo e($componente->marca); ?></td>
            <td class="label">Modelo</td>
            <td><?php echo e($componente->modelo); ?></td>
        </tr>
        <tr>
            <td class="label">Estado</td>
            <td><?php echo e($componente->sede->estado->nombre ?? 'N/A'); ?></td>
            <td class="label">Sede</td>
            <td><?php echo e($componente->sede->nombre_sede ?? 'N/A'); ?></td>
        </tr>
        <tr>
            <td class="label">Estatus</td>
            <td colspan="3">
                <?php if($componente->estatus == 'Disponible'): ?><span class="badge-disponible">✓ Disponible</span>
                <?php elseif($componente->estatus == 'Instalado'): ?><span class="badge-instalado">⚙ Instalado</span>
                <?php elseif($componente->estatus == 'En Revisión'): ?><span class="badge-revision">⚠ En Revisión</span>
                <?php else: ?><span class="badge-inoperativo">✗ <?php echo e($componente->estatus); ?></span><?php endif; ?>
            </td>
        </tr>
    </table>

    <?php if($componente->caracteristicas_tecnicas && is_array($componente->caracteristicas_tecnicas)): ?>
    <div class="section-title">Características Técnicas</div>
    <table class="datos-table">
        <?php $__currentLoopData = $componente->caracteristicas_tecnicas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clave => $valor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="label" style="width: 30%;"><?php echo e(ucfirst($clave)); ?></td>
            <td colspan="3"><?php echo e($valor); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
    <?php endif; ?>

    <?php if($componente->estatus == 'Instalado' && $componente->equipoActual->isNotEmpty()): ?>
    <div class="section-title">Instalado En</div>
    <table class="datos-table">
        <?php $__currentLoopData = $componente->equipoActual; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="label">Código Equipo</td>
            <td><?php echo e($equipo->codigo_inventario_institucional); ?></td>
            <td class="label">Fecha Instalación</td>
            <td><?php echo e($equipo->pivot->fecha_instalacion ? \Carbon\Carbon::parse($equipo->pivot->fecha_instalacion)->format('d/m/Y') : 'N/A'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
    <?php endif; ?>

    <?php if($componente->observaciones): ?>
    <div class="section-title">Observaciones</div>
    <table class="datos-table">
        <tr><td colspan="4"><?php echo e($componente->observaciones); ?></td></tr>
    </table>
    <?php endif; ?>

    <?php echo $__env->make('pdf.partials.firmas', [
        'firma1_nombre' => '',
        'firma1_cargo' => 'Técnico Evaluador',
        'firma1_cedula' => '',
        'firma2_nombre' => '',
        'firma2_cargo' => 'Funcionario Receptor',
        'firma2_cedula' => '',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="pie">
        Documento generado automáticamente por el Sistema de Gestión de Bienes CONAPDIS<br>
        Fecha de emisión: <?php echo e(date('d/m/Y H:i:s')); ?>

    </div>

</body>
</html><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/admin/componentes/pdf_individual.blade.php ENDPATH**/ ?>