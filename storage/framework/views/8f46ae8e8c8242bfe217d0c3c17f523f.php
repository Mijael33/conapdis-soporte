<?php $__env->startSection('title', 'Categorías de Componentes'); ?>
<?php $__env->startSection('page-title', 'Categorías de Componentes'); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Catálogo de Categorías</h5>
        <a href="<?php echo e(route('admin.categorias-componentes.create')); ?>" class="btn-conapdis"><i class="bi bi-plus-lg"></i> Nueva Categoría</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($cat->id); ?></td>
                        <td class="fw-semibold"><i class="bi bi-cpu me-2"></i><?php echo e($cat->nombre); ?></td>
                        <td><?php echo e($cat->descripcion ?? 'Sin descripción'); ?></td>
                        <td>
                            <a href="<?php echo e(route('admin.categorias-componentes.edit', $cat)); ?>" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-pencil"></i></a>
                            <form action="<?php echo e(route('admin.categorias-componentes.destroy', $cat)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php echo e($categorias->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/admin/categorias_componentes/index.blade.php ENDPATH**/ ?>