
<?php $__env->startSection('title', 'Vehículos'); ?>
<?php $__env->startSection('page-title', 'Flota Vehicular'); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-bold">Inventario de Vehículos</h5>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?php echo e(route('admin.vehiculos.importar')); ?>" class="btn-outline-conapdis btn-sm"><i class="bi bi-upload"></i> Importar Excel</a>
            <a href="<?php echo e(route('admin.vehiculos.exportar-excel')); ?>" class="btn-outline-conapdis btn-sm"><i class="bi bi-file-excel"></i> Exportar Excel</a>
            <a href="<?php echo e(route('admin.vehiculos.exportar-pdf')); ?>" class="btn-outline-conapdis btn-sm"><i class="bi bi-file-pdf"></i> Exportar PDF</a>
            <a href="<?php echo e(route('admin.vehiculos.create')); ?>" class="btn-conapdis btn-sm"><i class="bi bi-plus-lg"></i> Nuevo</a>
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
                <input type="text" name="search" class="form-control" placeholder="Buscar placa, marca..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-conapdis"><i class="bi bi-search"></i> Filtrar</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr>
                        <th>Código</th><th>Placa</th><th>Categoría</th><th>Marca/Modelo</th>
                        <th>Estado</th><th>Sede</th><th>Estatus</th>
                        <th class="text-center" style="width: 130px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $vehiculos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $veh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="fw-semibold"><?php echo e($veh->codigo_inventario); ?></td>
                        <td><span class="badge badge-instalado"><?php echo e($veh->placa); ?></span></td>
                        <td><?php echo e($veh->categoria->nombre ?? 'N/A'); ?></td>
                        <td><?php echo e($veh->marca); ?> <?php echo e($veh->modelo); ?> (<?php echo e($veh->anio); ?>)</td>
                        <td><?php echo e($veh->sede ? $veh->sede->estado->nombre : 'N/A'); ?></td>
                        <td><?php echo e($veh->sede ? $veh->sede->nombre_sede : 'N/A'); ?></td>
                        <td>
                            <?php if($veh->estatus=='Disponible'): ?><span class="badge badge-disponible">Disponible</span>
                            <?php elseif($veh->estatus=='Asignado'): ?><span class="badge badge-instalado">Asignado</span>
                            <?php elseif($veh->estatus=='En Mantenimiento'): ?><span class="badge badge-revision">En Mant.</span>
                            <?php else: ?><span class="badge badge-inoperativo">Desinc.</span><?php endif; ?>
                        </td>
                        <td class="text-center" style="white-space: nowrap;">
                            <a href="<?php echo e(route('admin.vehiculos.show', $veh)); ?>" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-eye"></i></a>
                            <a href="<?php echo e(route('admin.vehiculos.edit', $veh)); ?>" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-pencil"></i></a>
                            <form action="<?php echo e(route('admin.vehiculos.destroy', $veh)); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar el vehículo <?php echo e($veh->placa); ?>?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No hay vehículos registrados</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo e($vehiculos->links()); ?>

    </div>
</div>

<?php if (isset($component)) { $__componentOriginal9035454e7931fffd0ba2a867f6911c43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9035454e7931fffd0ba2a867f6911c43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal-resultado-importacion','data' => ['rutaDescarga' => 'admin.vehiculos.descargar-errores']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal-resultado-importacion'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['rutaDescarga' => 'admin.vehiculos.descargar-errores']); ?>
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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/admin/vehiculos/index.blade.php ENDPATH**/ ?>