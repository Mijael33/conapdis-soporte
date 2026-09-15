<?php $__env->startSection('title', 'Detalle Equipo'); ?>
<?php $__env->startSection('page-title', 'Ficha del Equipo'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold"><?php echo e($equipo->codigo_inventario_institucional); ?></h5>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('admin.equipos.pdf', $equipo)); ?>" class="btn-conapdis btn-sm" target="_blank">
                        <i class="bi bi-file-pdf"></i> PDF
                    </a>
                    <a href="<?php echo e(route('admin.equipos.pegatina', $equipo)); ?>" class="btn-outline-conapdis btn-sm" target="_blank">
                        <i class="bi bi-tag"></i> Pegatina
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr><th class="w-25">Código Inventario</th><td class="fw-bold"><?php echo e($equipo->codigo_inventario_institucional); ?></td></tr>
                    <tr><th>Serial Chasis</th><td><?php echo e($equipo->serial_chasis ?? 'N/A'); ?></td></tr>
                    <tr><th>Tipo</th><td><?php echo e($equipo->tipoEquipo->nombre ?? 'N/A'); ?></td></tr>
                    <tr><th>Marca/Modelo</th><td><?php echo e($equipo->marca); ?> <?php echo e($equipo->modelo); ?></td></tr>
                    <tr><th>Departamento</th><td><?php echo e($equipo->departamento->nombre_departamento ?? 'N/A'); ?> - <?php echo e($equipo->departamento->sede->nombre_sede ?? 'N/A'); ?> (<?php echo e($equipo->departamento->sede->estado->nombre ?? 'N/A'); ?>)</td></tr>
                    <tr><th>Estatus</th><td>
                        <?php if($equipo->estatus_general=='Operativo'): ?><span class="badge badge-operativo">Operativo</span>
                        <?php elseif($equipo->estatus_general=='En Mantenimiento'): ?><span class="badge badge-mantenimiento">En Mantenimiento</span>
                        <?php else: ?><span class="badge badge-inoperativo">Inoperativo</span><?php endif; ?>
                    </td></tr>
                    <tr><th>Usuario Asignado</th><td><?php echo e($equipo->usuario_asignado_nombre ?? 'No asignado'); ?></td></tr>
                    <tr><th>Cédula</th><td><?php echo e($equipo->usuario_asignado_cedula ?? 'N/A'); ?></td></tr>
                    <tr><th>Cargo</th><td><?php echo e($equipo->usuario_asignado_cargo ?? 'N/A'); ?></td></tr>
                </table>
            </div>
        </div>

        
        <div class="card mt-3">
            <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-microsoft me-2"></i>Sistemas Operativos</h5></div>
            <div class="card-body">
                <?php $__empty_1 = true; $__currentLoopData = $equipo->sistemasOperativos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $so): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="border rounded p-3 mb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong><?php echo e($so->nombre); ?></strong>
                                <?php if($so->arquitectura): ?><span class="badge badge-instalado ms-2"><?php echo e($so->arquitectura); ?></span><?php endif; ?>
                                <?php if($so->tiene_contrasena): ?>
                                    <span class="badge badge-revision ms-2"><i class="bi bi-lock"></i> Tiene contraseña</span>
                                <?php else: ?>
                                    <span class="badge badge-disponible ms-2">Sin contraseña</span>
                                <?php endif; ?>
                                <?php if($so->notas): ?><br><small class="text-muted"><?php echo e($so->notas); ?></small><?php endif; ?>
                            </div>
                            <?php if($so->tiene_contrasena): ?>
                                <button class="btn btn-sm btn-outline-conapdis ver-password" data-so-id="<?php echo e($so->id); ?>" title="Ver contraseña">
                                    <i class="bi bi-eye"></i> Ver contraseña
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="password-display mt-2" id="password-<?php echo e($so->id); ?>" style="display:none;">
                            <div class="alert alert-info">
                                <strong><i class="bi bi-key"></i> Contraseña:</strong> <span class="password-text fw-bold"></span>
                                <br><small class="text-muted"><i class="bi bi-shield-check"></i> Este acceso ha sido registrado en la bitácora.</small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted">No hay sistemas operativos registrados</p>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="card mt-3">
            <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-tools me-2"></i>Historial de Reparaciones</h5></div>
            <div class="card-body">
                <?php $__empty_1 = true; $__currentLoopData = $equipo->ordenesServicio; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orden): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="border-bottom pb-2 mb-2">
                        <div class="d-flex justify-content-between">
                            <strong><?php echo e($orden->codigo_ticket); ?></strong>
                            <small class="text-muted"><?php echo e($orden->fecha_inicio ? $orden->fecha_inicio->format('d/m/Y') : 'N/A'); ?></small>
                        </div>
                        <p class="mb-1"><strong>Problema:</strong> <?php echo e(Str::limit($orden->problema_reportado_usuario, 80)); ?></p>
                        <p class="mb-1"><strong>Diagnóstico:</strong> <?php echo e($orden->diagnostico_tecnico ?? 'Pendiente'); ?></p>
                        <span class="badge <?php echo e($orden->estatus_final == 'Reparado' ? 'badge-operativo' : 'badge-revision'); ?>"><?php echo e($orden->estatus_final ?? 'Abierta'); ?></span>
                        <small class="text-muted ms-2">Técnico: <?php echo e($orden->tecnico->name ?? 'N/A'); ?></small>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted">Sin órdenes de servicio</p>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="card mt-3">
            <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Bitácora de Cambios</h5></div>
            <div class="card-body">
                <?php $__empty_1 = true; $__currentLoopData = $equipo->bitacoras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-memory me-2"></i>Componentes</h5>
                <a href="<?php echo e(route('admin.equipos.asignar-componente', $equipo)); ?>" class="btn-conapdis btn-sm"><i class="bi bi-plus-lg"></i> Agregar</a>
            </div>
            <div class="card-body">
                <?php $__empty_1 = true; $__currentLoopData = $equipo->componentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="border rounded p-3 mb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong><?php echo e($comp->marca); ?> <?php echo e($comp->modelo); ?></strong>
                                <br><small class="text-muted"><?php echo e($comp->categoria->nombre ?? 'N/A'); ?></small>
                                <br><small class="text-muted"><i class="bi bi-upc"></i> <?php echo e($comp->serial_unico); ?></small>
                                <br><small class="text-muted"><i class="bi bi-calendar3"></i> Instalado: <?php echo e($comp->pivot->fecha_instalacion); ?></small>
                            </div>
                            <form action="<?php echo e(route('admin.equipos.remover-componente', [$equipo, $comp])); ?>" method="POST" class="ms-2">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                    onclick="return confirm('¿Remover el componente <?php echo e($comp->serial_unico); ?>?\n\nEste componente pasará a estado \"En Revisión\".')"
                                    title="Remover componente">
                                    <i class="bi bi-unlink"></i> Remover
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-cpu" style="font-size: 2rem;"></i>
                        <p class="mt-2">Sin componentes asignados</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<a href="<?php echo e(route('admin.equipos.index')); ?>" class="btn-outline-conapdis mt-3"><i class="bi bi-arrow-left"></i> Volver</a>

<?php $__env->startPush('scripts'); ?>
<script>
document.querySelectorAll('.ver-password').forEach(btn => {
    btn.addEventListener('click', async function() {
        const soId = this.dataset.soId;
        const display = document.getElementById('password-' + soId);
        
        if (display.style.display === 'none' || !display.style.display) {
            try {
                const response = await fetch(`<?php echo e(url('panel')); ?>/equipos/so/${soId}/password`);
                const data = await response.json();
                
                display.querySelector('.password-text').textContent = data.password || 'Sin contraseña';
                display.style.display = 'block';
                this.innerHTML = '<i class="bi bi-eye-slash"></i> Ocultar';
            } catch (err) {
                alert('Error al obtener la contraseña');
            }
        } else {
            display.style.display = 'none';
            this.innerHTML = '<i class="bi bi-eye"></i> Ver contraseña';
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/admin/equipos/show.blade.php ENDPATH**/ ?>