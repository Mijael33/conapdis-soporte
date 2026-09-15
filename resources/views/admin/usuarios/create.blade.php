@extends('layouts.admin')
@section('title', 'Nuevo Usuario')
@section('page-title', 'Nuevo Usuario')
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Crear Usuario</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.usuarios.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Estado</label>
                    <select name="estado_id" class="form-select">
                        <option value="">Seleccione</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id }}" {{ old('estado_id') == $estado->id ? 'selected' : '' }}>{{ $estado->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Sede</label>
                    <select name="sede_id" class="form-select">
                        <option value="">Seleccione</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ old('sede_id') == $sede->id ? 'selected' : '' }}>{{ $sede->nombre_sede }} ({{ $sede->estado->nombre }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Roles</label>
                <div class="row">
                    @foreach($roles as $role)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="form-check-input" id="role{{ $role->id }}">
                                <label class="form-check-label" for="role{{ $role->id }}">{{ $role->name }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="btn-conapdis">Guardar</button>
            <a href="{{ route('admin.usuarios.index') }}" class="btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>
@endsection