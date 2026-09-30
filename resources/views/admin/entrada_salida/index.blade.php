@extends('layouts.admin')
@section('title', 'Entrada/Salida')
@section('page-title', 'Movimientos de Entrada/Salida')

@section('content')
<div class="container-fluid">

    {{-- KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3" style="border-left: 4px solid #f59e0b;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #fef3c7;">
                        <i class="bi bi-hourglass-split" style="font-size: 1.2rem; color: #f59e0b;"></i>
                    </div>
                    <div>
                        <h4 style="font-weight: 700; color: #1f2937; margin: 0; font-size: 1.2rem;">{{ $pendientes }}</h4>
                        <small style="color: #6b7280; font-size: 0.8rem;">Salidas Pendientes de Entrada</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3" style="border-left: 4px solid #16a34a;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #dcfce7;">
                        <i class="bi bi-check-circle-fill" style="font-size: 1.2rem; color: #16a34a;"></i>
                    </div>
                    <div>
                        <h4 style="font-weight: 700; color: #1f2937; margin: 0; font-size: 1.2rem;">{{ $completados }}</h4>
                        <small style="color: #6b7280; font-size: 0.8rem;">Movimientos Completados</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
            <h5 class="mb-0 fw-bold" style="color: #1a3b5d;">
                <i class="bi bi-arrow-left-right me-2"></i>Movimientos Registrados
            </h5>
            <div class="d-flex gap-2 flex-wrap">
                @can('entrada-salida.exportar')
                <a href="{{ route('admin.entrada-salida.exportar-excel') }}" class="btn-outline-conapdis btn-sm">
                    <i class="bi bi-file-excel"></i> Excel
                </a>
                @endcan
                @can('entrada-salida.ver')
                <a href="{{ route('admin.entrada-salida.pdf-listado') }}" class="btn-outline-conapdis btn-sm" target="_blank">
                    <i class="bi bi-file-pdf"></i> PDF
                </a>
                @endcan
                @can('entrada-salida.crear')
                <a href="{{ route('admin.entrada-salida.create') }}" class="btn-conapdis btn-sm">
                    <i class="bi bi-plus-lg"></i> Registrar Salida
                </a>
                @endcan
            </div>
        </div>

        <div class="card-body p-4">
            {{-- Filtros --}}
            <form method="GET" class="row g-2 mb-4">
                <div class="col-md-3">
                    <select name="estatus" class="form-select rounded-3">
                        <option value="">Todos los estatus</option>
                        <option value="Pendiente" {{ request('estatus') == 'Pendiente' ? 'selected' : '' }}>Pendientes</option>
                        <option value="Completado" {{ request('estatus') == 'Completado' ? 'selected' : '' }}>Completados</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="bien_tipo" class="form-select rounded-3">
                        <option value="">Todos los bienes</option>
                        <option value="Tecnologia" {{ request('bien_tipo') == 'Tecnologia' ? 'selected' : '' }}>Tecnología</option>
                        <option value="BienNacional" {{ request('bien_tipo') == 'BienNacional' ? 'selected' : '' }}>Bien Nacional</option>
                        <option value="Vehiculo" {{ request('bien_tipo') == 'Vehiculo' ? 'selected' : '' }}>Vehículo</option>
                        <option value="Sonido" {{ request('bien_tipo') == 'Sonido' ? 'selected' : '' }}>Sonido</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control rounded-3" 
                           placeholder="Buscar código, descripción..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn-conapdis w-100">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                </div>
            </form>

            {{-- Tabla --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-header">
                        <tr>
                            <th>N°</th>
                            <th>Bien</th>
                            <th>Sede</th>
                            <th>Salida</th>
                            <th>Entrada</th>
                            <th class="text-center">Estatus</th>
                            <th class="text-center" style="width: 240px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registros as $reg)
                        <tr>
                            <td class="fw-semibold">{{ $reg->numero_comprobante }}</td>
                            <td>
                                <div class="fw-semibold">{{ $reg->bien_codigo }}</div>
                                <small class="text-muted">{{ Str::limit($reg->bien_descripcion, 30) }}</small>
                                <br>
                                <span class="badge rounded-pill" style="background: #e0e7ff; color: #4f46e5; font-size: 0.65rem;">
                                    {{ $reg->bien_tipo }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $reg->sede->nombre_sede ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <small>
                                    <strong>{{ \App\Helpers\FechaHelper::soloFecha($reg->fecha_hora_salida) }}</strong><br>
                                    {{ \App\Helpers\FechaHelper::horaNormal($reg->fecha_hora_salida) }}<br>
                                    Retira: {{ $reg->salida_retira_nombre }}
                                </small>
                            </td>
                            <td>
                                @if($reg->fecha_hora_entrada)
                                    <small>
                                        <strong>{{ \App\Helpers\FechaHelper::soloFecha($reg->fecha_hora_entrada) }}</strong><br>
                                        {{ \App\Helpers\FechaHelper::horaNormal($reg->fecha_hora_entrada) }}<br>
                                        Entrega: {{ $reg->entrada_recibe_nombre }}
                                    </small>
                                @else
                                    <small class="text-muted">Pendiente</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($reg->estatus === 'Pendiente')
                                    <span class="badge rounded-pill" style="background: #fef3c7; color: #a16207;">
                                        Pendiente
                                    </span>
                                @else
                                    <span class="badge rounded-pill" style="background: #dcfce7; color: #166534;">
                                        Completado
                                    </span>
                                @endif
                            </td>
                            <td class="text-center" style="white-space: nowrap;">
                                {{-- VER --}}
                                @can('entrada-salida.ver')
                                <a href="{{ route('admin.entrada-salida.show', $reg) }}" 
                                   class="btn btn-sm btn-outline-conapdis me-1" title="Ver detalles">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @endcan

                                @if($reg->estatus === 'Pendiente')
                                    {{-- REGISTRAR ENTRADA (permiso CREAR) --}}
                                    @can('entrada-salida.crear')
                                    <a href="{{ route('admin.entrada-salida.registrar-entrada', $reg) }}" 
                                       class="btn btn-sm btn-outline-success me-1" title="Registrar Entrada">
                                        <i class="bi bi-box-arrow-in-down"></i>
                                    </a>
                                    @endcan

                                    {{-- EDITAR SALIDA (permiso EDITAR) --}}
                                    @can('entrada-salida.editar')
                                    <a href="{{ route('admin.entrada-salida.edit', $reg) }}" 
                                       class="btn btn-sm btn-outline-conapdis me-1" title="Editar Salida">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                @endif

                                {{-- PDF SALIDA --}}
                                @can('entrada-salida.ver')
                                <a href="{{ route('admin.entrada-salida.pdf-salida', $reg) }}" 
                                   class="btn btn-sm btn-outline-conapdis me-1" title="PDF Salida" target="_blank">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                                @endcan

                                {{-- PDF ENTRADA (solo si está completado) --}}
                                @if($reg->estatus === 'Completado')
                                    @can('entrada-salida.ver')
                                    <a href="{{ route('admin.entrada-salida.pdf-entrada', $reg) }}" 
                                       class="btn btn-sm btn-outline-conapdis me-1" title="PDF Entrada" target="_blank">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                    @endcan
                                @endif

                                {{-- ELIMINAR --}}
                                @can('entrada-salida.eliminar')
                                <form action="{{ route('admin.entrada-salida.destroy', $reg) }}" method="POST" 
                                      class="d-inline" 
                                      onsubmit="return confirm('¿Está seguro de eliminar el movimiento {{ $reg->numero_comprobante }}?\n\nEsta acción quedará registrada en la bitácora.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="mt-2 mb-0">No hay movimientos registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $registros->links() }}
        </div>
    </div>
</div>
@endsection