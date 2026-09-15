@extends('layouts.admin')
@section('title', 'Roles')
@section('page-title', 'Roles y Permisos')
@section('content')
<div class="container-fluid">
    <h2 class="mb-4" style="color: #1a3b5d; font-weight: 700;">Gestión de Roles</h2>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3 px-4 rounded-top-4">
            <div>
                <h5 class="mb-0 fw-bold" style="color: #1a3b5d;">
                    <i class="bi bi-shield-lock me-2"></i>Listado de Roles
                </h5>
                <small class="text-muted">Administra los roles y sus permisos</small>
            </div>
            <a href="{{ route('admin.roles.create') }}" class="btn-conapdis">
                <i class="bi bi-plus-lg"></i> Nuevo Rol
            </a>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-header">
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Rol</th>
                            <th>Descripción de Permisos</th>
                            <th style="width: 120px;" class="text-center">Usuarios</th>
                            <th style="width: 150px;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td class="fw-semibold text-muted">#{{ $role->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: {{ $role->name === 'Administrador' ? '#fef3c7' : '#dbeafe' }};">
                                        <i class="bi {{ $role->name === 'Administrador' ? 'bi-crown-fill text-warning' : 'bi-shield-fill text-primary' }}"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="color: #001e5c;">{{ $role->name }}</div>
                                        <small class="text-muted">{{ $role->guard_name }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($role->name === 'Administrador')
                                    <span class="badge rounded-pill" style="background: #fef3c7; color: #a16207;">
                                        <i class="bi bi-crown-fill me-1"></i>Acceso Total
                                    </span>
                                @else
                                    @php
                                        $porModulo = $role->permissions->groupBy('modulo');
                                    @endphp
                                    @forelse($porModulo as $modulo => $perms)
                                        <span class="badge rounded-pill me-1 mb-1" style="background: #dbeafe; color: #003097;">
                                            {{ ucfirst(str_replace('_', ' ', $modulo)) }}
                                            <span class="ms-1 opacity-75">({{ $perms->count() }})</span>
                                        </span>
                                    @empty
                                        <span class="text-muted">Sin permisos asignados</span>
                                    @endforelse
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill" style="background: #e0e7ff; color: #4f46e5; font-size: 0.85rem;">
                                    <i class="bi bi-people-fill me-1"></i>{{ $role->users()->count() }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-conapdis" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($role->name !== 'Administrador')
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar el rol {{ $role->name }}?')" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Rol protegido">
                                        <i class="bi bi-lock-fill"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="mt-2 mb-0">No hay roles registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $roles->links() }}
        </div>
    </div>
</div>
@endsection