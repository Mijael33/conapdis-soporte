@extends('layouts.admin')
@section('title', 'Detalle Estado')
@section('page-title', 'Detalle del Estado')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0 fw-bold" style="color: #1a3b5d;">
                    <i class="bi bi-geo-alt me-2"></i>{{ $estado->nombre }}
                </h5>
                <small class="text-muted">Región: {{ $estado->region }}</small>
            </div>
            <div class="d-flex gap-2">
                @can('estados.editar')
                <a href="{{ route('admin.estados.edit', $estado) }}" class="btn-conapdis btn-sm">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                @endcan
                <a href="{{ route('admin.estados.index') }}" class="btn-outline-conapdis btn-sm">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3" style="color: #003097;">
                <i class="bi bi-building me-2"></i>Sedes Registradas ({{ $estado->sedes->count() }})
            </h6>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-header">
                        <tr>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Código Postal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estado->sedes as $sede)
                        <tr>
                            <td class="fw-semibold">{{ $sede->nombre_sede }}</td>
                            <td>{{ $sede->direccion }}</td>
                            <td>{{ $sede->codigo_postal ?? 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                Este estado no tiene sedes registradas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection