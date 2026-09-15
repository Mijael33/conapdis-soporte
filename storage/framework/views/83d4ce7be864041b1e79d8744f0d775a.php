<?php $__env->startSection('title', 'Detalle Componente'); ?>
<?php $__env->startSection('page-title', 'Historial del Componente'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><?php echo e($componente->marca); ?> <?php echo e($componente->modelo); ?></h5>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('admin.componentes.pdf', $componente)); ?>" class="btn-conapdis btn-sm" target="_blank">
                        <i class="bi bi-file-pdf"></i> PDF
                    </a>
                    <a href="<?php echo e(route('admin.componentes.pegatina', $componente)); ?>" class="btn-outline-conapdis btn-sm" target="_blank">
                        <i class="bi bi-tag"></i> Pegatina
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr><th class="w-25">Serial Único</th><td class="fw-bold"><?php echo e($componente->serial_unico); ?></td></tr>
                    <tr><th>Categoría</th><td><span class="badge badge-instalado"><?php echo e($componente->categoria->nombre ?? 'N/A'); ?></span></td></tr>
                    <tr><th>Sede</th><td><?php echo e($componente->sede ? $componente->sede->nombre_sede . ' (' . $componente->sede->estado->nombre . ')' : 'Sin sede'); ?></td></tr>
                    <tr><th>Estatus</th><td>
                        <?php if($componente->estatus=='Disponible'): ?><span class="badge badge-disponible">Disponible</span>
                        <?php elseif($componente->estatus=='Instalado'): ?><span class="badge badge-instalado">Instalado</span>
                        <?php elseif($componente->estatus=='En Revisión'): ?><span class="badge badge-revision">En Revisión</span>
                        <?php else: ?><span class="badge badge-inoperativo">Desincorporado</span><?php endif; ?>
                    </td></tr>
                    <tr><th>Observaciones</th><td><?php echo e($componente->observaciones ?? 'N/A'); ?></td></tr>
                    <?php if($componente->estatus == 'Instalado' && $componente->equipoActual->isNotEmpty()): ?>
                    <tr>
                        <th>Instalado en</th>
                        <td>
                            <?php $__currentLoopData = $componente->equipoActual; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('admin.equipos.show', $equipo)); ?>" class="btn btn-sm btn-outline-conapdis">
                                    <i class="bi bi-pc-display"></i> <?php echo e($equipo->codigo_inventario_institucional); ?>

                                </a>
                                <br><small class="text-muted"><?php echo e($equipo->departamento->sede->nombre_sede); ?> | Instalado: <?php echo e($equipo->pivot->fecha_instalacion); ?></small>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>

                <?php if($componente->caracteristicas_tecnicas): ?>
                <h6 class="fw-bold mt-3">Características Técnicas</h6>
                <table class="table table-bordered">
                    <?php $__currentLoopData = $componente->caracteristicas_tecnicas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr><th class="w-25"><?php echo e(ucfirst($key)); ?></th><td><?php echo e($value); ?></td></tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </table>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="card mt-3">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Bitácora de Cambios del Componente</h5>
            </div>
            <div class="card-body">
                <?php $__empty_1 = true; $__currentLoopData = $componente->bitacoras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="border-bottom pb-2 mb-2">
                        <small class="text-muted"><?php echo e($bit->fecha_registro->format('d/m/Y H:i')); ?></small>
                        <span class="badge badge-instalado ms-2"><?php echo e(str_replace('_', ' ', $bit->accion)); ?></span>
                        <strong class="ms-2"><?php echo e($bit->usuario->name ?? 'N/A'); ?></strong>
                        <p class="mb-0 mt-1"><?php echo e($bit->descripcion_detallada); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted">Sin registros en bitácora</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Historial en Equipos</h5></div>
            <div class="card-body">
                <?php $__empty_1 = true; $__currentLoopData = $componente->equipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="border-bottom pb-2 mb-2">
                        <a href="<?php echo e(route('admin.equipos.show', $equipo)); ?>" class="fw-semibold text-decoration-none">
                            <?php echo e($equipo->codigo_inventario_institucional); ?>

                        </a>
                        <br><small><?php echo e($equipo->departamento->sede->nombre_sede ?? 'N/A'); ?></small>
                        <br><small class="text-muted">Instalado: <?php echo e($equipo->pivot->fecha_instalacion); ?></small>
                        <?php if($equipo->pivot->fecha_desinstalacion): ?>
                            <br><small class="text-danger">Removido: <?php echo e($equipo->pivot->fecha_desinstalacion); ?></small>
                        <?php endif; ?>
                        <?php if($equipo->pivot->activo): ?>
                            <span class="badge badge-operativo ms-2">Activo</span>
                        <?php else: ?>
                            <span class="badge badge-inoperativo ms-2">Inactivo</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted">Sin historial</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<a href="<?php echo e(route('admin.componentes.index')); ?>" class="btn-outline-conapdis mt-3"><i class="bi bi-arrow-left"></i> Volver</a>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/admin/componentes/show.blade.php ENDPATH**/ ?>