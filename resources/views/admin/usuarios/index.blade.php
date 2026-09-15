@extends('layouts.admin')
@section('title', 'Usuarios')
@section('page-title', 'Usuarios del Sistema')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Gestión de Usuarios</h5>
        <a href="{{ route('admin.usuarios.create') }}" class="btn-conapdis"><i class="bi bi-plus-lg"></i> Nuevo Usuario</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr><th>Nombre</th><th>Email</th><th>Roles</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $user)
                    <tr>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge badge-instalado me-1">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('admin.usuarios.edit', $user) }}" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.usuarios.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar usuario?')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $usuarios->links() }}
    </div>
</div>
@endsection