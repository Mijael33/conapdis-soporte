<?php $__env->startSection('title', 'Tipos de Equipos'); ?>
<?php $__env->startSection('page-title', 'Tipos de Equipos'); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Catálogo de Tipos de Equipos</h5>
        <a href="<?php echo e(route('admin.tipos-equipos.create')); ?>" class="btn-conapdis"><i class="bi bi-plus-lg"></i> Nuevo Tipo</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $tipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($tipo->id); ?></td>
                        <td class="fw-semibold"><i class="bi bi-pc-display me-2"></i><?php echo e($tipo->nombre); ?></td>
                        <td><?php echo e($tipo->descripcion ?? 'Sin descripción'); ?></td>
                        <td>
                            <a href="<?php echo e(route('admin.tipos-equipos.edit', $tipo)); ?>" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-pencil"></i></a>
                            <form action="<?php echo e(route('admin.tipos-equipos.destroy', $tipo)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php echo e($tipos->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/admin/tipos_equipos/index.blade.php ENDPATH**/ ?>