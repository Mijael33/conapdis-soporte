
<?php $__env->startSection('title', 'Importar Equipos de Sonido'); ?>
<?php $__env->startSection('page-title', 'Importar Equipos de Sonido desde Excel'); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-file-excel me-2"></i>Importación Masiva</h5></div>
    <div class="card-body">
        <div class="alert alert-info">
            <h6 class="fw-bold"><i class="bi bi-info-circle me-2"></i>Formato del Excel</h6>
            <p class="mb-1">El archivo debe tener las siguientes columnas en la PRIMERA fila (encabezados):</p>
            <table class="table table-sm table-bordered mt-2">
                <thead class="table-header">
                    <tr><th>Columna</th><th>Descripción</th><th>Obligatorio</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>codigo_inventario</code></td><td>Código de inventario único</td><td>✅</td></tr>
                    <tr><td><code>categoria</code></td><td>Nombre de categoría existente (Parlante, Micrófono, etc.)</td><td>✅</td></tr>
                    <tr><td><code>sede</code></td><td>Nombre de sede existente</td><td>✅</td></tr>
                    <tr><td><code>marca</code></td><td>Marca del equipo</td><td>✅</td></tr>
                    <tr><td><code>modelo</code></td><td>Modelo del equipo</td><td>✅</td></tr>
                    <tr><td><code>serial</code></td><td>Serial único del equipo</td><td>✅</td></tr>
                    <tr><td><code>potencia</code></td><td>Potencia (ej: 2000W PMPO)</td><td>❌</td></tr>
                    <tr><td><code>estatus</code></td><td>Disponible, Asignado, En Mantenimiento, Desincorporado</td><td>❌</td></tr>
                    <tr><td><code>observaciones</code></td><td>Notas adicionales</td><td>❌</td></tr>
                </tbody>
            </table>
            <div class="mt-2">
                <a href="<?php echo e(route('admin.sonido.plantilla')); ?>" class="btn btn-sm btn-outline-conapdis">
                    <i class="bi bi-download"></i> Descargar Plantilla de Ejemplo
                </a>
            </div>
        </div>
        <form action="<?php echo e(route('admin.sonido.procesar-importacion')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Archivo Excel</label>
                <input type="file" name="archivo" class="form-control" accept=".xlsx,.xls,.csv" required>
                <?php $__errorArgs = ['archivo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="text-danger"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <button type="submit" class="btn-conapdis"><i class="bi bi-upload"></i> Importar</button>
            <a href="<?php echo e(route('admin.sonido.index')); ?>" class="btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/admin/sonido/importar.blade.php ENDPATH**/ ?>