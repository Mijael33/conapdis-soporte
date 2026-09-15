<?php $__env->startSection('title', 'Órdenes de Servicio'); ?>
<?php $__env->startSection('page-title', 'Órdenes de Servicio'); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Tickets de Soporte</h5>
        <a href="<?php echo e(route('admin.ordenes.create')); ?>" class="btn-conapdis"><i class="bi bi-plus-lg"></i> Nueva Orden</a>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="estatus" class="form-select">
                    <option value="">Todos</option>
                    <option value="abiertas" <?php echo e(request('estatus')=='abiertas' ? 'selected' : ''); ?>>Abiertas</option>
                    <option value="Reparado" <?php echo e(request('estatus')=='Reparado' ? 'selected' : ''); ?>>Reparado</option>
                    <option value="En Espera de Repuesto" <?php echo e(request('estatus')=='En Espera de Repuesto' ? 'selected' : ''); ?>>En Espera de Repuesto</option>
                    <option value="Irrecuperable" <?php echo e(request('estatus')=='Irrecuperable' ? 'selected' : ''); ?>>Irrecuperable</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-conapdis"><i class="bi bi-search"></i> Filtrar</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr><th>Ticket</th><th>Equipo</th><th>Técnico</th><th>Problema</th><th>Estatus</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $ordenes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orden): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="fw-semibold"><?php echo e($orden->codigo_ticket); ?></td>
                        <td><?php echo e($orden->equipo->codigo_inventario_institucional); ?></td>
                        <td><?php echo e($orden->tecnico->name); ?></td>
                        <td><?php echo e(Str::limit($orden->problema_reportado_usuario, 40)); ?></td>
                        <td>
                            <?php if(!$orden->estatus_final): ?><span class="badge badge-revision">Abierta</span>
                            <?php elseif($orden->estatus_final=='Reparado'): ?><span class="badge badge-operativo">Reparado</span>
                            <?php elseif($orden->estatus_final=='En Espera de Repuesto'): ?><span class="badge badge-mantenimiento">En Espera</span>
                            <?php else: ?><span class="badge badge-inoperativo"><?php echo e($orden->estatus_final); ?></span><?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo e(route('admin.ordenes.show', $orden)); ?>" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-eye"></i></a>
                            <a href="<?php echo e(route('admin.ordenes.edit', $orden)); ?>" class="btn btn-sm btn-outline-conapdis"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php echo e($ordenes->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/admin/ordenes/index.blade.php ENDPATH**/ ?>