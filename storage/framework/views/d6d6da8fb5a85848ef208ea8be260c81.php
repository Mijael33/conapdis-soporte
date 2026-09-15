
<?php $__env->startSection('title', 'Bienes Nacionales'); ?>
<?php $__env->startSection('page-title', 'Bienes Nacionales'); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-bold">Inventario de Bienes</h5>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?php echo e(route('admin.bienes.importar')); ?>" class="btn-outline-conapdis btn-sm"><i class="bi bi-upload"></i> Importar Excel</a>
            <a href="<?php echo e(route('admin.bienes.exportar-excel')); ?>" class="btn-outline-conapdis btn-sm"><i class="bi bi-file-excel"></i> Exportar Excel</a>
            <a href="<?php echo e(route('admin.bienes.exportar-pdf')); ?>" class="btn-outline-conapdis btn-sm"><i class="bi bi-file-pdf"></i> Exportar PDF</a>
            <a href="<?php echo e(route('admin.bienes.create')); ?>" class="btn-conapdis btn-sm"><i class="bi bi-plus-lg"></i> Nuevo</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="categoria_id" class="form-select">
                    <option value="">Todas las categorías</option>
                    <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e(request('categoria_id')==$cat->id ? 'selected' : ''); ?>><?php echo e($cat->nombre); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="estatus" class="form-select">
                    <option value="">Todos los estatus</option>
                    <option value="Disponible" <?php echo e(request('estatus')=='Disponible' ? 'selected' : ''); ?>>Disponible</option>
                    <option value="Asignado" <?php echo e(request('estatus')=='Asignado' ? 'selected' : ''); ?>>Asignado</option>
                    <option value="En Mantenimiento" <?php echo e(request('estatus')=='En Mantenimiento' ? 'selected' : ''); ?>>En Mantenimiento</option>
                    <option value="Desincorporado" <?php echo e(request('estatus')=='Desincorporado' ? 'selected' : ''); ?>>Desincorporado</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Buscar código, descripción..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-conapdis"><i class="bi bi-search"></i> Filtrar</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr>
                        <th>Código</th><th>Categoría</th><th>Descripción</th>
                        <th>Estado</th><th>Sede</th><th>Estatus</th>
                        <th class="text-center" style="width: 130px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $bienes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="fw-semibold"><?php echo e($bien->codigo_inventario); ?></td>
                        <td><span class="badge badge-instalado"><?php echo e($bien->categoria->nombre ?? 'N/A'); ?></span></td>
                        <td><?php echo e(Str::limit($bien->descripcion, 40)); ?></td>
                        <td><?php echo e($bien->sede ? $bien->sede->estado->nombre : 'N/A'); ?></td>
                        <td><?php echo e($bien->sede ? $bien->sede->nombre_sede : 'N/A'); ?></td>
                        <td>
                            <?php if($bien->estatus=='Disponible'): ?><span class="badge badge-disponible">Disponible</span>
                            <?php elseif($bien->estatus=='Asignado'): ?><span class="badge badge-instalado">Asignado</span>
                            <?php elseif($bien->estatus=='En Mantenimiento'): ?><span class="badge badge-revision">En Mant.</span>
                            <?php else: ?><span class="badge badge-inoperativo">Desinc.</span><?php endif; ?>
                        </td>
                        <td class="text-center" style="white-space: nowrap;">
                            <a href="<?php echo e(route('admin.bienes.show', $bien)); ?>" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-eye"></i></a>
                            <a href="<?php echo e(route('admin.bienes.edit', $bien)); ?>" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-pencil"></i></a>
                            <form action="<?php echo e(route('admin.bienes.destroy', $bien)); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar el bien <?php echo e($bien->codigo_inventario); ?>?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay bienes registrados</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo e($bienes->links()); ?>

    </div>
</div>

<?php if (isset($component)) { $__componentOriginal9035454e7931fffd0ba2a867f6911c43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9035454e7931fffd0ba2a867f6911c43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal-resultado-importacion','data' => ['rutaDescarga' => 'admin.bienes.descargar-errores']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal-resultado-importacion'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['rutaDescarga' => 'admin.bienes.descargar-errores']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9035454e7931fffd0ba2a867f6911c43)): ?>
<?php $attributes = $__attributesOriginal9035454e7931fffd0ba2a867f6911c43; ?>
<?php unset($__attributesOriginal9035454e7931fffd0ba2a867f6911c43); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9035454e7931fffd0ba2a867f6911c43)): ?>
<?php $component = $__componentOriginal9035454e7931fffd0ba2a867f6911c43; ?>
<?php unset($__componentOriginal9035454e7931fffd0ba2a867f6911c43); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/admin/bienes/index.blade.php ENDPATH**/ ?>