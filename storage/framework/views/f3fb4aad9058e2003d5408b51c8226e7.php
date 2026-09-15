
<?php $__env->startSection('title', 'Detalle Vehículo'); ?>
<?php $__env->startSection('page-title', 'Ficha del Vehículo'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><?php echo e($vehiculo->placa); ?></h5>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('admin.vehiculos.pdf', $vehiculo)); ?>" class="btn-conapdis btn-sm" target="_blank">
                    <i class="bi bi-file-pdf"></i> PDF
                </a>
                <a href="<?php echo e(route('admin.vehiculos.pegatina', $vehiculo)); ?>" class="btn-outline-conapdis btn-sm" target="_blank">
                    <i class="bi bi-tag"></i> Pegatina
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table">
                <tr><th class="w-25">Código</th><td><?php echo e($vehiculo->codigo_inventario); ?></td></tr>
                <tr><th>Placa</th><td class="fw-bold"><?php echo e($vehiculo->placa); ?></td></tr>
                <tr><th>Categoría</th><td><?php echo e($vehiculo->categoria->nombre ?? 'N/A'); ?></td></tr>
                <tr><th>Marca/Modelo</th><td><?php echo e($vehiculo->marca); ?> <?php echo e($vehiculo->modelo); ?></td></tr>
                <tr><th>Año</th><td><?php echo e($vehiculo->anio ?? 'N/A'); ?></td></tr>
                <tr><th>Color</th><td><?php echo e($vehiculo->color ?? 'N/A'); ?></td></tr>
                <tr><th>Serial Motor</th><td><?php echo e($vehiculo->serial_motor ?? 'N/A'); ?></td></tr>
                <tr><th>Serial Chasis</th><td><?php echo e($vehiculo->serial_chasis ?? 'N/A'); ?></td></tr>
                <tr><th>Kilometraje</th><td><?php echo e(number_format($vehiculo->kilometraje, 0, ',', '.')); ?> km</td></tr>
                <tr><th>Sede</th><td><?php echo e($vehiculo->sede ? $vehiculo->sede->nombre_sede . ' (' . $vehiculo->sede->estado->nombre . ')' : 'N/A'); ?></td></tr>
                <tr><th>Estatus</th><td>
                    <?php if($vehiculo->estatus=='Disponible'): ?><span class="badge badge-disponible">Disponible</span>
                    <?php elseif($vehiculo->estatus=='Asignado'): ?><span class="badge badge-instalado">Asignado</span>
                    <?php elseif($vehiculo->estatus=='En Mantenimiento'): ?><span class="badge badge-revision">En Mantenimiento</span>
                    <?php else: ?><span class="badge badge-inoperativo">Desincorporado</span><?php endif; ?>
                </td></tr>
                <tr><th>Usuario Asignado</th><td><?php echo e($vehiculo->usuario_asignado_nombre ?? 'N/A'); ?></td></tr>
                <tr><th>Cédula</th><td><?php echo e($vehiculo->usuario_asignado_cedula ?? 'N/A'); ?></td></tr>
                <tr><th>Cargo</th><td><?php echo e($vehiculo->usuario_asignado_cargo ?? 'N/A'); ?></td></tr>
                <tr><th>Observaciones</th><td><?php echo e($vehiculo->observaciones ?? 'N/A'); ?></td></tr>
            </table>
        </div>
    </div>
    <a href="<?php echo e(route('admin.vehiculos.index')); ?>" class="btn-outline-conapdis mt-3"><i class="bi bi-arrow-left"></i> Volver</a>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/admin/vehiculos/show.blade.php ENDPATH**/ ?>