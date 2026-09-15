<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <title><?php echo $__env->yieldContent('title', 'CONAPDIS'); ?> - Sistema de Gestión de Bienes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo e(asset('css/conapdis.css')); ?>" rel="stylesheet">
</head>
<body>
    <aside class="app-sidebar">
        <div class="sidebar-logo">
            <img src="<?php echo e(asset('images/logos/logo-conapdis.png')); ?>" alt="CONAPDIS" style="max-width: 120px; height: auto;">
            <h3>Sistema de Gestión de Bienes</h3>
        </div>
        <nav>
            <div class="nav-section-title">Principal</div>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('tecnologia.equipos.ver')): ?>
            <div class="nav-section-title">Tecnología</div>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('tecnologia.equipos.ver')): ?>
            <a href="<?php echo e(route('admin.equipos.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.equipos.*') ? 'active' : ''); ?>">
                <i class="bi bi-hdd-stack"></i> Equipos
            </a>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('tecnologia.componentes.ver')): ?>
            <a href="<?php echo e(route('admin.componentes.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.componentes.*') ? 'active' : ''); ?>">
                <i class="bi bi-memory"></i> Componentes
            </a>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('tecnologia.tipos-equipos.ver')): ?>
            <a href="<?php echo e(route('admin.tipos-equipos.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.tipos-equipos.*') ? 'active' : ''); ?>">
                <i class="bi bi-pc-display"></i> Tipos de Equipos
            </a>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('tecnologia.categorias-componentes.ver')): ?>
            <a href="<?php echo e(route('admin.categorias-componentes.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.categorias-componentes.*') ? 'active' : ''); ?>">
                <i class="bi bi-cpu"></i> Categorías Componentes
            </a>
            <?php endif; ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('tecnologia.ordenes.ver')): ?>
            <a href="<?php echo e(route('admin.ordenes.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.ordenes.*') ? 'active' : ''); ?>">
                <i class="bi bi-tools"></i> Órdenes de Servicio
            </a>
            <?php endif; ?>

            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('bienes.ver')): ?>
            <div class="nav-section-title">Bienes Nacionales</div>
            <a href="<?php echo e(route('admin.bienes.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.bienes.*') && !request()->routeIs('admin.bienes-categorias.*') ? 'active' : ''); ?>">
                <i class="bi bi-box-seam"></i> Bienes
            </a>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('bienes.categorias.ver')): ?>
            <a href="<?php echo e(route('admin.bienes-categorias.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.bienes-categorias.*') ? 'active' : ''); ?>">
                <i class="bi bi-tags"></i> Categorías
            </a>
            <?php endif; ?>

            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vehiculos.ver')): ?>
            <div class="nav-section-title">Vehículos</div>
            <a href="<?php echo e(route('admin.vehiculos.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.vehiculos.*') && !request()->routeIs('admin.vehiculos-categorias.*') ? 'active' : ''); ?>">
                <i class="bi bi-truck"></i> Vehículos
            </a>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('vehiculos.categorias.ver')): ?>
            <a href="<?php echo e(route('admin.vehiculos-categorias.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.vehiculos-categorias.*') ? 'active' : ''); ?>">
                <i class="bi bi-tags"></i> Categorías
            </a>
            <?php endif; ?>

            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sonido.ver')): ?>
            <div class="nav-section-title">Equipos de Sonido</div>
            <a href="<?php echo e(route('admin.sonido.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.sonido.*') && !request()->routeIs('admin.sonido-categorias.*') ? 'active' : ''); ?>">
                <i class="bi bi-soundwave"></i> Equipos
            </a>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sonido.categorias.ver')): ?>
            <a href="<?php echo e(route('admin.sonido-categorias.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.sonido-categorias.*') ? 'active' : ''); ?>">
                <i class="bi bi-tags"></i> Categorías
            </a>
            <?php endif; ?>

            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('entrada-salida.ver')): ?>
            <div class="nav-section-title">Entrada/Salida</div>
            <a href="<?php echo e(route('admin.entrada-salida.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.entrada-salida.index') ? 'active' : ''); ?>">
                <i class="bi bi-arrow-left-right"></i> Registros
            </a>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('entrada-salida.crear')): ?>
            <a href="<?php echo e(route('admin.entrada-salida.create')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.entrada-salida.create') ? 'active' : ''); ?>">
                <i class="bi bi-plus-circle"></i> Nuevo Registro
            </a>
            <?php endif; ?>

            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('bitacora.ver')): ?>
            <div class="nav-section-title">Auditoría</div>
            <a href="<?php echo e(route('admin.bitacora.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.bitacora.*') ? 'active' : ''); ?>">
                <i class="bi bi-journal-text"></i> Bitácora
            </a>
            <?php endif; ?>

            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('usuarios.ver')): ?>
            <div class="nav-section-title">Administración</div>
            <a href="<?php echo e(route('admin.usuarios.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.usuarios.*') ? 'active' : ''); ?>">
                <i class="bi bi-people"></i> Usuarios
            </a>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('roles.ver')): ?>
            <a href="<?php echo e(route('admin.roles.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.roles.*') ? 'active' : ''); ?>">
                <i class="bi bi-shield-lock"></i> Roles
            </a>
            <?php endif; ?>
        </nav>
    </aside>
    
    <header class="app-header">
        <div class="header-title"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small"><?php echo e(Auth::user()->name); ?></span>
            <div class="user-avatar"><?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?></div>
            <form action="<?php echo e(route('admin.logout')); ?>" method="POST" class="m-0">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-box-arrow-right"></i> Salir</button>
            </form>
        </div>
    </header>
    
    <main class="app-main">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle"></i> <?php echo e(session('error')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php echo $__env->yieldContent('content'); ?>
    </main>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html><?php /**PATH C:\Users\CONAPDIS\Desktop\conapdis-soporte-main\resources\views/layouts/admin.blade.php ENDPATH**/ ?>