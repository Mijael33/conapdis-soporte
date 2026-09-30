@extends('layouts.admin')
@section('title', 'Bitácora')
@section('page-title', 'Bitácora de Auditoría')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center">
                <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: #dbeafe;">
                    <i class="bi bi-journal-text" style="font-size: 1.2rem; color: #003097;"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold" style="color: #1a3b5d;">Bitácora Global del Sistema</h5>
                    <small class="text-muted">Registro completo de todas las acciones realizadas</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3" style="color: #1a3b5d;">
                <i class="bi bi-funnel me-2"></i>Filtros
            </h6>
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Módulo</label>
                    <select name="modulo" class="form-select rounded-3">
                        <option value="">Todos</option>
                        @foreach($modulos as $mod)
                            <option value="{{ $mod }}" {{ request('modulo') == $mod ? 'selected' : '' }}>
                                {{ ucfirst($mod) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Acción</label>
                    <select name="accion" class="form-select rounded-3">
                        <option value="">Todas</option>
                        @foreach($acciones as $acc)
                            <option value="{{ $acc }}" {{ request('accion') == $acc ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $acc)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Usuario</label>
                    <select name="usuario_id" class="form-select rounded-3">
                        <option value="">Todos</option>
                        @foreach($usuarios as $usr)
                            <option value="{{ $usr->id }}" {{ request('usuario_id') == $usr->id ? 'selected' : '' }}>
                                {{ $usr->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control rounded-3" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control rounded-3" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn-conapdis w-100">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                </div>
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <input type="text" name="search" class="form-control rounded-3" 
                               placeholder="Buscar en descripción, código o usuario..." value="{{ request('search') }}">
                        <a href="{{ route('admin.bitacora.index') }}" class="btn-outline-conapdis">
                            <i class="bi bi-x-circle"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0 fw-bold" style="color: #1a3b5d;">
                Total de registros: <span style="color: #003097;">{{ $bitacoras->total() }}</span>
            </h6>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-header">
                        <tr>
                            <th style="width: 140px;">Fecha y Hora</th>
                            <th style="width: 130px;">Módulo</th>
                            <th style="width: 140px;">Acción</th>
                            <th style="width: 180px;">Usuario</th>
                            <th>Descripción</th>
                            <th class="text-center" style="width: 80px;">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bitacoras as $bit)
                        <tr>
                            <td>
                                <small>
                                    <strong>{{ \App\Helpers\FechaHelper::soloFecha($bit->fecha_registro) }}</strong><br>
                                    {{ \App\Helpers\FechaHelper::horaNormal($bit->fecha_registro) }}
                                </small>
                            </td>
                            <td>
                                <span class="badge rounded-pill" 
                                      style="background: {{ $bit->color_modulo }}20; color: {{ $bit->color_modulo }}; border: 1px solid {{ $bit->color_modulo }}40;">
                                    {{ ucfirst($bit->modulo) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $bit->color_accion }}">
                                    {{ ucfirst(str_replace('_', ' ', $bit->accion)) }}
                                </span>
                            </td>
                            <td>
                                <small>
                                    <strong>{{ $bit->usuario_nombre_snapshot ?? 'Sistema' }}</strong>
                                    @if($bit->ip_address)
                                        <br><span class="text-muted">IP: {{ $bit->ip_address }}</span>
                                    @endif
                                </small>
                            </td>
                            <td>
                                <small>{{ Str::limit($bit->descripcion, 100) }}</small>
                                @if($bit->modelo_codigo)
                                    <br><span class="badge rounded-pill" style="background: #e0e7ff; color: #4f46e5; font-size: 0.65rem;">
                                        {{ $bit->modelo_codigo }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.bitacora.show', $bit) }}" 
                                   class="btn btn-sm btn-outline-conapdis" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-journal" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="mt-2 mb-0">No hay registros en la bitácora</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $bitacoras->links() }}
        </div>
    </div>
</div>
@endsection