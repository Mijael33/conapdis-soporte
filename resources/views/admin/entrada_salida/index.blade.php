@extends('layouts.admin')
@section('title', 'Entrada/Salida')
@section('page-title', 'Registros de Entrada/Salida')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-bold">Movimientos de Bienes</h5>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.entrada-salida.exportar-excel') }}" class="btn-outline-conapdis btn-sm"><i class="bi bi-file-excel"></i> Exportar Excel</a>
            <a href="{{ route('admin.entrada-salida.pdf-listado') }}" class="btn-outline-conapdis btn-sm"><i class="bi bi-file-pdf"></i> Exportar PDF</a>
            <a href="{{ route('admin.entrada-salida.create') }}" class="btn-conapdis btn-sm"><i class="bi bi-plus-lg"></i> Nuevo</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-2">
                <select name="tipo" class="form-select">
                    <option value="">Todos los tipos</option>
                    <option value="Salida" {{ request('tipo')=='Salida' ? 'selected' : '' }}>Salida</option>
                    <option value="Entrada" {{ request('tipo')=='Entrada' ? 'selected' : '' }}>Entrada</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="bien_tipo" class="form-select">
                    <option value="">Todos los bienes</option>
                    <option value="Tecnologia" {{ request('bien_tipo')=='Tecnologia' ? 'selected' : '' }}>Tecnología</option>
                    <option value="BienNacional" {{ request('bien_tipo')=='BienNacional' ? 'selected' : '' }}>Bien Nacional</option>
                    <option value="Vehiculo" {{ request('bien_tipo')=='Vehiculo' ? 'selected' : '' }}>Vehículo</option>
                    <option value="Sonido" {{ request('bien_tipo')=='Sonido' ? 'selected' : '' }}>Sonido</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-conapdis"><i class="bi bi-search"></i> Filtrar</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr>
                        <th>N°</th><th>Tipo</th><th>Bien</th><th>Descripción</th>
                        <th>Fecha</th><th>Retirado por</th><th>Seguridad</th>
                        <th class="text-center" style="width: 130px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registros as $reg)
                    <tr>
                        <td class="fw-semibold">{{ str_pad($reg->id, 6, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            @if($reg->tipo=='Salida')<span class="badge badge-revision">Salida</span>
                            @else<span class="badge badge-disponible">Entrada</span>@endif
                        </td>
                        <td><span class="badge badge-instalado">{{ $reg->bien_tipo }}</span></td>
                        <td>{{ Str::limit($reg->descripcion_bien, 30) }}</td>
                        <td>
                            @if($reg->tipo=='Salida')
                                {{ $reg->fecha_hora_salida ? $reg->fecha_hora_salida->format('d/m/Y H:i') : 'N/A' }}
                            @else
                                {{ $reg->fecha_hora_entrada ? $reg->fecha_hora_entrada->format('d/m/Y H:i') : 'N/A' }}
                            @endif
                        </td>
                        <td>{{ $reg->persona_retira_nombre ?? 'N/A' }}</td>
                        <td>
                            @if($reg->tipo=='Salida')
                                {{ $reg->seguridad_salida_nombre ?? 'N/A' }}
                            @else
                                {{ $reg->seguridad_entrada_nombre ?? 'N/A' }}
                            @endif
                        </td>
                        <td class="text-center" style="white-space: nowrap;">
                            <a href="{{ route('admin.entrada-salida.show', $reg) }}" class="btn btn-sm btn-outline-conapdis me-1" title="Ver"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.entrada-salida.pdf', $reg) }}" class="btn btn-sm btn-outline-conapdis me-1" title="PDF" target="_blank"><i class="bi bi-file-pdf"></i></a>
                            <a href="{{ route('admin.entrada-salida.edit', $reg) }}" class="btn btn-sm btn-outline-conapdis me-1" title="Editar"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.entrada-salida.destroy', $reg) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar el registro N° {{ str_pad($reg->id, 6, '0', STR_PAD_LEFT) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Sin registros</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $registros->links() }}
    </div>
</div>
@endsection