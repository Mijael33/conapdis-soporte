<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Panel de Control'); ?>

<?php $__env->startSection('content'); ?>


<?php if(auth()->user()->hasRole('Administrador') || auth()->user()->hasRole('Auditor')): ?>
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3" style="color: #1a3b5d;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1a3b5d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -4px; margin-right: 0.5rem;">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
            </svg>
            Filtro Global - Se aplica a todo el sistema
        </h6>
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Estado</label>
                <select name="estado_id" id="estadoSelect" class="form-select rounded-3" onchange="cargarSedes()">
                    <option value="">Todos los estados</option>
                    <?php $__currentLoopData = $estados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($estado->id); ?>" <?php echo e(session('filtro_estado_id') == $estado->id ? 'selected' : ''); ?>><?php echo e($estado->nombre); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Sede</label>
                <select name="sede_id" id="sedeSelect" class="form-select rounded-3">
                    <option value="">Todas las sedes</option>
                    <?php $__currentLoopData = $sedes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sede): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($sede->id); ?>" <?php echo e(session('filtro_sede_id') == $sede->id ? 'selected' : ''); ?>><?php echo e($sede->nombre_sede); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn-conapdis w-100"><i class="bi bi-funnel"></i> Aplicar</button>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <a href="?limpiar_filtro=1" class="btn-outline-conapdis w-100"><i class="bi bi-x-circle"></i> Limpiar</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>


<h2 class="mb-4" style="color: #1a3b5d; font-weight: 700;">Panel de Control</h2>


<div class="row g-3 mb-4">
    <?php
    $resumenGeneral = [
        ['label' => 'Total Bienes', 'total' => $totalEquipos + $totalComponentes + $totalBienes + $totalVehiculos + $totalSonido, 'color' => '#003097', 'bg' => '#dbeafe', 'icon' => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line>'],
        ['label' => 'Operativos', 'total' => $equiposOperativos + $componentesInstalados + $bienesDisponibles + $vehiculosDisponibles + $sonidoDisponible, 'color' => '#16a34a', 'bg' => '#dcfce7', 'icon' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>'],
        ['label' => 'En Mantenimiento', 'total' => $equiposMantenimiento + $componentesRevision, 'color' => '#d97706', 'bg' => '#fef3c7', 'icon' => '<circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>'],
        ['label' => 'Órdenes Abiertas', 'total' => $ordenesAbiertas, 'color' => '#dc2626', 'bg' => '#fee2e2', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line>'],
        ['label' => 'Movimientos Totales', 'total' => $totalSalidas + $totalEntradas, 'color' => '#7c3aed', 'bg' => '#ede9fe', 'icon' => '<polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path>'],
        ['label' => 'Usuarios del Sistema', 'total' => $totalUsuarios, 'color' => '#4f46e5', 'bg' => '#e0e7ff', 'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>'],
    ];
    ?>

    <?php $__currentLoopData = $resumenGeneral; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="card border-0 shadow-sm rounded-4 p-3" style="border-left: 4px solid <?php echo e($stat['color']); ?>;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: <?php echo e($stat['bg']); ?>;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="<?php echo e($stat['color']); ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?php echo $stat['icon']; ?></svg>
                </div>
                <div>
                    <h4 style="font-weight: 700; color: #1f2937; margin: 0; font-size: 1.2rem;"><?php echo e($stat['total']); ?></h4>
                    <small style="color: #6b7280; font-size: 0.7rem;"><?php echo e($stat['label']); ?></small>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: #dbeafe;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect>
                    <rect x="9" y="9" width="6" height="6"></rect>
                </svg>
            </div>
            <div>
                <h5 class="mb-0 fw-bold" style="color: #1a3b5d;">Tecnología</h4>
                <small class="text-muted">Equipos, Componentes y Órdenes de Servicio</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card operativo">
                    <div class="kpi-card-title">Equipos Totales</div>
                    <div class="kpi-card-value"><?php echo e($totalEquipos); ?></div>
                    <small class="text-muted"><?php echo e($equiposOperativos); ?> operativos</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card mantenimiento">
                    <div class="kpi-card-title">Equipos en Mantenimiento</div>
                    <div class="kpi-card-value"><?php echo e($equiposMantenimiento); ?></div>
                    <small class="text-muted"><?php echo e($equiposInoperativos); ?> inoperativos</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card disponible">
                    <div class="kpi-card-title">Componentes Totales</div>
                    <div class="kpi-card-value"><?php echo e($totalComponentes); ?></div>
                    <small class="text-muted"><?php echo e($componentesDisponibles); ?> disponibles</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card" style="border-top-color: #d97706;">
                    <div class="kpi-card-title">Órdenes de Servicio</div>
                    <div class="kpi-card-value"><?php echo e($ordenesAbiertas + $ordenesReparadas + $ordenesEsperaRepuesto); ?></div>
                    <small class="text-muted"><?php echo e($ordenesAbiertas); ?> abiertas · <?php echo e($ordenesReparadas); ?> reparadas</small>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: #fce7f3;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#db2777" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                </svg>
            </div>
            <div>
                <h5 class="mb-0 fw-bold" style="color: #1a3b5d;">Bienes Nacionales</h4>
                <small class="text-muted">Escritorios, Sillas, Mesas y Otros</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card disponible">
                    <div class="kpi-card-title">Bienes Totales</div>
                    <div class="kpi-card-value"><?php echo e($totalBienes); ?></div>
                    <small class="text-muted">registrados en el sistema</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card operativo">
                    <div class="kpi-card-title">Bienes Disponibles</div>
                    <div class="kpi-card-value"><?php echo e($bienesDisponibles); ?></div>
                    <small class="text-muted">listos para asignar</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-card-title">Asignados</div>
                    <div class="kpi-card-value"><?php echo e($totalBienes - $bienesDisponibles); ?></div>
                    <small class="text-muted">en uso actualmente</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card" style="border-top-color: #003097;">
                    <div class="kpi-card-title">Categorías</div>
                    <div class="kpi-card-value"><?php echo e(\App\Models\CategoriaBien::count()); ?></div>
                    <small class="text-muted">tipos de bienes</small>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: #ede9fe;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"></path>
                    <circle cx="7" cy="17" r="2"></circle>
                    <path d="M9 17h6"></path>
                    <circle cx="17" cy="17" r="2"></circle>
                </svg>
            </div>
            <div>
                <h5 class="mb-0 fw-bold" style="color: #1a3b5d;">Vehículos</h4>
                <small class="text-muted">Flota Vehicular Institucional</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card" style="border-top-color: #7c3aed;">
                    <div class="kpi-card-title">Vehículos Totales</div>
                    <div class="kpi-card-value"><?php echo e($totalVehiculos); ?></div>
                    <small class="text-muted">en la flota</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card operativo">
                    <div class="kpi-card-title">Disponibles</div>
                    <div class="kpi-card-value"><?php echo e($vehiculosDisponibles); ?></div>
                    <small class="text-muted">listos para usar</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-card-title">Asignados</div>
                    <div class="kpi-card-value"><?php echo e($totalVehiculos - $vehiculosDisponibles); ?></div>
                    <small class="text-muted">en operación</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card" style="border-top-color: #003097;">
                    <div class="kpi-card-title">Categorías</div>
                    <div class="kpi-card-value"><?php echo e(\App\Models\CategoriaVehiculo::count()); ?></div>
                    <small class="text-muted">tipos de vehículos</small>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: #cffafe;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0891b2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                    <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                </svg>
            </div>
            <div>
                <h5 class="mb-0 fw-bold" style="color: #1a3b5d;">Equipos de Sonido</h4>
                <small class="text-muted">Parlantes, Micrófonos, Consolas y Amplificadores</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card" style="border-top-color: #0891b2;">
                    <div class="kpi-card-title">Equipos Totales</div>
                    <div class="kpi-card-value"><?php echo e($totalSonido); ?></div>
                    <small class="text-muted">en el inventario</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card operativo">
                    <div class="kpi-card-title">Disponibles</div>
                    <div class="kpi-card-value"><?php echo e($sonidoDisponible); ?></div>
                    <small class="text-muted">listos para asignar</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-card-title">Asignados</div>
                    <div class="kpi-card-value"><?php echo e($totalSonido - $sonidoDisponible); ?></div>
                    <small class="text-muted">en uso actualmente</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card" style="border-top-color: #003097;">
                    <div class="kpi-card-title">Categorías</div>
                    <div class="kpi-card-value"><?php echo e(\App\Models\CategoriaSonido::count()); ?></div>
                    <small class="text-muted">tipos de equipos</small>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: #fef3c7;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="17 1 21 5 17 9"></polyline>
                    <path d="M3 11V9a4 4 0 0 1 4-4h14"></path>
                    <polyline points="7 23 3 19 7 15"></polyline>
                    <path d="M21 13v2a4 4 0 0 1-4 4H3"></path>
                </svg>
            </div>
            <div>
                <h5 class="mb-0 fw-bold" style="color: #1a3b5d;">Entrada / Salida</h4>
                <small class="text-muted">Registro de Movimientos de Bienes</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card" style="border-top-color: #f59e0b;">
                    <div class="kpi-card-title">Total Movimientos</div>
                    <div class="kpi-card-value"><?php echo e($totalSalidas + $totalEntradas); ?></div>
                    <small class="text-muted">registrados en el sistema</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card" style="border-top-color: #dc2626;">
                    <div class="kpi-card-title">Salidas</div>
                    <div class="kpi-card-value"><?php echo e($totalSalidas); ?></div>
                    <small class="text-muted">bienes que salieron</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card operativo">
                    <div class="kpi-card-title">Entradas</div>
                    <div class="kpi-card-value"><?php echo e($totalEntradas); ?></div>
                    <small class="text-muted">bienes retornados</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card" style="border-top-color: #003097;">
                    <div class="kpi-card-title">Pendientes de Retorno</div>
                    <div class="kpi-card-value"><?php echo e(max(0, $totalSalidas - $totalEntradas)); ?></div>
                    <small class="text-muted">bienes fuera de sede</small>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: #e0e7ff;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </div>
            <div>
                <h5 class="mb-0 fw-bold" style="color: #1a3b5d;">Estructura y Usuarios</h4>
                <small class="text-muted">Organización Geográfica del Sistema</small>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card" style="border-top-color: #e11d48;">
                    <div class="kpi-card-title">Estados</div>
                    <div class="kpi-card-value"><?php echo e($totalEstados); ?></div>
                    <small class="text-muted">estados en el sistema</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card" style="border-top-color: #ca8a04;">
                    <div class="kpi-card-title">Sedes</div>
                    <div class="kpi-card-value"><?php echo e($totalSedes); ?></div>
                    <small class="text-muted">sedes registradas</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card" style="border-top-color: #4f46e5;">
                    <div class="kpi-card-title">Usuarios</div>
                    <div class="kpi-card-value"><?php echo e($totalUsuarios); ?></div>
                    <small class="text-muted">usuarios activos</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card" style="border-top-color: #003097;">
                    <div class="kpi-card-title">Roles</div>
                    <div class="kpi-card-value"><?php echo e(\Spatie\Permission\Models\Role::count()); ?></div>
                    <small class="text-muted">roles configurados</small>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 style="color: #1a3b5d; font-weight: 700;">
                <i class="bi bi-bar-chart-fill me-2"></i>Resumen por Categoría
            </h5>
            <canvas id="graficoBarras" height="280"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 style="color: #1a3b5d; font-weight: 700;">
                <i class="bi bi-pie-chart-fill me-2"></i>Distribución General
            </h5>
            <canvas id="graficoPastel" height="280"></canvas>
        </div>
    </div>
</div>


<?php if(session('filtro_sede_id') || session('filtro_estado_id')): ?>
<div class="alert alert-info rounded-4 border-0 shadow-sm">
    <i class="bi bi-info-circle"></i>
    Mostrando datos filtrados por:
    <strong><?php echo e(session('filtro_estado_id') ? \App\Models\Estado::find(session('filtro_estado_id'))->nombre : 'Todos los estados'); ?></strong>
    <?php if(session('filtro_sede_id')): ?>
        - <strong><?php echo e(\App\Models\Sede::find(session('filtro_sede_id'))->nombre_sede); ?></strong>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function cargarSedes() {
    const estadoId = document.getElementById('estadoSelect').value;
    const sedeSelect = document.getElementById('sedeSelect');
    sedeSelect.innerHTML = '<option value="">Cargando...</option>';
    if (!estadoId) {
        sedeSelect.innerHTML = '<option value="">Todas las sedes</option>';
        return;
    }
    fetch(`<?php echo e(url('panel')); ?>/api/sedes/${estadoId}`)
        .then(r => r.json())
        .then(sedes => {
            sedeSelect.innerHTML = '<option value="">Todas las sedes</option>';
            sedes.forEach(s => sedeSelect.innerHTML += `<option value="${s.id}">${s.nombre_sede}</option>`);
        });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const statsData = [
    { label: 'Equipos', value: <?php echo e($totalEquipos); ?>, color: '#2563eb' },
    { label: 'Componentes', value: <?php echo e($totalComponentes); ?>, color: '#059669' },
    { label: 'Bienes', value: <?php echo e($totalBienes); ?>, color: '#db2777' },
    { label: 'Vehículos', value: <?php echo e($totalVehiculos); ?>, color: '#7c3aed' },
    { label: 'Sonido', value: <?php echo e($totalSonido); ?>, color: '#0891b2' },
    { label: 'Movimientos', value: <?php echo e($totalSalidas + $totalEntradas); ?>, color: '#f59e0b' },
].sort((a, b) => b.value - a.value);

const ctxBar = document.getElementById('graficoBarras').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: statsData.map(s => s.label),
        datasets: [{
            label: 'Cantidad',
            data: statsData.map(s => s.value),
            backgroundColor: statsData.map(s => s.color),
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});

const ctxPie = document.getElementById('graficoPastel').getContext('2d');
new Chart(ctxPie, {
    type: 'doughnut',
    data: {
        labels: statsData.map(s => s.label),
        datasets: [{
            data: statsData.map(s => s.value),
            backgroundColor: statsData.map(s => s.color),
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { padding: 10, font: { size: 10 } } }
        }
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>