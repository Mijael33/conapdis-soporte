<?php $__env->startSection('title', 'Equipos'); ?>
<?php $__env->startSection('page-title', 'Equipos'); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-bold">Inventario de Equipos</h5>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?php echo e(route('admin.equipos.importar')); ?>" class="btn-outline-conapdis btn-sm"><i class="bi bi-upload"></i> Importar</a>
            <a href="<?php echo e(route('admin.equipos.exportar-excel')); ?>" class="btn-outline-conapdis btn-sm"><i class="bi bi-file-excel"></i> Excel</a>
            <a href="<?php echo e(route('admin.equipos.exportar-pdf')); ?>" class="btn-outline-conapdis btn-sm"><i class="bi bi-file-pdf"></i> PDF</a>
            <a href="<?php echo e(route('admin.equipos.create')); ?>" class="btn-conapdis btn-sm"><i class="bi bi-plus-lg"></i> Nuevo</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-2">
                <select name="estatus" class="form-select">
                    <option value="">Todos los estatus</option>
                    <option value="Operativo" <?php echo e(request('estatus')=='Operativo' ? 'selected' : ''); ?>>Operativo</option>
                    <option value="En Mantenimiento" <?php echo e(request('estatus')=='En Mantenimiento' ? 'selected' : ''); ?>>En Mantenimiento</option>
                    <option value="Inoperativo" <?php echo e(request('estatus')=='Inoperativo' ? 'selected' : ''); ?>>Inoperativo</option>
                    <option value="Donado/Desincorporado" <?php echo e(request('estatus')=='Donado/Desincorporado' ? 'selected' : ''); ?>>Desincorporado</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="departamento_id" class="form-select">
                    <option value="">Todos los departamentos</option>
                    <?php $__currentLoopData = $departamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($depto->id); ?>" <?php echo e(request('departamento_id')==$depto->id ? 'selected' : ''); ?>>
                            <?php echo e($depto->nombre_departamento); ?> (<?php echo e($depto->sede->nombre_sede); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Buscar código, serial, marca..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-conapdis"><i class="bi bi-search"></i> Filtrar</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr>
                        <th>Código</th><th>Tipo</th><th>Marca/Modelo</th>
                        <th>Estado</th><th>Sede</th><th>Departamento</th>
                        <th>Estatus</th><th class="text-center" style="width: 130px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $equipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="fw-semibold"><?php echo e($equipo->codigo_inventario_institucional); ?></td>
                        <td><span class="badge badge-instalado"><?php echo e($equipo->tipoEquipo->nombre); ?></span></td>
                        <td><?php echo e($equipo->marca); ?> <?php echo e($equipo->modelo); ?></td>
                        <td><?php echo e($equipo->departamento->sede->estado->nombre); ?></td>
                        <td><?php echo e($equipo->departamento->sede->nombre_sede); ?></td>
                        <td><?php echo e($equipo->departamento->nombre_departamento); ?></td>
                        <td>
                            <?php if($equipo->estatus_general=='Operativo'): ?><span class="badge badge-operativo">Operativo</span>
                            <?php elseif($equipo->estatus_general=='En Mantenimiento'): ?><span class="badge badge-mantenimiento">En Mant.</span>
                            <?php elseif($equipo->estatus_general=='Inoperativo'): ?><span class="badge badge-inoperativo">Inoperativo</span>
                            <?php else: ?><span class="badge badge-revision">Desinc.</span><?php endif; ?>
                        </td>
                        <td class="text-center" style="white-space: nowrap;">
                            <a href="<?php echo e(route('admin.equipos.show', $equipo)); ?>" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-eye"></i></a>
                            <a href="<?php echo e(route('admin.equipos.edit', $equipo)); ?>" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-pencil"></i></a>
                            <form action="<?php echo e(route('admin.equipos.destroy', $equipo)); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar el equipo <?php echo e($equipo->codigo_inventario_institucional); ?>?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No hay equipos registrados</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo e($equipos->links()); ?>

    </div>
</div>

<?php if (isset($component)) { $__componentOriginal9035454e7931fffd0ba2a867f6911c43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9035454e7931fffd0ba2a867f6911c43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal-resultado-importacion','data' => ['rutaDescarga' => 'admin.equipos.descargar-errores']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal-resultado-importacion'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['rutaDescarga' => 'admin.equipos.descargar-errores']); ?>
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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/admin/equipos/index.blade.php ENDPATH**/ ?>