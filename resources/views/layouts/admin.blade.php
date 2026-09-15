<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>@yield('title', 'CONAPDIS') - Sistema de Gestión de Bienes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/conapdis.css') }}" rel="stylesheet">
</head>
<body>
    <aside class="app-sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('images/logos/logo-conapdis.png') }}" alt="CONAPDIS" style="max-width: 120px; height: auto;">
            <h3>Sistema de Gestión de Bienes</h3>
        </div>
        <nav>
            <div class="nav-section-title">Principal</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            {{-- TECNOLOGÍA --}}
            @can('tecnologia.equipos.ver')
            <div class="nav-section-title">Tecnología</div>
            @endcan

            @can('tecnologia.equipos.ver')
            <a href="{{ route('admin.equipos.index') }}" class="nav-link {{ request()->routeIs('admin.equipos.*') ? 'active' : '' }}">
                <i class="bi bi-hdd-stack"></i> Equipos
            </a>
            @endcan

            @can('tecnologia.componentes.ver')
            <a href="{{ route('admin.componentes.index') }}" class="nav-link {{ request()->routeIs('admin.componentes.*') ? 'active' : '' }}">
                <i class="bi bi-memory"></i> Componentes
            </a>
            @endcan

            @can('tecnologia.tipos-equipos.ver')
            <a href="{{ route('admin.tipos-equipos.index') }}" class="nav-link {{ request()->routeIs('admin.tipos-equipos.*') ? 'active' : '' }}">
                <i class="bi bi-pc-display"></i> Tipos de Equipos
            </a>
            @endcan

            @can('tecnologia.categorias-componentes.ver')
            <a href="{{ route('admin.categorias-componentes.index') }}" class="nav-link {{ request()->routeIs('admin.categorias-componentes.*') ? 'active' : '' }}">
                <i class="bi bi-cpu"></i> Categorías Componentes
            </a>
            @endcan

            @can('tecnologia.ordenes.ver')
            <a href="{{ route('admin.ordenes.index') }}" class="nav-link {{ request()->routeIs('admin.ordenes.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i> Órdenes de Servicio
            </a>
            @endcan

            {{-- BIENES NACIONALES --}}
            @can('bienes.ver')
            <div class="nav-section-title">Bienes Nacionales</div>
            <a href="{{ route('admin.bienes.index') }}" class="nav-link {{ request()->routeIs('admin.bienes.*') && !request()->routeIs('admin.bienes-categorias.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Bienes
            </a>
            @endcan
            @can('bienes.categorias.ver')
            <a href="{{ route('admin.bienes-categorias.index') }}" class="nav-link {{ request()->routeIs('admin.bienes-categorias.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> Categorías
            </a>
            @endcan

            {{-- VEHÍCULOS --}}
            @can('vehiculos.ver')
            <div class="nav-section-title">Vehículos</div>
            <a href="{{ route('admin.vehiculos.index') }}" class="nav-link {{ request()->routeIs('admin.vehiculos.*') && !request()->routeIs('admin.vehiculos-categorias.*') ? 'active' : '' }}">
                <i class="bi bi-truck"></i> Vehículos
            </a>
            @endcan
            @can('vehiculos.categorias.ver')
            <a href="{{ route('admin.vehiculos-categorias.index') }}" class="nav-link {{ request()->routeIs('admin.vehiculos-categorias.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> Categorías
            </a>
            @endcan

            {{-- EQUIPOS DE SONIDO --}}
            @can('sonido.ver')
            <div class="nav-section-title">Equipos de Sonido</div>
            <a href="{{ route('admin.sonido.index') }}" class="nav-link {{ request()->routeIs('admin.sonido.*') && !request()->routeIs('admin.sonido-categorias.*') ? 'active' : '' }}">
                <i class="bi bi-soundwave"></i> Equipos
            </a>
            @endcan
            @can('sonido.categorias.ver')
            <a href="{{ route('admin.sonido-categorias.index') }}" class="nav-link {{ request()->routeIs('admin.sonido-categorias.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> Categorías
            </a>
            @endcan

            {{-- ENTRADA/SALIDA --}}
            @can('entrada-salida.ver')
            <div class="nav-section-title">Entrada/Salida</div>
            <a href="{{ route('admin.entrada-salida.index') }}" class="nav-link {{ request()->routeIs('admin.entrada-salida.index') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right"></i> Registros
            </a>
            @endcan
            @can('entrada-salida.crear')
            <a href="{{ route('admin.entrada-salida.create') }}" class="nav-link {{ request()->routeIs('admin.entrada-salida.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i> Nuevo Registro
            </a>
            @endcan

            {{-- BITÁCORA --}}
            @can('bitacora.ver')
            <div class="nav-section-title">Auditoría</div>
            <a href="{{ route('admin.bitacora.index') }}" class="nav-link {{ request()->routeIs('admin.bitacora.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i> Bitácora
            </a>
            @endcan

            {{-- ADMINISTRACIÓN --}}
            @can('usuarios.ver')
            <div class="nav-section-title">Administración</div>
            <a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Usuarios
            </a>
            @endcan
            @can('roles.ver')
            <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock"></i> Roles
            </a>
            @endcan
        </nav>
    </aside>
    
    <header class="app-header">
        <div class="header-title">@yield('page-title', 'Dashboard')</div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">{{ Auth::user()->name }}</span>
            <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-box-arrow-right"></i> Salir</button>
            </form>
        </div>
    </header>
    
    <main class="app-main">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle"></i> {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @yield('content')
    </main>
    
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>