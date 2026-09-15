
<?php $__env->startSection('title', 'Categorías de Vehículos'); ?>
<?php $__env->startSection('page-title', 'Categorías de Vehículos'); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-bold">Catálogo de Categorías</h5>
        <a href="<?php echo e(route('admin.vehiculos-categorias.create')); ?>" class="btn-conapdis btn-sm"><i class="bi bi-plus-lg"></i> Nueva Categoría</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($cat->id); ?></td>
                        <td class="fw-semibold"><i class="bi bi-truck me-2"></i><?php echo e($cat->nombre); ?></td>
                        <td><?php echo e($cat->descripcion ?? 'Sin descripción'); ?></td>
                        <td>
                            <a href="<?php echo e(route('admin.vehiculos-categorias.edit', $cat)); ?>" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-pencil"></i></a>
                            <form action="<?php echo e(route('admin.vehiculos-categorias.destroy', $cat)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">Sin categorías</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo e($categorias->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/admin/vehiculos_categorias/index.blade.php ENDPATH**/ ?>