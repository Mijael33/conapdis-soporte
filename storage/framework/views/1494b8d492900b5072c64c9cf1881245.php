
<?php $__env->startSection('title', 'Entrada/Salida'); ?>
<?php $__env->startSection('page-title', 'Registros de Entrada/Salida'); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-bold">Movimientos de Bienes</h5>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?php echo e(route('admin.entrada-salida.exportar-excel')); ?>" class="btn-outline-conapdis btn-sm"><i class="bi bi-file-excel"></i> Exportar Excel</a>
            <a href="<?php echo e(route('admin.entrada-salida.pdf-listado')); ?>" class="btn-outline-conapdis btn-sm"><i class="bi bi-file-pdf"></i> Exportar PDF</a>
            <a href="<?php echo e(route('admin.entrada-salida.create')); ?>" class="btn-conapdis btn-sm"><i class="bi bi-plus-lg"></i> Nuevo</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-2">
                <select name="tipo" class="form-select">
                    <option value="">Todos los tipos</option>
                    <option value="Salida" <?php echo e(request('tipo')=='Salida' ? 'selected' : ''); ?>>Salida</option>
                    <option value="Entrada" <?php echo e(request('tipo')=='Entrada' ? 'selected' : ''); ?>>Entrada</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="bien_tipo" class="form-select">
                    <option value="">Todos los bienes</option>
                    <option value="Tecnologia" <?php echo e(request('bien_tipo')=='Tecnologia' ? 'selected' : ''); ?>>Tecnología</option>
                    <option value="BienNacional" <?php echo e(request('bien_tipo')=='BienNacional' ? 'selected' : ''); ?>>Bien Nacional</option>
                    <option value="Vehiculo" <?php echo e(request('bien_tipo')=='Vehiculo' ? 'selected' : ''); ?>>Vehículo</option>
                    <option value="Sonido" <?php echo e(request('bien_tipo')=='Sonido' ? 'selected' : ''); ?>>Sonido</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-conapdis"><i class="bi bi-search"></i> Filtrar</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr>
                        <th>N°</th><th>Tipo</th><th>Bien</th><th>Descripción</th>
                        <th>Fecha</th><th>Retirado por</th><th>Seguridad</th>
                        <th class="text-center" style="width: 130px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $registros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="fw-semibold"><?php echo e(str_pad($reg->id, 6, '0', STR_PAD_LEFT)); ?></td>
                        <td>
                            <?php if($reg->tipo=='Salida'): ?><span class="badge badge-revision">Salida</span>
                            <?php else: ?><span class="badge badge-disponible">Entrada</span><?php endif; ?>
                        </td>
                        <td><span class="badge badge-instalado"><?php echo e($reg->bien_tipo); ?></span></td>
                        <td><?php echo e(Str::limit($reg->descripcion_bien, 30)); ?></td>
                        <td>
                            <?php if($reg->tipo=='Salida'): ?>
                                <?php echo e($reg->fecha_hora_salida ? $reg->fecha_hora_salida->format('d/m/Y H:i') : 'N/A'); ?>

                            <?php else: ?>
                                <?php echo e($reg->fecha_hora_entrada ? $reg->fecha_hora_entrada->format('d/m/Y H:i') : 'N/A'); ?>

                            <?php endif; ?>
                        </td>
                        <td><?php echo e($reg->persona_retira_nombre ?? 'N/A'); ?></td>
                        <td>
                            <?php if($reg->tipo=='Salida'): ?>
                                <?php echo e($reg->seguridad_salida_nombre ?? 'N/A'); ?>

                            <?php else: ?>
                                <?php echo e($reg->seguridad_entrada_nombre ?? 'N/A'); ?>

                            <?php endif; ?>
                        </td>
                        <td class="text-center" style="white-space: nowrap;">
                            <a href="<?php echo e(route('admin.entrada-salida.show', $reg)); ?>" class="btn btn-sm btn-outline-conapdis me-1" title="Ver"><i class="bi bi-eye"></i></a>
                            <a href="<?php echo e(route('admin.entrada-salida.pdf', $reg)); ?>" class="btn btn-sm btn-outline-conapdis me-1" title="PDF" target="_blank"><i class="bi bi-file-pdf"></i></a>
                            <a href="<?php echo e(route('admin.entrada-salida.edit', $reg)); ?>" class="btn btn-sm btn-outline-conapdis me-1" title="Editar"><i class="bi bi-pencil"></i></a>
                            <form action="<?php echo e(route('admin.entrada-salida.destroy', $reg)); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar el registro N° <?php echo e(str_pad($reg->id, 6, '0', STR_PAD_LEFT)); ?>?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">Sin registros</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo e($registros->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/admin/entrada_salida/index.blade.php ENDPATH**/ ?>