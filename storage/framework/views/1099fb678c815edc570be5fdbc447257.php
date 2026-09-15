
<?php $__env->startSection('title', 'Detalle Bien'); ?>
<?php $__env->startSection('page-title', 'Ficha del Bien'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><?php echo e($bien->codigo_inventario); ?></h5>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('admin.bienes.pdf', $bien)); ?>" class="btn-conapdis btn-sm" target="_blank">
                    <i class="bi bi-file-pdf"></i> PDF
                </a>
                <a href="<?php echo e(route('admin.bienes.pegatina', $bien)); ?>" class="btn-outline-conapdis btn-sm" target="_blank">
                    <i class="bi bi-tag"></i> Pegatina
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table">
                <tr><th class="w-25">Código</th><td class="fw-bold"><?php echo e($bien->codigo_inventario); ?></td></tr>
                <tr><th>Categoría</th><td><span class="badge badge-instalado"><?php echo e($bien->categoria->nombre ?? 'N/A'); ?></span></td></tr>
                <tr><th>Descripción</th><td><?php echo e($bien->descripcion); ?></td></tr>
                <tr><th>Marca/Modelo</th><td><?php echo e($bien->marca ?? 'N/A'); ?> <?php echo e($bien->modelo ?? ''); ?></td></tr>
                <tr><th>Serial</th><td><?php echo e($bien->serial ?? 'N/A'); ?></td></tr>
                <tr><th>Color</th><td><?php echo e($bien->color ?? 'N/A'); ?></td></tr>
                <tr><th>Material</th><td><?php echo e($bien->material ?? 'N/A'); ?></td></tr>
                <tr><th>Sede</th><td><?php echo e($bien->sede ? $bien->sede->nombre_sede . ' (' . $bien->sede->estado->nombre . ')' : 'N/A'); ?></td></tr>
                <tr><th>Estatus</th><td>
                    <?php if($bien->estatus=='Disponible'): ?><span class="badge badge-disponible">Disponible</span>
                    <?php elseif($bien->estatus=='Asignado'): ?><span class="badge badge-instalado">Asignado</span>
                    <?php elseif($bien->estatus=='En Mantenimiento'): ?><span class="badge badge-revision">En Mantenimiento</span>
                    <?php else: ?><span class="badge badge-inoperativo">Desincorporado</span><?php endif; ?>
                </td></tr>
                <tr><th>Usuario Asignado</th><td><?php echo e($bien->usuario_asignado_nombre ?? 'N/A'); ?></td></tr>
                <tr><th>Cédula</th><td><?php echo e($bien->usuario_asignado_cedula ?? 'N/A'); ?></td></tr>
                <tr><th>Cargo</th><td><?php echo e($bien->usuario_asignado_cargo ?? 'N/A'); ?></td></tr>
                <tr><th>Valor Adquisición</th><td><?php echo e($bien->valor_adquisicion ? number_format($bien->valor_adquisicion, 2, ',', '.') . ' Bs.' : 'N/A'); ?></td></tr>
                <tr><th>Fecha Adquisición</th><td><?php echo e($bien->fecha_adquisicion ? $bien->fecha_adquisicion->format('d/m/Y') : 'N/A'); ?></td></tr>
                <tr><th>Observaciones</th><td><?php echo e($bien->observaciones ?? 'N/A'); ?></td></tr>
            </table>
        </div>
    </div>
    <a href="<?php echo e(route('admin.bienes.index')); ?>" class="btn-outline-conapdis mt-3"><i class="bi bi-arrow-left"></i> Volver</a>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/admin/bienes/show.blade.php ENDPATH**/ ?>